<?php

namespace Modules\Hostels\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelInventoryItem;
use Modules\Hostels\Models\HostelInventoryTransaction;
use Modules\Hostels\Models\Room;

class HostelInventoryTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $hostels = Hostel::all();

        if ($hostels->isEmpty()) {
            $this->call(HostelSeeder::class);
            $hostels = Hostel::all();
        }

        $inventoryItems = HostelInventoryItem::all();
        if ($inventoryItems->isEmpty()) {
            $this->call(HostelInventoryItemSeeder::class);
            $inventoryItems = HostelInventoryItem::all();
        }

        $rooms = Room::all();
        if ($rooms->isEmpty()) {
            $this->call(RoomSeeder::class);
            $rooms = Room::all();
        }

        $users = User::whereIn('role', ['admin', 'manager', 'supervisor'])->get();
        if ($users->isEmpty()) {
            $users = User::factory()->count(3)->create(['role' => 'manager']);
        }

        $transactionTypes = ['receipt', 'issue', 'adjustment', 'transfer'];
        $transactionReasons = [
            'receipt' => ['Purchase order delivery', 'Supplier delivery', 'Stock replenishment'],
            'issue' => ['Room restocking', 'Maintenance use', 'Staff request', 'Cleaning supplies'],
            'adjustment' => ['Stock count correction', 'Damage write-off', 'Theft adjustment'],
            'transfer' => ['Inter-hostel transfer', 'Room to room transfer'],
        ];

        foreach ($hostels as $hostel) {
            $hostelItems = $inventoryItems->where('hostel_id', $hostel->id);
            $hostelRooms = $rooms->where('hostel_id', $hostel->id);

            foreach ($hostelItems as $item) {
                $currentBalance = $item->current_stock;
                $transactionsCount = rand(5, 15);

                for ($i = 0; $i < $transactionsCount; $i++) {
                    $transactionType = $transactionTypes[array_rand($transactionTypes)];
                    $reason = $transactionReasons[$transactionType][array_rand($transactionReasons[$transactionType])];

                    $quantity = match ($transactionType) {
                        'receipt' => rand(5, 50),
                        'issue' => rand(1, min(20, $currentBalance)),
                        'adjustment' => rand(-10, 10),
                        'transfer' => rand(1, min(15, $currentBalance)),
                        default => 0
                    };

                    if ($quantity === 0) {
                        continue;
                    }

                    $currentBalance += $quantity;

                    $room = null;
                    if (in_array($transactionType, ['issue', 'transfer']) && $hostelRooms->isNotEmpty()) {
                        $room = $hostelRooms->random();
                    }

                    HostelInventoryTransaction::create([
                        'hostel_id' => $hostel->id,
                        'inventory_item_id' => $item->id,
                        'room_id' => $room?->id,
                        'processed_by' => $users->random()->id,
                        'transaction_type' => $transactionType,
                        'quantity' => $quantity,
                        'balance_after' => $currentBalance,
                        'notes' => $reason.' - '.$this->generateTransactionNote($transactionType, $item->name, $quantity),
                        'reference_number' => 'TRX-'.strtoupper(substr($transactionType, 0, 3)).'-'.now()->format('Ymd').'-'.rand(1000, 9999),
                        'transaction_date' => now()->subDays(rand(0, 90)),
                    ]);
                }

                $item->update(['current_stock' => $currentBalance]);
            }
        }
    }

    private function generateTransactionNote(string $type, string $itemName, int $quantity): string
    {
        $notes = [
            'receipt' => [
                "Received {$quantity} units of {$itemName} from supplier",
                "Stock replenishment for {$itemName} - {$quantity} units",
                "Purchase order delivery: {$quantity} x {$itemName}",
            ],
            'issue' => [
                "Issued {$quantity} units of {$itemName} for room restocking",
                "{$itemName} issued to maintenance team - {$quantity} units",
                "Staff request fulfilled: {$quantity} x {$itemName}",
            ],
            'adjustment' => [
                "Stock adjustment: {$quantity} units of {$itemName}",
                "Physical count correction for {$itemName}",
                "Inventory variance adjustment: {$quantity} units",
            ],
            'transfer' => [
                "Inter-hostel transfer of {$quantity} units of {$itemName}",
                "Room to room transfer: {$quantity} x {$itemName}",
                "Inventory redistribution: {$itemName} - {$quantity} units",
            ],
        ];

        return $notes[$type][array_rand($notes[$type])];
    }
}
