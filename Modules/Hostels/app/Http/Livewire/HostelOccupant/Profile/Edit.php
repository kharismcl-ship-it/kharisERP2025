<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Profile;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Edit extends Component
{
    public $first_name;

    public $last_name;

    public $email;

    public $phone;

    public $student_id;

    public $institution;

    public $address;

    public $emergency_contact_name;

    public $emergency_contact_phone;

    public $current_password;

    public $new_password;

    public $new_password_confirmation;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string|max:20',
        'student_id' => 'nullable|string|max:50',
        'institution' => 'nullable|string|max:255',
        'address' => 'nullable|string',
        'emergency_contact_name' => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:20',
        'new_password' => 'nullable|string|min:8|confirmed',
    ];

    public function __invoke()
    {
        return $this->render();
    }

    public function mount()
    {
        $hostelOccupant = auth('hostel_occupant')->user()->hostelOccupant;

        $this->first_name = $hostelOccupant->first_name;
        $this->last_name = $hostelOccupant->last_name;
        $this->email = $hostelOccupant->email;
        $this->phone = $hostelOccupant->phone;
        $this->student_id = $hostelOccupant->student_id;
        $this->institution = $hostelOccupant->institution;
        $this->address = $hostelOccupant->address;
        $this->emergency_contact_name = $hostelOccupant->emergency_contact_name;
        $this->emergency_contact_phone = $hostelOccupant->emergency_contact_phone;
    }

    public function updateProfile()
    {
        $this->validate();

        $hostelOccupant = auth('hostel_occupant')->user()->hostelOccupant;

        // Check if password update is requested
        if ($this->new_password) {
            // Verify current password
            if (! Hash::check($this->current_password, auth('hostel_occupant')->user()->password)) {
                $this->addError('current_password', 'The current password is incorrect.');

                return;
            }
        }

        // Update hostel occupant information
        $hostelOccupant->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'student_id' => $this->student_id,
            'institution' => $this->institution,
            'address' => $this->address,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
        ]);

        // Update password if provided
        if ($this->new_password) {
            auth('hostel_occupant')->user()->update([
                'password' => Hash::make($this->new_password),
            ]);

            // Clear password fields
            $this->current_password = '';
            $this->new_password = '';
            $this->new_password_confirmation = '';
        }

        session()->flash('message', 'Profile updated successfully.');
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.profile.edit')
            ->layout('hostels::layouts.app');
    }
}
