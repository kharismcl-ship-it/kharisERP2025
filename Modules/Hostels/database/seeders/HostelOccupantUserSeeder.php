<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\HostelOccupantUser;

class HostelOccupantUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hostelOccupants = HostelOccupant::all();

        if ($hostelOccupants->isEmpty()) {
            $this->call(HostelOccupantSeeder::class);
            $hostelOccupants = HostelOccupant::all();
        }

        $statuses = ['active', 'inactive'];

        foreach ($hostelOccupants as $hostelOccupant) {
            HostelOccupantUser::create([
                'hostel_occupant_id' => $hostelOccupant->id,
                'username' => strtolower($hostelOccupant->first_name.'.'.$hostelOccupant->last_name),
                'email' => $hostelOccupant->email,
                'phone' => $hostelOccupant->phone,
                'password' => bcrypt('password123'),
                'status' => $statuses[array_rand($statuses)],
                'last_login_at' => now()->subDays(rand(1, 30)),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);
        }
    }
}
