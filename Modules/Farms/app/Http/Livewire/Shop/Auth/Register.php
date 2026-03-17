<?php

namespace Modules\Farms\Http\Livewire\Shop\Auth;

use Livewire\Component;
use Modules\Farms\Models\FarmReferral;
use Modules\Farms\Models\ShopCustomer;

class Register extends Component
{
    public string $name            = '';
    public string $email           = '';
    public string $phone           = '';
    public string $password        = '';
    public string $password_confirmation = '';
    public string $referralCode    = '';

    public function mount(): void
    {
        // Pre-fill referral code from URL param or session
        if (request()->has('ref')) {
            session(['farm_shop_ref' => request()->query('ref')]);
        }
        $this->referralCode = session('farm_shop_ref', '');
    }

    protected function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'min:2', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:shop_customers,email'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    protected $messages = [
        'email.unique' => 'An account with this email already exists.',
    ];

    public function register()
    {
        $this->validate();

        $customer = ShopCustomer::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'phone'    => $this->phone ?: null,
            'password' => $this->password,
        ]);

        // Generate referral code for new customer
        $customer->ensureReferralCode();

        // Link referral if a valid code was provided
        $refCode = trim($this->referralCode);
        if ($refCode) {
            $referrer = ShopCustomer::where('referral_code', strtoupper($refCode))->first();
            if ($referrer && $referrer->id !== $customer->id) {
                // Get company from any of the referrer's orders (best-effort)
                $companyId = \Modules\Farms\Models\FarmOrder::where('shop_customer_id', $referrer->id)
                    ->value('company_id');
                if ($companyId) {
                    FarmReferral::firstOrCreate([
                        'referrer_id' => $referrer->id,
                        'referred_id' => $customer->id,
                    ], [
                        'company_id' => $companyId,
                    ]);
                }
            }
        }
        session()->forget('farm_shop_ref');

        auth('shop_customer')->login($customer, true);

        session()->flash('success', 'Account created! Welcome, ' . $customer->name . '.');
        return $this->redirect(route('farm-shop.index'));
    }

    public function render()
    {
        return view('farms::livewire.shop.auth.register')
            ->layout('farms::layouts.public', ['title' => 'Create Account']);
    }
}
