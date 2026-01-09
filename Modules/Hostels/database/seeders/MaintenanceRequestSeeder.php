<?php

namespace Modules\Hostels\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Hostels\Enums\MaintenancePriority;
use Modules\Hostels\Enums\MaintenanceStatus;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\MaintenanceRequest;
use Modules\Hostels\Models\Room;
use Modules\Hostels\Models\Tenant;

class MaintenanceRequestSeeder extends Seeder
{
    public function run(): void
    {
        $hostels = Hostel::all();

        if ($hostels->isEmpty()) {
            $this->call(HostelSeeder::class);
            $hostels = Hostel::all();
        }

        $rooms = Room::all();
        if ($rooms->isEmpty()) {
            $this->call(RoomSeeder::class);
            $rooms = Room::all();
        }

        $beds = Bed::all();
        if ($beds->isEmpty()) {
            $this->call(BedSeeder::class);
            $beds = Bed::all();
        }

        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->call(TenantSeeder::class);
            $tenants = Tenant::all();
        }

        $users = User::whereIn('role', ['admin', 'manager', 'supervisor', 'maintenance'])->get();
        if ($users->isEmpty()) {
            $users = User::factory()->count(5)->create([
                'role' => 'maintenance',
            ]);
        }

        $maintenanceIssues = [
            'Plumbing' => ['Leaking faucet', 'Clogged drain', 'Running toilet', 'Low water pressure', 'Water heater issue'],
            'Electrical' => ['Power outlet not working', 'Light fixture broken', 'Flickering lights', 'Circuit breaker tripping', 'Switch not functioning'],
            'HVAC' => ['AC not cooling', 'Heater not working', 'Strange noises from AC', 'Poor air circulation', 'Thermostat malfunction'],
            'Furniture' => ['Broken bed frame', 'Damaged desk', 'Wobbly chair', 'Cracked wardrobe', 'Drawer stuck'],
            'Appliances' => ['Refrigerator not cooling', 'Microwave not heating', 'Kettle broken', 'Fan not spinning', 'TV not working'],
            'Structural' => ['Crack in wall', 'Peeling paint', 'Door not closing properly', 'Window stuck', 'Flooring issue'],
            'Cleaning' => ['Deep cleaning required', 'Carpet stain removal', 'Bathroom sanitization', 'Kitchen deep clean', 'Odor removal'],
        ];

        $statusWeights = [
            MaintenanceStatus::PENDING->value => 25,
            MaintenanceStatus::IN_PROGRESS->value => 20,
            MaintenanceStatus::COMPLETED->value => 50,
            MaintenanceStatus::CANCELLED->value => 5,
        ];

        $priorityWeights = [
            MaintenancePriority::LOW->value => 30,
            MaintenancePriority::MEDIUM->value => 40,
            MaintenancePriority::HIGH->value => 20,
            MaintenancePriority::CRITICAL->value => 10,
        ];

        foreach ($hostels as $hostel) {
            $hostelRooms = $rooms->where('hostel_id', $hostel->id);
            $hostelBeds = $beds->whereIn('room_id', $hostelRooms->pluck('id'));
            $hostelTenants = $tenants->whereIn('bed_id', $hostelBeds->pluck('id'));

            $requestsPerHostel = rand(10, 30);

            for ($i = 0; $i < $requestsPerHostel; $i++) {
                $category = array_rand($maintenanceIssues);
                $issue = $maintenanceIssues[$category][array_rand($maintenanceIssues[$category])];

                $room = $hostelRooms->random();
                $bed = $hostelBeds->where('room_id', $room->id)->random();
                $tenant = $hostelTenants->where('bed_id', $bed->id)->first();

                $status = $this->getWeightedStatus($statusWeights);
                $priority = $this->getWeightedStatus($priorityWeights);

                $requestData = [
                    'hostel_id' => $hostel->id,
                    'room_id' => $room->id,
                    'bed_id' => $bed->id,
                    'reported_by_tenant_id' => $tenant?->id,
                    'reported_by_user_id' => $users->random()->id,
                    'title' => $issue,
                    'description' => $this->generateDescription($category, $issue, $room->room_number),
                    'priority' => $priority,
                    'status' => $status,
                    'reported_at' => now()->subDays(rand(1, 90)),
                ];

                if ($status === MaintenanceStatus::IN_PROGRESS->value) {
                    $requestData['assigned_to_user_id'] = $users->random()->id;
                }

                if (in_array($status, [MaintenanceStatus::COMPLETED->value, MaintenanceStatus::CANCELLED->value])) {
                    $requestData['assigned_to_user_id'] = $users->random()->id;
                    $requestData['completed_at'] = now()->subDays(rand(0, 30));
                }

                MaintenanceRequest::create($requestData);
            }
        }
    }

    private function getWeightedStatus(array $weights): string
    {
        $total = array_sum($weights);
        $random = rand(1, $total);
        $current = 0;

        foreach ($weights as $status => $weight) {
            $current += $weight;
            if ($random <= $current) {
                return $status;
            }
        }

        return MaintenanceStatus::PENDING->value;
    }

    private function generateDescription(string $category, string $issue, string $roomNumber): string
    {
        $descriptions = [
            'Plumbing' => [
                "{$issue} in Room {$roomNumber}. Water is leaking and causing inconvenience.",
                "Plumbing issue: {$issue}. Requires immediate attention in Room {$roomNumber}.",
                "{$issue} reported in Room {$roomNumber}. Tenant complains about water wastage.",
            ],
            'Electrical' => [
                "Electrical problem: {$issue} in Room {$roomNumber}. Safety concern.",
                "{$issue} detected in Room {$roomNumber}. Electrical system needs inspection.",
                "Power issue: {$issue}. Room {$roomNumber} has no functioning outlets.",
            ],
            'HVAC' => [
                "HVAC malfunction: {$issue} in Room {$roomNumber}. Temperature control affected.",
                "{$issue} reported. Room {$roomNumber} is uncomfortable due to climate control issues.",
                "Air conditioning problem: {$issue}. Room {$roomNumber} needs HVAC service.",
            ],
            'Furniture' => [
                "Furniture damage: {$issue} in Room {$roomNumber}. Item needs repair or replacement.",
                "{$issue} reported. Furniture in Room {$roomNumber} is unsafe for use.",
                "Broken furniture: {$issue}. Room {$roomNumber} requires maintenance.",
            ],
            'Appliances' => [
                "Appliance failure: {$issue} in Room {$roomNumber}. Tenant cannot use essential equipment.",
                "{$issue} reported. Appliance in Room {$roomNumber} needs service.",
                "Equipment malfunction: {$issue}. Room {$roomNumber} appliance not working.",
            ],
            'Structural' => [
                "Structural issue: {$issue} in Room {$roomNumber}. Building integrity concern.",
                "{$issue} detected. Room {$roomNumber} has cosmetic or structural damage.",
                "Building maintenance: {$issue}. Room {$roomNumber} requires structural repair.",
            ],
            'Cleaning' => [
                "Cleaning request: {$issue} for Room {$roomNumber}. Deep cleaning required.",
                "{$issue} needed. Room {$roomNumber} requires professional cleaning service.",
                "Sanitation issue: {$issue}. Room {$roomNumber} needs thorough cleaning.",
            ],
        ];

        return $descriptions[$category][array_rand($descriptions[$category])];
    }
}
