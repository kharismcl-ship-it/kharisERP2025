<?php

namespace Modules\Farms\Http\Livewire\Requests;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmRequest;
use Modules\Farms\Models\FarmRequestItem;

class Create extends Component
{
    public Farm $farm;

    public string $requestType = 'materials';

    public string $title = '';

    public string $description = '';

    public string $urgency = 'medium';

    public array $items = [
        ['description' => '', 'quantity' => 1, 'unit' => '', 'unit_cost' => 0],
    ];

    public function mount(Farm $farm): void
    {
        $this->farm = $farm;
    }

    public function addItem(): void
    {
        $this->items[] = ['description' => '', 'quantity' => 1, 'unit' => '', 'unit_cost' => 0];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            array_splice($this->items, $index, 1);
        }
    }

    protected function rules(): array
    {
        return [
            'title'                    => 'required|string|max:255',
            'requestType'              => 'required|string',
            'urgency'                  => 'required|in:low,medium,high,urgent',
            'description'              => 'nullable|string',
            'items.*.description'      => 'required|string',
            'items.*.quantity'         => 'required|numeric|min:0.01',
        ];
    }

    public function submitRequest(): void
    {
        $this->validate();

        $request = FarmRequest::create([
            'farm_id'      => $this->farm->id,
            'company_id'   => $this->farm->company_id,
            'requested_by' => Auth::id(),
            'request_type' => $this->requestType,
            'title'        => $this->title,
            'description'  => $this->description ?: null,
            'urgency'      => $this->urgency,
            'status'       => 'pending',
        ]);

        foreach ($this->items as $item) {
            FarmRequestItem::create([
                'farm_request_id' => $request->id,
                'description'     => $item['description'],
                'quantity'        => $item['quantity'],
                'unit'            => $item['unit'] ?: null,
                'unit_cost'       => $item['unit_cost'] ?: null,
            ]);
        }

        session()->flash('success', "Request {$request->reference} submitted.");
        $this->redirect(route('farms.requests.index', $this->farm->slug));
    }

    public function render()
    {
        return view('farms::livewire.requests.create')
            ->layout('farms::layouts.app');
    }
}
