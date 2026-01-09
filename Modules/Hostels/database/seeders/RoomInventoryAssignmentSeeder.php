<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Enums\AssignmentStatus;
use Modules\Hostels\Models\HostelInventoryItem;
use Modules\Hostels\Models\Room;
use Modules\Hostels\Models\RoomInventoryAssignment;

class RoomInventoryAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = Room::with('hostel')->get();

        if ($rooms->isEmpty()) {
            $this->call(RoomSeeder::class);
            $rooms = Room::with('hostel')->get();
        }

        $inventoryItems = HostelInventoryItem::all();
        if ($inventoryItems->isEmpty()) {
            $this->call(HostelInventoryItemSeeder::class);
            $inventoryItems = HostelInventoryItem::all();
        }

        $assignableItems = $inventoryItems->filter(function ($item) {
            return in_array($item->category, [
                'Furniture', 'Appliances', 'Electronics', 'Bedding', 'Bathroom', 'Kitchen', 'Cleaning',
            ]);
        });

        $roomTypesInventory = [
            'single' => ['Bed', 'Mattress', 'Pillow', 'Desk', 'Chair', 'Wardrobe', 'Fan', 'Light'],
            'double' => ['Bed', 'Mattress', 'Pillow', 'Desk', 'Chair', 'Wardrobe', 'Fan', 'Light', 'Mini-fridge'],
            'suite' => ['Bed', 'Mattress', 'Pillow', 'Desk', 'Chair', 'Wardrobe', 'AC Unit', 'TV', 'Mini-fridge', 'Microwave', 'Kettle'],
            'dormitory' => ['Bed', 'Mattress', 'Pillow', 'Locker', 'Study Table', 'Chair', 'Fan', 'Light'],
        ];

        $statusWeights = [
            AssignmentStatus::ACTIVE->value => 70,
            AssignmentStatus::DAMAGED->value => 8,
            AssignmentStatus::MAINTENANCE->value => 7,
            AssignmentStatus::REMOVED->value => 6,
            AssignmentStatus::LOST->value => 4,
            AssignmentStatus::RESERVED->value => 3,
            AssignmentStatus::DECOMMISSIONED->value => 2,
        ];

        foreach ($rooms as $room) {
            $roomType = strtolower($room->room_type);
            $expectedItems = $roomTypesInventory[$roomType] ?? $roomTypesInventory['single'];

            $itemsToAssign = $assignableItems
                ->where('hostel_id', $room->hostel_id)
                ->filter(function ($item) use ($expectedItems) {
                    return in_array($item->name, $expectedItems);
                });

            if ($itemsToAssign->isEmpty()) {
                continue;
            }

            $itemsCount = min(rand(3, count($expectedItems)), $itemsToAssign->count());
            $selectedItems = $itemsToAssign->random($itemsCount);

            foreach ($selectedItems as $item) {
                $status = $this->getWeightedStatus($statusWeights);
                $quantity = $this->getQuantityForItem($item->name, $roomType);

                $assignmentData = [
                    'room_id' => $room->id,
                    'inventory_item_id' => $item->id,
                    'quantity' => $quantity,
                    'status' => $status,
                    'assigned_at' => now()->subDays(rand(1, 365)),
                    'notes' => $this->generateAssignmentNote($item->name, $room->room_number, $status),
                ];

                if ($status !== AssignmentStatus::ACTIVE->value) {
                    $assignmentData['condition_notes'] = $this->generateConditionNote($status, $item->name);

                    if (in_array($status, [AssignmentStatus::REMOVED->value, AssignmentStatus::DECOMMISSIONED->value])) {
                        $assignmentData['removed_at'] = now()->subDays(rand(1, 30));
                    }
                }

                RoomInventoryAssignment::create($assignmentData);
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

        return AssignmentStatus::ACTIVE->value;
    }

    private function getQuantityForItem(string $itemName, string $roomType): int
    {
        $quantities = [
            'Bed' => $roomType === 'double' ? 2 : 1,
            'Mattress' => $roomType === 'double' ? 2 : 1,
            'Pillow' => $roomType === 'double' ? 4 : 2,
            'Desk' => 1,
            'Chair' => $roomType === 'suite' ? 2 : 1,
            'Wardrobe' => 1,
            'Fan' => 1,
            'Light' => $roomType === 'suite' ? 3 : 2,
            'Mini-fridge' => 1,
            'AC Unit' => 1,
            'TV' => 1,
            'Microwave' => 1,
            'Kettle' => 1,
            'Locker' => $roomType === 'dormitory' ? rand(4, 8) : 1,
            'Study Table' => $roomType === 'dormitory' ? rand(2, 4) : 1,
        ];

        return $quantities[$itemName] ?? 1;
    }

    private function generateAssignmentNote(string $itemName, string $roomNumber, string $status): string
    {
        $statusText = AssignmentStatus::from($status)->label();

        $notes = [
            "{$itemName} assigned to Room {$roomNumber} - {$statusText}",
            "Room {$roomNumber} inventory: {$itemName} - {$statusText}",
            "{$itemName} provision for Room {$roomNumber} ({$statusText})",
            "Inventory assignment: {$itemName} to Room {$roomNumber} - {$statusText}",
        ];

        return $notes[array_rand($notes)];
    }

    private function generateConditionNote(string $status, string $itemName): string
    {
        $conditionNotes = [
            AssignmentStatus::DAMAGED->value => [
                "{$itemName} has visible damage and requires replacement",
                "{$itemName} is broken and needs repair",
                "{$itemName} damaged during use, needs attention",
            ],
            AssignmentStatus::MAINTENANCE->value => [
                "{$itemName} sent for routine maintenance",
                "{$itemName} undergoing scheduled service",
                "{$itemName} removed for maintenance work",
            ],
            AssignmentStatus::REMOVED->value => [
                "{$itemName} removed from room during renovation",
                "{$itemName} temporarily removed for deep cleaning",
                "{$itemName} removed for inventory audit",
            ],
            AssignmentStatus::LOST->value => [
                "{$itemName} reported missing during room inspection",
                "{$itemName} cannot be located, presumed lost",
                "{$itemName} lost during tenant move-out",
            ],
            AssignmentStatus::DECOMMISSIONED->value => [
                "{$itemName} decommissioned due to end of life",
                "{$itemName} removed from service permanently",
                "{$itemName} decommissioned after damage assessment",
            ],
            AssignmentStatus::RESERVED->value => [
                "{$itemName} reserved for incoming tenant",
                "{$itemName} held for special assignment",
                "{$itemName} reserved for VIP room preparation",
            ],
        ];

        return $conditionNotes[$status][array_rand($conditionNotes[$status])];
    }
}
