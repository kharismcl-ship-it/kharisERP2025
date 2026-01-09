<?php

namespace Modules\Hostels\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Deposit;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Services\DepositManagementService;
use Tests\TestCase;

class DepositManagementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DepositManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(DepositManagementService::class);

        // Create required GL accounts for testing
        $this->createGLAccounts();
    }

    protected function createGLAccounts(): void
    {
        // Check if Finance module's Account model exists
        if (class_exists('Modules\\Finance\\Models\\Account')) {
            $accountClass = 'Modules\\Finance\\Models\\Account';

            // Create a company for the accounts
            $company = \Database\Factories\CompanyFactory::new()->create();

            // Create cash/bank account (1110)
            $accountClass::firstOrCreate(
                ['code' => '1110'],
                [
                    'name' => 'Cash and Bank',
                    'type' => 'asset',
                    'company_id' => $company->id,
                ]
            );

            // Create security deposits liability account (2310)
            $accountClass::firstOrCreate(
                ['code' => '2310'],
                [
                    'name' => 'Security Deposits Liability',
                    'type' => 'liability',
                    'company_id' => $company->id,
                ]
            );
        }
    }

    /** @test */
    public function it_can_create_deposit_for_booking()
    {
        $hostel = Hostel::factory()->create();
        $hostelOccupant = HostelOccupant::factory()->create();
        $booking = Booking::factory()->create([
            'hostel_id' => $hostel->id,
            'hostel_occupant_id' => $hostelOccupant->id,
        ]);

        $deposit = $this->service->createDepositForBooking($booking, 'security');

        $this->assertInstanceOf(Deposit::class, $deposit);
        $this->assertEquals('security', $deposit->deposit_type);
        $this->assertEquals(Deposit::STATUS_PENDING, $deposit->status);
        $this->assertEquals($booking->id, $deposit->booking_id);
        $this->assertEquals($hostel->id, $deposit->hostel_id);
        $this->assertEquals($hostelOccupant->id, $deposit->hostel_occupant_id);
    }

    /** @test */
    public function it_can_calculate_deposit_amount()
    {
        $hostel = Hostel::factory()->create([
            'deposit_percentage' => 20.0,
            'deposit_type' => 'percentage',
        ]);

        $bookingAmount = 1000.00;
        $expectedDeposit = 200.00; // 20% of 1000

        $calculatedAmount = $this->service->calculateDepositAmount($hostel, $bookingAmount);

        $this->assertEquals($expectedDeposit, $calculatedAmount);
    }

    /** @test */
    public function it_can_collect_deposit()
    {
        $hostel = Hostel::factory()->create();
        $hostelOccupant = HostelOccupant::factory()->create();
        $booking = Booking::factory()->create([
            'hostel_id' => $hostel->id,
            'hostel_occupant_id' => $hostelOccupant->id,
        ]);

        $deposit = Deposit::factory()->create([
            'hostel_occupant_id' => $hostelOccupant->id,
            'booking_id' => $booking->id,
            'hostel_id' => $hostel->id,
            'status' => Deposit::STATUS_PENDING,
            'amount' => 1000.00,
        ]);

        $result = $this->service->collectDeposit($deposit);

        $this->assertTrue($result);
        $this->assertEquals(Deposit::STATUS_COLLECTED, $deposit->fresh()->status);
        $this->assertNotNull($deposit->fresh()->collected_date);
    }

    /** @test */
    public function it_can_process_refund_with_deductions()
    {
        $hostel = Hostel::factory()->create();
        $hostelOccupant = HostelOccupant::factory()->create();
        $booking = Booking::factory()->create([
            'hostel_id' => $hostel->id,
            'hostel_occupant_id' => $hostelOccupant->id,
        ]);

        $deposit = Deposit::factory()->create([
            'hostel_occupant_id' => $hostelOccupant->id,
            'booking_id' => $booking->id,
            'hostel_id' => $hostel->id,
            'status' => Deposit::STATUS_COLLECTED,
            'amount' => 1000.00,
            'deductions' => 0.00,
        ]);

        $deductions = [
            ['amount' => 200.00, 'reason' => 'Cleaning fee'],
            ['amount' => 50.00, 'reason' => 'Utility balance'],
        ];

        $result = $this->service->processRefund($deposit, 750.00, $deductions, 'End of occupancy refund');

        $this->assertTrue($result);
        $refreshedDeposit = $deposit->fresh();
        $this->assertEquals(Deposit::STATUS_REFUNDED, $refreshedDeposit->status);
        $this->assertEquals(750.00, $refreshedDeposit->refund_amount);
        $this->assertEquals(250.00, $refreshedDeposit->deductions);
        $this->assertNotNull($refreshedDeposit->refunded_date);
    }

    /** @test */
    public function it_returns_false_when_refund_fails()
    {
        $hostel = Hostel::factory()->create();
        $hostelOccupant = HostelOccupant::factory()->create();
        $booking = Booking::factory()->create([
            'hostel_id' => $hostel->id,
            'hostel_occupant_id' => $hostelOccupant->id,
        ]);

        $deposit = Deposit::factory()->create([
            'hostel_occupant_id' => $hostelOccupant->id,
            'booking_id' => $booking->id,
            'hostel_id' => $hostel->id,
            'status' => Deposit::STATUS_PENDING, // Wrong status for refund
            'amount' => 1000.00,
        ]);

        $result = $this->service->processRefund($deposit, 1000.00, [], 'Test refund');

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_get_deposits_due_for_refund()
    {
        $hostel = Hostel::factory()->create();
        $hostelOccupant = HostelOccupant::factory()->create();
        $booking = Booking::factory()->create([
            'hostel_id' => $hostel->id,
            'hostel_occupant_id' => $hostelOccupant->id,
        ]);

        // Create deposits that should be due for refund
        $dueDeposit1 = Deposit::factory()->create([
            'hostel_occupant_id' => $hostelOccupant->id,
            'booking_id' => $booking->id,
            'hostel_id' => $hostel->id,
            'status' => Deposit::STATUS_COLLECTED,
            'collected_date' => now()->subMonths(2),
        ]);

        $dueDeposit2 = Deposit::factory()->create([
            'hostel_occupant_id' => $hostelOccupant->id,
            'booking_id' => $booking->id,
            'hostel_id' => $hostel->id,
            'status' => Deposit::STATUS_COLLECTED,
            'collected_date' => now()->subMonths(3),
        ]);

        // Create a deposit that shouldn't be due (too recent)
        $recentDeposit = Deposit::factory()->create([
            'hostel_occupant_id' => $hostelOccupant->id,
            'booking_id' => $booking->id,
            'hostel_id' => $hostel->id,
            'status' => Deposit::STATUS_COLLECTED,
            'collected_date' => now()->subDays(10),
        ]);

        $dueDeposits = $this->service->getDepositsDueForRefund();

        $this->assertCount(2, $dueDeposits);
        $this->assertTrue($dueDeposits->contains('id', $dueDeposit1->id));
        $this->assertTrue($dueDeposits->contains('id', $dueDeposit2->id));
        $this->assertFalse($dueDeposits->contains('id', $recentDeposit->id));
    }

    /** @test */
    public function it_can_process_auto_refunds()
    {
        $hostel = Hostel::factory()->create();
        $hostelOccupant = HostelOccupant::factory()->create();
        $booking = Booking::factory()->create([
            'hostel_id' => $hostel->id,
            'hostel_occupant_id' => $hostelOccupant->id,
        ]);

        // Create deposits that should be auto-refunded
        $dueDeposit1 = Deposit::factory()->create([
            'hostel_occupant_id' => $hostelOccupant->id,
            'booking_id' => $booking->id,
            'hostel_id' => $hostel->id,
            'status' => Deposit::STATUS_COLLECTED,
            'collected_date' => now()->subMonths(2),
            'amount' => 500.00,
        ]);

        $dueDeposit2 = Deposit::factory()->create([
            'hostel_occupant_id' => $hostelOccupant->id,
            'booking_id' => $booking->id,
            'hostel_id' => $hostel->id,
            'status' => Deposit::STATUS_COLLECTED,
            'collected_date' => now()->subMonths(3),
            'amount' => 1000.00,
        ]);

        $processedCount = $this->service->processAutoRefunds();

        $this->assertEquals(2, $processedCount);
        $this->assertEquals(Deposit::STATUS_REFUNDED, $dueDeposit1->fresh()->status);
        $this->assertEquals(Deposit::STATUS_REFUNDED, $dueDeposit2->fresh()->status);
    }
}
