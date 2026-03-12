<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\HostelOccupantUser;

class HostelOccupantTestSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = \App\Models\Company::first()?->id ?? 1;
        $hostelId  = \Modules\Hostels\Models\Hostel::first()?->id ?? 1;

        // Create the occupant profile (or find if email already exists)
        $occupant = HostelOccupant::firstOrCreate(
            ['email' => 'test.occupant@example.com'],
            [
                'company_id'   => $companyId,
                'hostel_id'    => $hostelId,
                'first_name'   => 'Test',
                'last_name'    => 'Occupant',
                'full_name'    => 'Test Occupant',
                'gender'       => 'male',
                'phone'        => '08000000001',
                'student_id'   => 'STU/2024/001',
                'status'       => 'prospect',
            ]
        );

        // Create the login account
        $user = HostelOccupantUser::firstOrCreate(
            ['email' => 'test.occupant@example.com'],
            [
                'hostel_occupant_id' => $occupant->id,
                'password'           => Hash::make('password'),
            ]
        );

        $this->command->info('Hostel occupant test user created.');
        $this->command->line('  Email:    test.occupant@example.com');
        $this->command->line('  Password: password');
        $this->command->line('  Login at: /hostel-occupant/login');
    }
}
