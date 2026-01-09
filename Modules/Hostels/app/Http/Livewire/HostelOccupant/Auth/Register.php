<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Auth;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\HostelOccupantUser;

class Register extends Component
{
    public function __invoke()
    {
        return $this->render();
    }

    public $first_name;

    public $last_name;

    public $email;

    public $phone;

    public $student_id;

    public $password;

    public $password_confirmation;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:hostel_occupant_users,email',
        'phone' => 'required|string|max:20',
        'student_id' => 'nullable|string|max:50',
        'password' => 'required|string|min:8|confirmed',
    ];

    public function register()
    {
        $this->validate();

        // First check if hostel occupant exists
        $hostelOccupant = HostelOccupant::where('email', $this->email)
            ->orWhere('phone', $this->phone)
            ->orWhere('student_id', $this->student_id)
            ->first();

        // If hostel occupant doesn't exist, create one
        if (! $hostelOccupant) {
            $hostelOccupant = HostelOccupant::create([
                'hostel_id' => null, // Will be assigned during booking
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone' => $this->phone,
                'email' => $this->email,
                'student_id' => $this->student_id,
                'status' => 'prospect',
            ]);
        }

        // Create hostel occupant user account
        $hostelOccupantUser = HostelOccupantUser::create([
            'hostel_occupant_id' => $hostelOccupant->id,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Authenticate the hostel occupant
        auth('hostel_occupant')->login($hostelOccupantUser);

        return redirect()->route('hostel_occupant.dashboard');
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.auth.register')
            ->layout('hostels::layouts.guest');
    }
}
