<?php

use Illuminate\Support\Facades\Mockery;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Room;
use Modules\Hostels\Services\FinanceIntegrationAdapter;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Mock the FinanceService dependency
    $this->financeService = Mockery::mock('Modules\Finance\Services\FinanceService');
    $this->adapter = new FinanceIntegrationAdapter($this->financeService);

    $this->hostel = Hostel::factory()->create();
    $this->room = Room::factory()->create(['hostel_id' => $this->hostel->id]);
    $this->occupant = HostelOccupant::factory()->create();

    $this->booking = Booking::factory()->create([
        'hostel_id' => $this->hostel->id,
        'room_id' => $this->room->id,
        'hostel_occupant_id' => $this->occupant->id,
        'base_amount' => 300.00,
        'total_amount' => 350.00,
        'status' => 'confirmed',
    ]);
});

afterEach(function () {
    Mockery::close();
});

test('creates booking invoice successfully', function () {
    $invoiceData = [
        'id' => 1,
        'invoice_number' => 'INV-001',
        'amount' => 350.00,
        'status' => 'draft',
    ];

    $this->financeService->shouldReceive('createInvoice')
        ->once()
        ->with([
            'company_id' => $this->hostel->company_id,
            'customer_name' => $this->occupant->full_name,
            'module' => 'hostels',
            'entity_type' => 'booking',
            'entity_id' => $this->booking->id,
            'amount' => 350.00,
            'description' => 'Hostel booking for '.$this->occupant->full_name,
        ])
        ->andReturn((object) $invoiceData);

    $result = $this->adapter->createBookingInvoice($this->booking);

    $this->assertEquals(1, $result->id);
    $this->assertEquals('INV-001', $result->invoice_number);
});

test('handles finance service failure gracefully', function () {
    $this->financeService->shouldReceive('createInvoice')
        ->once()
        ->andThrow(new \Exception('Finance service unavailable'));

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Failed to create invoice for booking: Finance service unavailable');

    $this->adapter->createBookingInvoice($this->booking);
});

test('gets booking payment status successfully', function () {
    $paymentStatus = [
        'status' => 'partial',
        'amount_paid' => 150.00,
        'amount_due' => 200.00,
    ];

    $this->financeService->shouldReceive('getInvoiceStatusByEntity')
        ->once()
        ->with('hostels', 'booking', $this->booking->id)
        ->andReturn($paymentStatus);

    $result = $this->adapter->getBookingPaymentStatus($this->booking);

    $this->assertEquals('partial', $result['status']);
    $this->assertEquals(150.00, $result['amount_paid']);
    $this->assertEquals(200.00, $result['amount_due']);
});

test('returns unpaid status when no invoice exists', function () {
    $this->financeService->shouldReceive('getInvoiceStatusByEntity')
        ->once()
        ->with('hostels', 'booking', $this->booking->id)
        ->andReturn(null);

    $result = $this->adapter->getBookingPaymentStatus($this->booking);

    $this->assertEquals('unpaid', $result['status']);
    $this->assertEquals(0.00, $result['amount_paid']);
    $this->assertEquals(350.00, $result['amount_due']);
});

test('marks booking as paid successfully', function () {
    $paymentData = [
        'amount' => 350.00,
        'payment_method' => 'bank_transfer',
        'reference_number' => 'PAY-001',
        'paid_at' => now(),
    ];

    $this->financeService->shouldReceive('recordPaymentForEntity')
        ->once()
        ->with('hostels', 'booking', $this->booking->id, $paymentData)
        ->andReturn(true);

    $result = $this->adapter->markBookingAsPaid($this->booking, $paymentData);

    $this->assertTrue($result);
});

test('determines if booking requires payment', function () {
    // Test booking that requires payment
    $this->assertTrue($this->adapter->requiresPayment($this->booking));

    // Test booking with zero amount (should not require payment)
    $freeBooking = Booking::factory()->create([
        'hostel_id' => $this->hostel->id,
        'room_id' => $this->room->id,
        'hostel_occupant_id' => $this->occupant->id,
        'base_amount' => 0.00,
        'total_amount' => 0.00,
        'status' => 'confirmed',
    ]);

    $this->assertFalse($this->adapter->requiresPayment($freeBooking));

    // Test cancelled booking (should not require payment)
    $cancelledBooking = Booking::factory()->create([
        'hostel_id' => $this->hostel->id,
        'room_id' => $this->room->id,
        'hostel_occupant_id' => $this->occupant->id,
        'base_amount' => 300.00,
        'total_amount' => 350.00,
        'status' => 'cancelled',
    ]);

    $this->assertFalse($this->adapter->requiresPayment($cancelledBooking));
});

test('handles guest bookings without occupant', function () {
    $guestBooking = Booking::factory()->create([
        'hostel_id' => $this->hostel->id,
        'room_id' => $this->room->id,
        'hostel_occupant_id' => null,
        'guest_full_name' => 'Guest User',
        'base_amount' => 300.00,
        'total_amount' => 350.00,
        'status' => 'confirmed',
    ]);

    $invoiceData = [
        'id' => 2,
        'invoice_number' => 'INV-002',
        'amount' => 350.00,
        'status' => 'draft',
    ];

    $this->financeService->shouldReceive('createInvoice')
        ->once()
        ->with([
            'company_id' => $this->hostel->company_id,
            'customer_name' => 'Guest User',
            'module' => 'hostels',
            'entity_type' => 'booking',
            'entity_id' => $guestBooking->id,
            'amount' => 350.00,
            'description' => 'Hostel booking for Guest User',
        ])
        ->andReturn((object) $invoiceData);

    $result = $this->adapter->createBookingInvoice($guestBooking);

    $this->assertEquals(2, $result->id);
});

test('handles booking with company context', function () {
    $hostelWithCompany = Hostel::factory()->create(['company_id' => 123]);
    $roomWithCompany = Room::factory()->create(['hostel_id' => $hostelWithCompany->id]);
    $bookingWithCompany = Booking::factory()->create([
        'hostel_id' => $hostelWithCompany->id,
        'room_id' => $roomWithCompany->id,
        'hostel_occupant_id' => $this->occupant->id,
        'base_amount' => 300.00,
        'total_amount' => 350.00,
        'status' => 'confirmed',
    ]);

    $invoiceData = [
        'id' => 3,
        'invoice_number' => 'INV-003',
        'amount' => 350.00,
        'status' => 'draft',
    ];

    $this->financeService->shouldReceive('createInvoice')
        ->once()
        ->with([
            'company_id' => 123,
            'customer_name' => $this->occupant->full_name,
            'module' => 'hostels',
            'entity_type' => 'booking',
            'entity_id' => $bookingWithCompany->id,
            'amount' => 350.00,
            'description' => 'Hostel booking for '.$this->occupant->full_name,
        ])
        ->andReturn((object) $invoiceData);

    $result = $this->adapter->createBookingInvoice($bookingWithCompany);

    $this->assertEquals(3, $result->id);
});
