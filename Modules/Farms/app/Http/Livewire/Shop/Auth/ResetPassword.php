<?php

namespace Modules\Farms\Http\Livewire\Shop\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;
use Modules\Farms\Models\ShopCustomer;

class ResetPassword extends Component
{
    public string $token    = '';
    public string $email    = '';
    public string $password = '';
    public string $passwordConfirmation = '';

    protected function rules(): array
    {
        return [
            'token'               => ['required'],
            'email'               => ['required', 'email'],
            'password'            => ['required', 'string', 'min:8', 'same:passwordConfirmation'],
            'passwordConfirmation' => ['required', 'string'],
        ];
    }

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate();

        $status = Password::broker('shop_customers')->reset(
            [
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->passwordConfirmation,
                'token'                 => $this->token,
            ],
            function (ShopCustomer $customer, string $password) {
                $customer->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                auth('shop_customer')->login($customer);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('success', 'Password reset successfully. You are now logged in.');
            $this->redirect(route('farm-shop.my-orders'));
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('farms::livewire.shop.auth.reset-password')
            ->layout('farms::layouts.public', ['title' => 'Reset Password']);
    }
}
