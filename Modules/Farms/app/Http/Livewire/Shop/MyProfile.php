<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Modules\Farms\Models\FarmReferral;
use Modules\Farms\Models\FarmSavedAddress;
use Modules\Farms\Models\ShopCustomer;

class MyProfile extends Component
{
    public string $name             = '';
    public string $phone            = '';
    public string $defaultAddress   = '';
    public string $defaultLandmark  = '';

    // Password change
    public string $currentPassword     = '';
    public string $newPassword         = '';
    public string $newPasswordConfirm  = '';

    // Saved addresses form
    public bool   $showAddressForm   = false;
    public string $addrLabel         = 'Home';
    public string $addrAddress       = '';
    public string $addrLandmark      = '';
    public bool   $addrIsDefault     = false;

    public function mount(): void
    {
        if (! auth('shop_customer')->check()) {
            session(['farm_shop_intended' => url()->current()]);
            $this->redirect(route('farm-shop.login'));
            return;
        }

        /** @var ShopCustomer $customer */
        $customer = auth('shop_customer')->user();
        $this->name            = $customer->name;
        $this->phone           = $customer->phone ?? '';
        $this->defaultAddress  = $customer->default_address ?? '';
        $this->defaultLandmark = $customer->default_landmark ?? '';
    }

    public function saveProfile(): void
    {
        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'defaultAddress'  => ['nullable', 'string', 'max:500'],
            'defaultLandmark' => ['nullable', 'string', 'max:255'],
        ]);

        /** @var ShopCustomer $customer */
        $customer = auth('shop_customer')->user();
        $customer->update([
            'name'             => $this->name,
            'phone'            => $this->phone,
            'default_address'  => $this->defaultAddress,
            'default_landmark' => $this->defaultLandmark,
        ]);

        session()->flash('profile_success', 'Profile updated successfully.');
    }

    public function changePassword(): void
    {
        $this->validate([
            'currentPassword'    => ['required', 'string'],
            'newPassword'        => ['required', 'string', 'min:8', 'same:newPasswordConfirm'],
            'newPasswordConfirm' => ['required', 'string'],
        ]);

        /** @var ShopCustomer $customer */
        $customer = auth('shop_customer')->user();

        if (! Hash::check($this->currentPassword, $customer->password)) {
            $this->addError('currentPassword', 'Current password is incorrect.');
            return;
        }

        $customer->update(['password' => Hash::make($this->newPassword)]);

        $this->currentPassword    = '';
        $this->newPassword        = '';
        $this->newPasswordConfirm = '';

        session()->flash('password_success', 'Password changed successfully.');
    }

    public function addAddress(): void
    {
        $this->validate([
            'addrLabel'     => ['required', 'string', 'max:50'],
            'addrAddress'   => ['required', 'string', 'max:500'],
            'addrLandmark'  => ['nullable', 'string', 'max:255'],
        ]);

        $customer = auth('shop_customer')->user();

        if ($this->addrIsDefault) {
            FarmSavedAddress::where('shop_customer_id', $customer->id)
                ->update(['is_default' => false]);
        }

        FarmSavedAddress::create([
            'shop_customer_id' => $customer->id,
            'label'      => $this->addrLabel,
            'address'    => $this->addrAddress,
            'landmark'   => $this->addrLandmark ?: null,
            'is_default' => $this->addrIsDefault,
        ]);

        $this->addrLabel     = 'Home';
        $this->addrAddress   = '';
        $this->addrLandmark  = '';
        $this->addrIsDefault = false;
        $this->showAddressForm = false;
        session()->flash('address_success', 'Address saved.');
    }

    public function deleteAddress(int $id): void
    {
        $customer = auth('shop_customer')->user();
        FarmSavedAddress::where('id', $id)
            ->where('shop_customer_id', $customer->id)
            ->delete();
    }

    public function setDefaultAddress(int $id): void
    {
        $customer = auth('shop_customer')->user();
        FarmSavedAddress::where('shop_customer_id', $customer->id)->update(['is_default' => false]);
        FarmSavedAddress::where('id', $id)
            ->where('shop_customer_id', $customer->id)
            ->update(['is_default' => true]);
    }

    public function render()
    {
        $customer   = auth('shop_customer')->user();
        $addresses  = $customer ? FarmSavedAddress::where('shop_customer_id', $customer->id)
            ->orderByDesc('is_default')->orderBy('id')->get() : collect();
        $referralCode     = $customer?->ensureReferralCode();
        $referralCount    = $customer ? FarmReferral::where('referrer_id', $customer->id)->count() : 0;
        $referralCredited = $customer ? FarmReferral::where('referrer_id', $customer->id)->whereNotNull('credited_at')->count() : 0;

        return view('farms::livewire.shop.my-profile', compact('addresses', 'referralCode', 'referralCount', 'referralCredited'))
            ->layout('farms::layouts.public', ['title' => 'My Account']);
    }
}
