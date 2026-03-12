<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Auth;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    protected array $rules = [
        'email'    => 'required|email',
        'password' => 'required',
    ];

    public function login(): void
    {
        $this->validate();

        $key = 'hostel_occupant_login:' . Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('email', "Too many login attempts. Please try again in {$seconds} seconds.");
            return;
        }

        if (Auth::guard('hostel_occupant')->attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember
        )) {
            RateLimiter::clear($key);
            session()->regenerate();
            $this->redirect(route('hostel_occupant.dashboard'), navigate: false);
            return;
        }

        RateLimiter::hit($key, 60);
        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.auth.login')
            ->layout('hostels::layouts.guest');
    }
}
