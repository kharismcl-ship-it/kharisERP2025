<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\BookingCharge;
use Modules\Hostels\Models\FeeType;

class BookingChargeSeeder extends Seeder
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

        $feeTypes = FeeType::all();
        if ($feeTypes->isEmpty()) {
            $this->call(FeeTypeSeeder::class);
            $feeTypes = FeeType::all();
        }

        foreach ($bookings as $booking) {
            // Add 2-5 random charges per booking
            $chargeCount = rand(2, 5);
            $applicableFeeTypes = $feeTypes->where('hostel_id', $booking->hostel_id);

            if ($applicableFeeTypes->count() > 0) {
                $selectedFeeTypes = $applicableFeeTypes->random(min($chargeCount, $applicableFeeTypes->count()));

                foreach ($selectedFeeTypes as $feeType) {
                    BookingCharge::create([
                        'booking_id'  => $booking->id,
                        'fee_type_id' => $feeType->id,
                        'unit_price'  => $feeType->default_amount,
                        'quantity'    => 1,
                        'amount'      => $feeType->default_amount,
                        'description' => $feeType->name.' for booking '.$booking->booking_reference,
                    ]);
                }
            }
        }
    }
}
