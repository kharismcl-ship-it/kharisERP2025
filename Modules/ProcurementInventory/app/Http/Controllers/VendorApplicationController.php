<?php

namespace Modules\ProcurementInventory\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\ProcurementInventory\Models\ItemCategory;
use Modules\ProcurementInventory\Models\VendorApplication;

class VendorApplicationController extends Controller
{
    public function create(string $company)
    {
        $companyModel = Company::where('slug', $company)->firstOrFail();
        $categories   = ItemCategory::where('company_id', $companyModel->id)->orderBy('name')->get();

        return view('procurementinventory::vendor-portal.apply', compact('companyModel', 'categories'));
    }

    public function store(string $company, Request $request)
    {
        $companyModel = Company::where('slug', $company)->firstOrFail();

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'trading_name'        => ['nullable', 'string', 'max:255'],
            'email'               => ['required', 'email', 'max:255'],
            'phone'               => ['nullable', 'string', 'max:50'],
            'address'             => ['nullable', 'string'],
            'city'                => ['nullable', 'string', 'max:100'],
            'country'             => ['nullable', 'string', 'max:100'],
            'contact_person'      => ['nullable', 'string', 'max:255'],
            'contact_phone'       => ['nullable', 'string', 'max:50'],
            'tax_number'          => ['nullable', 'string', 'max:100'],
            'bank_name'           => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
            'bank_branch'         => ['nullable', 'string', 'max:255'],
            'business_type'       => ['nullable', 'string'],
            'years_in_business'   => ['nullable', 'integer', 'min:0', 'max:200'],
            'annual_revenue_band' => ['nullable', 'string'],
            'categories_supplied' => ['nullable', 'array'],
            'categories_supplied.*' => ['string'],
        ]);

        $application = VendorApplication::create(array_merge($validated, [
            'company_id' => $companyModel->id,
            'status'     => 'submitted',
            'currency'   => 'GHS',
        ]));

        // Send confirmation email via CommunicationCentre if available
        $this->sendConfirmation($application);

        return redirect()->route('vendor.apply.success');
    }

    public function success()
    {
        return view('procurementinventory::vendor-portal.apply-success');
    }

    private function sendConfirmation(VendorApplication $application): void
    {
        if (! class_exists(\Modules\CommunicationCentre\Services\CommunicationService::class)) {
            return;
        }

        try {
            $service = app(\Modules\CommunicationCentre\Services\CommunicationService::class);
            $service->sendRawEmail(
                $application->email,
                'Vendor Application Received — ' . ($application->company?->name ?? 'Our Company'),
                "Dear {$application->name},\n\nThank you for submitting your vendor application. We have received your details and will review your application shortly.\n\nApplication Reference: #" . $application->id . "\n\nWe will be in touch within 5-7 business days.\n\nKind regards,\nThe Procurement Team"
            );
        } catch (\Throwable $e) {
            Log::warning("VendorApplication confirmation email failed for #{$application->id}: " . $e->getMessage());
        }
    }
}