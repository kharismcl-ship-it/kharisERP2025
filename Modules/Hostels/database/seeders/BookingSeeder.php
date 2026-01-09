<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Room;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occupants = HostelOccupant::all();
        if ($occupants->isEmpty()) {
            $this->call(HostelOccupantSeeder::class);
            $occupants = HostelOccupant::all();
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

        $bookingStatuses = ['pending', 'awaiting_payment', 'confirmed', 'checked_in', 'checked_out', 'no_show', 'cancelled'];
        $paymentStatuses = ['unpaid', 'partially_paid', 'paid', 'overpaid'];
        $bookingTypes = ['academic', 'short_stay', 'semester'];

        foreach ($occupants as $occupant) {
            $room = $rooms->random();
            $availableBeds = $beds->where('room_id', $room->id)->where('status', 'available');

            if ($availableBeds->count() > 0) {
                $bed = $availableBeds->random();

                $checkInDate = now()->subDays(rand(1, 180));
                $checkOutDate = $checkInDate->copy()->addDays(rand(30, 365));

                $totalAmount = $room->base_rate * $checkInDate->diffInMonths($checkOutDate);
                $amountPaid = $totalAmount * (rand(0, 100) / 100);

                Booking::create([
                    'hostel_id' => $occupant->hostel_id,
                    'hostel_occupant_id' => $occupant->id,
                    'room_id' => $room->id,
                    'bed_id' => $bed->id,
                    'booking_reference' => 'BK'.now()->format('Ymd').rand(1000, 9999),
                    'booking_type' => $bookingTypes[array_rand($bookingTypes)],
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'total_amount' => $totalAmount,
                    'amount_paid' => $amountPaid,
                    'balance_amount' => $totalAmount - $amountPaid,
                    'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                    'status' => $bookingStatuses[array_rand($bookingStatuses)],
                    'channel' => ['walk_in', 'online', 'agent'][array_rand([0, 1, 2])],
                    'notes' => 'Booking for '.$occupant->full_name.' in room '.$room->room_number,
                ]);
            }
        }
    }
}
