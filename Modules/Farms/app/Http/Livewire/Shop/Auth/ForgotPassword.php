<?php

namespace Modules\Farms\Http\Livewire\Shop\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';

    public ?string $status = null;

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function sendLink(): void
    {
        $this->validate();

        // Point the built-in ResetPassword notification at our custom reset URL
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return route('farm-shop.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });

        $status = Password::broker('shop_customers')->sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->status = 'We have emailed you a password reset link!';
            $this->email  = '';
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('farms::livewire.shop.auth.forgot-password')
            ->layout('farms::layouts.public', ['title' => 'Forgot Password']);
    }
}
