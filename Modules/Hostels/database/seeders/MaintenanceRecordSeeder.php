<?php

namespace Modules\Hostels\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Hostels\Enums\MaintenanceOutcome;
use Modules\Hostels\Enums\MaintenancePriority;
use Modules\Hostels\Enums\MaintenanceStatus;
use Modules\Hostels\Enums\MaintenanceType;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelInventoryItem;
use Modules\Hostels\Models\MaintenanceRecord;
use Modules\Hostels\Models\Room;
use Modules\Hostels\Models\RoomInventoryAssignment;
use Modules\Hostels\Models\Tenant;
use Modules\HR\Models\Employee;

class MaintenanceRecordSeeder extends Seeder
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

        $inventoryItems = HostelInventoryItem::all();
        if ($inventoryItems->isEmpty()) {
            $this->call(HostelInventoryItemSeeder::class);
            $inventoryItems = HostelInventoryItem::all();
        }

        $roomAssignments = RoomInventoryAssignment::all();
        if ($roomAssignments->isEmpty()) {
            $this->call(RoomInventoryAssignmentSeeder::class);
            $roomAssignments = RoomInventoryAssignment::all();
        }

        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->call(TenantSeeder::class);
            $tenants = Tenant::all();
        }

        $employees = Employee::all();
        if ($employees->isEmpty()) {
            $employees = Employee::factory()->count(8)->create();
        }

        $users = User::whereIn('role', ['admin', 'manager', 'supervisor', 'maintenance'])->get();
        if ($users->isEmpty()) {
            $users = User::factory()->count(5)->create([
                'role' => 'maintenance',
            ]);
        }

        $maintenanceTypes = [
            MaintenanceType::PREVENTIVE->value => 30,
            MaintenanceType::CORRECTIVE->value => 50,
            MaintenanceType::ROUTINE->value => 15,
            MaintenanceType::EMERGENCY->value => 5,
        ];

        $priorityWeights = [
            MaintenancePriority::LOW->value => 30,
            MaintenancePriority::MEDIUM->value => 40,
            MaintenancePriority::HIGH->value => 20,
            MaintenancePriority::CRITICAL->value => 10,
        ];

        $statusWeights = [
            MaintenanceStatus::COMPLETED->value => 70,
            MaintenanceStatus::IN_PROGRESS->value => 15,
            MaintenanceStatus::PENDING->value => 10,
            MaintenanceStatus::CANCELLED->value => 5,
        ];

        $outcomeWeights = [
            MaintenanceOutcome::RESOLVED->value => 65,
            MaintenanceOutcome::PARTIALLY_RESOLVED->value => 20,
            MaintenanceOutcome::NOT_RESOLVED->value => 10,
            MaintenanceOutcome::DEFERRED->value => 5,
        ];

        $maintenanceTasks = [
            'Plumbing' => ['Pipe replacement', 'Faucet repair', 'Drain cleaning', 'Toilet repair', 'Water heater service'],
            'Electrical' => ['Wiring inspection', 'Outlet replacement', 'Circuit breaker repair', 'Light fixture installation', 'Switch replacement'],
            'HVAC' => ['AC filter replacement', 'Compressor service', 'Thermostat calibration', 'Duct cleaning', 'Refrigerant recharge'],
            'Furniture' => ['Bed frame repair', 'Desk reinforcement', 'Chair repair', 'Wardrobe hinge replacement', 'Drawer track fix'],
            'Appliances' => ['Refrigerator service', 'Microwave repair', 'Kettle element replacement', 'Fan motor repair', 'TV troubleshooting'],
            'Structural' => ['Wall patching', 'Paint touch-up', 'Door adjustment', 'Window repair', 'Flooring fix'],
            'Cleaning' => ['Deep carpet cleaning', 'Bathroom sanitization', 'Kitchen degreasing', 'Window washing', 'Vent cleaning'],
        ];

        $partsInventory = [
            'Plumbing' => ['Pipe fittings', 'Washers', 'Valves', 'Seal tape', 'Drain cleaner'],
            'Electrical' => ['Wire nuts', 'Electrical tape', 'Outlets', 'Switches', 'Circuit breakers'],
            'HVAC' => ['AC filters', 'Thermostats', 'Refrigerant', 'Duct tape', 'Insulation'],
            'Furniture' => ['Screws', 'Bolts', 'Hinges', 'Drawer slides', 'Wood glue'],
            'Appliances' => ['Fuses', 'Heating elements', 'Motors', 'Control boards', 'Power cords'],
            'Structural' => ['Drywall patches', 'Paint', 'Caulk', 'Nails', 'Screws'],
            'Cleaning' => ['Cleaning solution', 'Sponges', 'Gloves', 'Brushes', 'Disinfectant'],
        ];

        foreach ($hostels as $hostel) {
            $hostelRooms = $rooms->where('hostel_id', $hostel->id);
            $hostelItems = $inventoryItems->where('hostel_id', $hostel->id);
            $hostelAssignments = $roomAssignments->whereIn('room_id', $hostelRooms->pluck('id'));
            $hostelTenants = $tenants->whereIn('bed_id', $hostelRooms->pluck('id'));

            $recordsPerHostel = rand(15, 40);

            for ($i = 0; $i < $recordsPerHostel; $i++) {
                $category = array_rand($maintenanceTasks);
                $task = $maintenanceTasks[$category][array_rand($maintenanceTasks[$category])];

                $type = $this->getWeightedStatus($maintenanceTypes);
                $priority = $this->getWeightedStatus($priorityWeights);
                $status = $this->getWeightedStatus($statusWeights);
                $outcome = $this->getWeightedStatus($outcomeWeights);

                $room = $hostelRooms->random();
                $inventoryItem = $hostelItems->random();
                $assignment = $hostelAssignments->where('room_id', $room->id)->first();
                $tenant = $hostelTenants->where('bed_id', $room->id)->first();
                $employee = $employees->random();
                $user = $users->random();

                $laborCost = rand(5000, 30000); // 50-300 GHS
                $partsCost = rand(1000, 15000); // 10-150 GHS
                $totalCost = $laborCost + $partsCost;

                $partsUsed = $this->generatePartsList($partsInventory[$category]);

                $recordData = [
                    'tenant_id' => $tenant?->id,
                    'inventory_item_id' => $inventoryItem->id,
                    'room_assignment_id' => $assignment?->id,
                    'assigned_to' => $employee->id,
                    'maintenance_type' => $type,
                    'priority' => $priority,
                    'status' => $status,
                    'scheduled_date' => now()->subDays(rand(1, 60)),
                    'description' => $task.' - '.$this->generateTaskDescription($category, $task, $room->room_number),
                    'issue_details' => $this->generateIssueDetails($category, $task),
                    'work_performed' => $this->generateWorkPerformed($category, $task, $outcome),
                    'parts_used' => $partsUsed,
                    'labor_cost' => $laborCost,
                    'parts_cost' => $partsCost,
                    'total_cost' => $totalCost,
                    'outcome' => $outcome,
                    'notes' => $this->generateMaintenanceNotes($category, $task, $outcome),
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ];

                if ($status === MaintenanceStatus::IN_PROGRESS->value) {
                    $recordData['started_at'] = now()->subDays(rand(1, 7));
                }

                if ($status === MaintenanceStatus::COMPLETED->value) {
                    $recordData['started_at'] = now()->subDays(rand(2, 14));
                    $recordData['completed_at'] = now()->subDays(rand(0, 7));
                }

                if ($outcome === MaintenanceOutcome::PARTIALLY_RESOLVED->value ||
                    $outcome === MaintenanceOutcome::NOT_RESOLVED->value) {
                    $recordData['follow_up_required'] = true;
                    $recordData['next_maintenance_date'] = now()->addDays(rand(7, 30));
                }

                MaintenanceRecord::create($recordData);
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

        return MaintenanceStatus::COMPLETED->value;
    }

    private function generatePartsList(array $availableParts): string
    {
        $partsCount = rand(1, 4);
        $parts = [];

        for ($i = 0; $i < $partsCount; $i++) {
            $part = $availableParts[array_rand($availableParts)];
            $quantity = rand(1, 5);
            $parts[] = "{$quantity} x {$part}";
        }

        return implode(', ', $parts);
    }

    private function generateTaskDescription(string $category, string $task, string $roomNumber): string
    {
        $descriptions = [
            'Plumbing' => "{$task} required in Room {$roomNumber} to address water system issues",
            'Electrical' => "{$task} needed for Room {$roomNumber} electrical system maintenance",
            'HVAC' => "{$task} scheduled for Room {$roomNumber} climate control system",
            'Furniture' => "{$task} requested for Room {$roomNumber} furniture maintenance",
            'Appliances' => "{$task} required for Room {$roomNumber} appliance functionality",
            'Structural' => "{$task} needed for Room {$roomNumber} building maintenance",
            'Cleaning' => "{$task} scheduled for Room {$roomNumber} deep cleaning",
        ];

        return $descriptions[$category];
    }

    private function generateIssueDetails(string $category, string $task): string
    {
        $details = [
            'Plumbing' => "Identified issue requiring {$task}. Water leakage/damage observed. Needs professional attention.",
            'Electrical' => "Electrical system issue detected. {$task} required to ensure safety and functionality.",
            'HVAC' => "Climate control problem identified. {$task} needed to restore proper temperature regulation.",
            'Furniture' => "Furniture damage reported. {$task} required to restore item to usable condition.",
            'Appliances' => "Appliance malfunction detected. {$task} needed to restore functionality.",
            'Structural' => "Building maintenance issue identified. {$task} required to maintain structural integrity.",
            'Cleaning' => "Cleaning requirement identified. {$task} scheduled to maintain hygiene standards.",
        ];

        return $details[$category];
    }

    private function generateWorkPerformed(string $category, string $task, string $outcome): string
    {
        $work = [
            'Plumbing' => "Performed {$task}. Checked all connections, tested water flow, and ensured no leaks.",
            'Electrical' => "Completed {$task}. Verified circuit integrity, tested all components, and ensured safety compliance.",
            'HVAC' => "Executed {$task}. Calibrated system, cleaned components, and tested temperature control.",
            'Furniture' => "Completed {$task}. Repaired/replaced damaged parts and tested structural integrity.",
            'Appliances' => "Performed {$task}. Diagnosed issue, replaced faulty components, and tested functionality.",
            'Structural' => "Completed {$task}. Repaired damage, applied finishes, and ensured structural soundness.",
            'Cleaning' => "Executed {$task}. Thorough cleaning performed, disinfectant applied, and area sanitized.",
        ];

        $outcomeText = MaintenanceOutcome::from($outcome)->label();

        return $work[$category]." Outcome: {$outcomeText}.";
    }

    private function generateMaintenanceNotes(string $category, string $task, string $outcome): string
    {
        $notes = [
            'Plumbing' => "{$task} completed successfully. Recommend periodic water system checks.",
            'Electrical' => "{$task} performed. Electrical system now meets safety standards.",
            'HVAC' => "{$task} executed. System operating efficiently. Recommend filter replacement every 3 months.",
            'Furniture' => "{$task} completed. Furniture restored to good condition. Monitor for future wear.",
            'Appliances' => "{$task} performed. Appliance functioning properly. Provide user instructions if needed.",
            'Structural' => "{$task} completed. Structural integrity maintained. Schedule follow-up inspection.",
            'Cleaning' => "{$task} executed. Area thoroughly cleaned and sanitized. Maintain regular cleaning schedule.",
        ];

        $outcomeText = MaintenanceOutcome::from($outcome)->label();

        return $notes[$category]." Final outcome: {$outcomeText}.";
    }
}
