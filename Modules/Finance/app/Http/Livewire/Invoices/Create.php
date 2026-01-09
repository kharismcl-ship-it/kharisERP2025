<?php

namespace Modules\Finance\Http\Livewire\Invoices;

use Livewire\Component;
use Modules\Finance\App\Models\Invoice;
use Modules\Finance\App\Models\InvoiceLine;

class Create extends Component
{
    public $customer_name;

    public $invoice_date;

    public $due_date;

    public $lines = [
        ['description' => '', 'quantity' => 1, 'unit_price' => 0, 'line_total' => 0],
    ];

    protected $rules = [
        'customer_name' => 'required|string|max:255',
        'invoice_date' => 'required|date',
        'due_date' => 'nullable|date',
        'lines.*.description' => 'required|string',
        'lines.*.quantity' => 'required|numeric|min:0',
        'lines.*.unit_price' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->invoice_date = now()->format('Y-m-d');
    }

    public function addLine()
    {
        $this->lines[] = ['description' => '', 'quantity' => 1, 'unit_price' => 0, 'line_total' => 0];
    }

    public function removeLine($index)
    {
        if (count($this->lines) > 1) {
            unset($this->lines[$index]);
            $this->lines = array_values($this->lines);
        }
    }

    public function updatedLines($value, $key)
    {
        // Calculate line totals when quantity or unit_price changes
        preg_match('/lines\.(\d+)\.(.+)/', $key, $matches);
        if (count($matches) == 3) {
            $index = $matches[1];
            $field = $matches[2];

            if (in_array($field, ['quantity', 'unit_price']) && isset($this->lines[$index])) {
                $this->lines[$index]['line_total'] =
                    $this->lines[$index]['quantity'] * $this->lines[$index]['unit_price'];
            }
        }
    }

    public function createInvoice()
    {
        $this->validate();

        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        if (! $companyId) {
            $this->dispatch('notification', ['message' => 'No company selected', 'type' => 'error']);

            return;
        }

        // Calculate totals
        $subTotal = collect($this->lines)->sum('line_total');
        $taxTotal = 0; // For simplicity, no tax calculation in this example
        $total = $subTotal + $taxTotal;

        // Create the invoice
        $invoice = Invoice::create([
            'company_id' => $companyId,
            'customer_name' => $this->customer_name,
            'invoice_number' => 'INV-'.now()->format('Y').'-'.strtoupper(uniqid()),
            'invoice_date' => $this->invoice_date,
            'due_date' => $this->due_date,
            'status' => 'draft',
            'sub_total' => $subTotal,
            'tax_total' => $taxTotal,
            'total' => $total,
        ]);

        // Create invoice lines
        foreach ($this->lines as $line) {
            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'description' => $line['description'],
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'line_total' => $line['line_total'],
            ]);
        }

        $this->dispatch('notification', ['message' => 'Invoice created successfully', 'type' => 'success']);

        return redirect()->route('finance.invoices.index');
    }

    public function render()
    {
        return view('finance::livewire.invoices.create')->layout('layouts.app');
    }
}
