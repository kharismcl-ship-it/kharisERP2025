<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;
use Modules\Hostels\Models\HostelOccupantUser;

class ResetPassword extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    protected array $rules = [
        'token'                 => 'required',
        'email'                 => 'required|email',
        'password'              => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required',
    ];

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate();

        $status = Password::broker('hostel_occupants')->reset(
            [
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token'                 => $this->token,
            ],
            function (HostelOccupantUser $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', 'Your password has been reset. You can now sign in.');
            $this->redirect(route('hostel_occupant.login'), navigate: false);
            return;
        }

        $this->addError('email', __($status));
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.auth.reset-password')
            ->layout('hostels::layouts.guest');
    }
}
