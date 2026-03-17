<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Modules\Farms\Models\FarmB2bAccount;
use Modules\Farms\Models\ShopCustomer;
use Modules\Farms\Services\ShopSettingsService;

class B2bRegister extends Component
{
    // Personal (login) fields
    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Business fields
    public string $business_name    = '';
    public string $business_type    = 'restaurant';
    public string $contact_name     = '';
    public string $contact_phone    = '';
    public string $contact_email    = '';
    public string $business_address = '';
    public string $tax_id           = '';

    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'min:2', 'max:100'],
            'email'                 => ['required', 'email', 'max:150', 'unique:shop_customers,email'],
            'phone'                 => ['required', 'string', 'min:9', 'max:20'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'business_name'         => ['required', 'string', 'max:255'],
            'business_type'         => ['required', 'in:' . implode(',', FarmB2bAccount::TYPES)],
            'contact_name'          => ['required', 'string', 'max:255'],
            'contact_phone'         => ['required', 'string', 'max:30'],
            'contact_email'         => ['nullable', 'email', 'max:150'],
            'business_address'      => ['nullable', 'string', 'max:500'],
            'tax_id'                => ['nullable', 'string', 'max:50'],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $settings  = app(ShopSettingsService::class)->forCurrentDomain();
        $companyId = $settings?->company_id;

        if (! $companyId) {
            $this->addError('business_name', 'Shop not found. Please try again.');
            return;
        }

        // Create B2B account (pending approval)
        $b2bAccount = FarmB2bAccount::create([
            'company_id'      => $companyId,
            'business_name'   => $this->business_name,
            'business_type'   => $this->business_type,
            'contact_name'    => $this->contact_name,
            'contact_phone'   => $this->contact_phone,
            'contact_email'   => $this->contact_email ?: null,
            'business_address'=> $this->business_address ?: null,
            'tax_id'          => $this->tax_id ?: null,
            'status'          => 'pending',
            'payment_terms'   => 'prepay',
        ]);

        // Create ShopCustomer linked to this account (not yet is_b2b until approved)
        ShopCustomer::create([
            'name'           => $this->name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'password'       => Hash::make($this->password),
            'is_b2b'         => false, // admin approves → triggers is_b2b=true
            'b2b_account_id' => $b2bAccount->id,
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        return view('farms::livewire.shop.b2b-register')
            ->layout('farms::layouts.public', ['title' => 'Wholesale Account Application']);
    }
}
