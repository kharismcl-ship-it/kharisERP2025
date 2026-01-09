<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Deposit;
use Modules\Hostels\Models\Hostel;

class DepositSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::all();

        if ($bookings->isEmpty()) {
            $this->call(BookingSeeder::class);
            $bookings = Booking::all();
        }

        $hostels = Hostel::all();
        if ($hostels->isEmpty()) {
            $this->call(HostelSeeder::class);
            $hostels = Hostel::all();
        }

        $depositTypes = ['security', 'utility', 'damage', 'booking'];
        $statuses = ['pending', 'collected', 'refunded', 'partially_refunded', 'forfeited'];

        foreach ($bookings as $booking) {
            $hostel = $hostels->find($booking->hostel_id);

            if ($hostel && $hostel->require_deposit) {
                $depositAmount = $hostel->deposit_type === 'percentage'
                    ? $booking->total_amount * ($hostel->deposit_percentage / 100)
                    : $hostel->deposit_amount;

                Deposit::create([
                    'hostel_id' => $hostel->id,
                    'tenant_id' => $booking->tenant_id,
                    'booking_id' => $booking->id,
                    'amount' => $depositAmount,
                    'deposit_type' => $depositTypes[array_rand($depositTypes)],
                    'status' => $statuses[array_rand($statuses)],
                    'collected_date' => now()->subDays(rand(1, 30)),
                    'refund_date' => rand(0, 1) ? now()->subDays(rand(1, 15)) : null,
                    'refund_amount' => rand(0, 1) ? $depositAmount * (rand(50, 100) / 100) : 0,
                    'deductions' => rand(0, 1) ? ['cleaning' => 50, 'damages' => 100] : [],
                    'notes' => 'Deposit for booking '.$booking->booking_reference,
                ]);
            }
        }
    }
}
