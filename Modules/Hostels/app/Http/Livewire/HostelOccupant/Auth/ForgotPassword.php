<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';

    public bool $sent = false;

    protected array $rules = [
        'email' => 'required|email',
    ];

    public function sendLink(): void
    {
        $this->validate();

        $status = Password::broker('hostel_occupants')->sendResetLink(
            ['email' => $this->email]
        );

        // Always show the same "sent" message regardless of whether the email exists
        // to prevent user enumeration.
        $this->sent  = true;
        $this->email = '';
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.auth.forgot-password')
            ->layout('hostels::layouts.guest');
    }
}
