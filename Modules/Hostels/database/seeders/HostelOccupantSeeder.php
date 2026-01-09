<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;

class HostelOccupantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hostels = Hostel::all();

        if ($hostels->isEmpty()) {
            $this->call(HostelSeeder::class);
            $hostels = Hostel::all();
        }

        $firstNames = ['Kwame', 'Ama', 'Kofi', 'Abena', 'Yaw', 'Akua', 'Kwasi', 'Esi', 'Kwabena', 'Adwoa'];
        $lastNames = ['Mensah', 'Owusu', 'Agyemang', 'Darko', 'Appiah', 'Sarpong', 'Boateng', 'Amoah', 'Osei', 'Danso'];
        $genders = ['male', 'female'];
        $statuses = ['prospect', 'active', 'inactive', 'blacklisted'];

        foreach ($hostels as $hostel) {
            for ($i = 1; $i <= 50; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $gender = $genders[array_rand($genders)];

                HostelOccupant::create([
                    'hostel_id' => $hostel->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'full_name' => $firstName.' '.$lastName,
                    'gender' => $gender,
                    'phone' => '+233'.rand(200000000, 299999999),
                    'email' => strtolower($firstName.'.'.$lastName.$i.'@example.com'),
                    'status' => $statuses[array_rand($statuses)],
                    'national_id_number' => 'ID'.rand(100000, 999999),
                    'emergency_contact_phone' => '+233'.rand(200000000, 299999999),
                    'emergency_contact_name' => 'Emergency Contact '.$i,
                ]);
            }
        }
    }
}
