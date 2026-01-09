<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email;

    public $password;

    public $remember;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function __invoke()
    {
        return $this->render();
    }

    public function login()
    {
        $this->validate();

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::guard('hostel_occupant')->attempt($credentials, $this->remember)) {
            return redirect()->route('hostel_occupant.dashboard');
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.auth.login')
            ->layout('hostels::layouts.guest');
    }
}
