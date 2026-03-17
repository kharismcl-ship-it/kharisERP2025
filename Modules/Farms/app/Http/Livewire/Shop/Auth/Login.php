<?php

namespace Modules\Farms\Http\Livewire\Shop\Auth;

use Livewire\Component;

class Login extends Component
{
    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;

    protected function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function login()
    {
        $this->validate();

        if (auth('shop_customer')->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->flash('success', 'Welcome back, ' . auth('shop_customer')->user()->name . '!');
            return $this->redirect(session()->pull('farm_shop_intended', route('farm-shop.index')));
        }

        $this->addError('email', 'These credentials do not match our records.');
    }

    public function logout()
    {
        auth('shop_customer')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return $this->redirect(route('farm-shop.index'));
    }

    public function render()
    {
        return view('farms::livewire.shop.auth.login')
            ->layout('farms::layouts.public', ['title' => 'Sign In']);
    }
}
