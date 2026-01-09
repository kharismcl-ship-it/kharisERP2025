<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelStaffRole;

class HostelStaffRoleSeeder extends Seeder
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

        $staffRoles = [
            ['Warden', 'Overall hostel management and supervision'],
            ['Assistant Warden', 'Assists warden in daily operations'],
            ['Housekeeper', 'Cleaning and maintenance of rooms'],
            ['Security Guard', 'Security and safety of hostel premises'],
            ['Receptionist', 'Front desk operations and guest services'],
            ['Maintenance Technician', 'Repairs and technical maintenance'],
            ['Cook', 'Food preparation and kitchen management'],
            ['Laundry Attendant', 'Laundry services for residents'],
            ['Accountant', 'Financial management and billing'],
            ['IT Support', 'Technical support and network maintenance'],
        ];

        foreach ($hostels as $hostel) {
            foreach ($staffRoles as $role) {
                HostelStaffRole::create([
                    'hostel_id' => $hostel->id,
                    'name' => $role[0],
                    'description' => $role[1],
                    'is_active' => true,
                    'permissions' => $this->getPermissionsForRole($role[0]),
                ]);
            }
        }
    }

    private function getPermissionsForRole(string $role): array
    {
        return match ($role) {
            'Warden' => ['manage_staff', 'approve_bookings', 'view_reports', 'manage_finances'],
            'Assistant Warden' => ['manage_staff', 'approve_bookings', 'view_reports'],
            'Housekeeper' => ['manage_cleaning', 'update_room_status'],
            'Security Guard' => ['manage_security', 'log_incidents'],
            'Receptionist' => ['manage_bookings', 'check_in_out', 'handle_payments'],
            'Maintenance Technician' => ['manage_maintenance', 'update_equipment_status'],
            'Cook' => ['manage_kitchen', 'order_supplies'],
            'Laundry Attendant' => ['manage_laundry', 'track_inventory'],
            'Accountant' => ['manage_finances', 'generate_reports', 'process_payments'],
            'IT Support' => ['manage_systems', 'troubleshoot_issues'],
            default => ['view_basic_info']
        };
    }
}
