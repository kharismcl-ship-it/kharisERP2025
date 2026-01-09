<?php

use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\FeeType;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\Room;
use Modules\Hostels\Services\HostelBillingService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(HostelBillingService::class);

    $this->hostel = Hostel::factory()->create();

    $this->room = Room::factory()->create([
        'hostel_id' => $this->hostel->id,
        'daily_rate' => 100.00,
        'weekly_rate' => 600.00,
        'monthly_rate' => 2000.00,
    ]);

    // Create fee types
    $this->cleaningFee = FeeType::factory()->create([
        'hostel_id' => $this->hostel->id,
        'name' => 'Cleaning Fee',
        'amount' => 50.00,
        'type' => 'fixed',
    ]);

    $this->taxFee = FeeType::factory()->create([
        'hostel_id' => $this->hostel->id,
        'name' => 'Tax',
        'amount' => 0.15, // 15%
        'type' => 'percentage',
    ]);
});

test('calculates daily rate correctly', function () {
    $checkIn = now();
    $checkOut = now()->addDays(3);

    $amount = $this->service->calculateRoomCharge(
        $this->room->daily_rate,
        $checkIn,
        $checkOut,
        'daily'
    );

    $this->assertEquals(300.00, $amount);
});

test('calculates weekly rate correctly', function () {
    $checkIn = now();
    $checkOut = now()->addDays(7);

    $amount = $this->service->calculateRoomCharge(
        $this->room->weekly_rate,
        $checkIn,
        $checkOut,
        'weekly'
    );

    $this->assertEquals(600.00, $amount);
});

test('calculates monthly rate correctly', function () {
    $checkIn = now();
    $checkOut = now()->addDays(30);

    $amount = $this->service->calculateRoomCharge(
        $this->room->monthly_rate,
        $checkIn,
        $checkOut,
        'monthly'
    );

    $this->assertEquals(2000.00, $amount);
});

test('applies fixed fees correctly', function () {
    $baseAmount = 300.00;
    $fees = [$this->cleaningFee];

    $result = $this->service->applyFees($baseAmount, $fees);

    $this->assertEquals(350.00, $result['total_amount']);
    $this->assertCount(1, $result['applied_fees']);
    $this->assertEquals(50.00, $result['applied_fees'][0]['amount']);
});

test('applies percentage fees correctly', function () {
    $baseAmount = 300.00;
    $fees = [$this->taxFee];

    $result = $this->service->applyFees($baseAmount, $fees);

    $this->assertEquals(345.00, $result['total_amount']); // 300 + 15%
    $this->assertCount(1, $result['applied_fees']);
    $this->assertEquals(45.00, $result['applied_fees'][0]['amount']);
});

test('applies multiple fees correctly', function () {
    $baseAmount = 300.00;
    $fees = [$this->cleaningFee, $this->taxFee];

    $result = $this->service->applyFees($baseAmount, $fees);

    $this->assertEquals(395.00, $result['total_amount']); // 300 + 50 + 45 (15% of 300)
    $this->assertCount(2, $result['applied_fees']);
});

test('handles partial payments calculation', function () {
    $totalAmount = 395.00;
    $paidAmount = 200.00;

    $result = $this->service->calculatePaymentStatus($totalAmount, $paidAmount);

    $this->assertEquals(195.00, $result['amount_due']);
    $this->assertEquals('partial', $result['payment_status']);
    $this->assertEquals(50.63, round($result['percentage_paid'], 2)); // 200/395*100
});

test('identifies fully paid status', function () {
    $totalAmount = 395.00;
    $paidAmount = 395.00;

    $result = $this->service->calculatePaymentStatus($totalAmount, $paidAmount);

    $this->assertEquals(0.00, $result['amount_due']);
    $this->assertEquals('paid', $result['payment_status']);
    $this->assertEquals(100.00, $result['percentage_paid']);
});

test('identifies overdue payments', function () {
    $totalAmount = 395.00;
    $paidAmount = 100.00;
    $dueDate = now()->subDays(5);

    $result = $this->service->calculatePaymentStatus($totalAmount, $paidAmount, $dueDate);

    $this->assertEquals('overdue', $result['payment_status']);
});

test('generates proper billing breakdown', function () {
    $booking = Booking::factory()->create([
        'hostel_id' => $this->hostel->id,
        'room_id' => $this->room->id,
        'check_in_date' => now(),
        'check_out_date' => now()->addDays(3),
        'billing_rate_type' => 'daily',
        'base_amount' => 300.00,
    ]);

    $breakdown = $this->service->generateBillingBreakdown($booking);

    $this->assertEquals(300.00, $breakdown['base_amount']);
    $this->assertEquals('3 days at $100.00/day', $breakdown['rate_description']);
    $this->assertArrayHasKey('fees', $breakdown);
});

test('handles free cancellation period', function () {
    $bookingDate = now();
    $cancellationDate = now()->addHours(12);
    $freeCancellationHours = 24;

    $result = $this->service->calculateCancellationFee(
        300.00,
        $bookingDate,
        $cancellationDate,
        $freeCancellationHours
    );

    $this->assertEquals(0.00, $result['cancellation_fee']);
    $this->assertTrue($result['free_cancellation']);
});

test('calculates cancellation fee after free period', function () {
    $bookingDate = now();
    $cancellationDate = now()->addHours(36); // 12 hours past free period
    $freeCancellationHours = 24;
    $cancellationFeePercentage = 0.2; // 20%

    $result = $this->service->calculateCancellationFee(
        300.00,
        $bookingDate,
        $cancellationDate,
        $freeCancellationHours,
        $cancellationFeePercentage
    );

    $this->assertEquals(60.00, $result['cancellation_fee']); // 20% of 300
    $this->assertFalse($result['free_cancellation']);
});
