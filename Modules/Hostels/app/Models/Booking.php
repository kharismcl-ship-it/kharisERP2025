<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Hostels\Database\factories\BookingFactory;
use Modules\Hostels\Events\HostelOccupantCheckedIn;
use Modules\PaymentsChannel\Services\PaymentService;
use Modules\PaymentsChannel\Traits\HasPayments;

/**
 * Booking Model
 *
 * Represents a hostel booking made by a hostel occupant.
 *
 * Concurrency Control:
 * - Business rule: Only one active booking per bed is allowed
 * - Active statuses: pending,awaiting_payment, confirmed, checked_in
 * - Inactive statuses: cancelled, checked_out,no_show
 * - When creating a booking, check for existing active bookings for the same bed
 */
class Booking extends Model
{
    use HasEvents, HasFactory, HasPayments;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hostel_id',
        'room_id',
        'bed_id',
        'hostel_occupant_id',
        'hostel_occupant_user_id',
        'booking_reference',
        'booking_type',
        'academic_year',
        'semester',
        'number_of_nights',
        'check_in_date',
        'check_out_date',
        'expected_check_out_date',
        'actual_check_in_at',
        'actual_check_out_at',
        'status',
        'total_amount',
        'amount_paid',
        'balance_amount',
        'deposit_amount',
        'deposit_paid',
        'deposit_balance',
        'deposit_refunded',
        'payment_status',
        'hold_expires_at',
        'accepted_terms_at',
        'channel',
        'notes',
        // Guest information for hostel occupant creation
        'guest_first_name',
        'guest_last_name',
        'guest_other_names',
        'guest_full_name',
        'guest_gender',
        'guest_dob',
        'guest_phone',
        'guest_alt_phone',
        'guest_email',
        'guest_national_id_number',
        'guest_student_id',
        'guest_institution',
        'guest_guardian_name',
        'guest_guardian_phone',
        'guest_guardian_email',
        'guest_address',
        'guest_emergency_contact_name',
        'guest_emergency_contact_phone',
        'id_card_front_photo',
        'id_card_back_photo',
        'profile_photo',
        'refund_processed_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'actual_check_in_at' => 'datetime',
        'actual_check_out_at' => 'datetime',
        'hold_expires_at' => 'datetime',
        'accepted_terms_at' => 'datetime',
        'total_amount' => 'float',
        'amount_paid' => 'float',
        'balance_amount' => 'float',
        'deposit_amount' => 'float',
        'deposit_paid' => 'float',
        'deposit_balance' => 'float',
        'deposit_refunded' => 'float',
        'guest_dob' => 'date',
        'refund_processed_at' => 'datetime',
    ];

    // Define active statuses for concurrency control
    public const ACTIVE_STATUSES = ['pending', 'awaiting_payment', 'confirmed', 'checked_in'];

    public const INACTIVE_STATUSES = ['cancelled', 'checked_out', 'no_show'];

    // Payment status constants
    public const PAYMENT_STATUS_PENDING = 'pending';

    public const PAYMENT_STATUS_PARTIAL = 'partial';

    public const PAYMENT_STATUS_DEPOSIT_PAID = 'deposit_paid';

    public const PAYMENT_STATUS_FULLY_PAID = 'fully_paid';

    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    protected static function newFactory()
    {
        return new BookingFactory;
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    public function hostelOccupant()
    {
        return $this->belongsTo(HostelOccupant::class);
    }

    public function hostelOccupantUser()
    {
        return $this->belongsTo(HostelOccupantUser::class);
    }

    public function charges()
    {
        return $this->hasMany(BookingCharge::class);
    }

    /**
     * Scope a query to only includeactive bookings.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    /**
     * Scope a query to only include inactive bookings.
     */
    public function scopeInactive($query)
    {
        return $query->whereIn('status', self::INACTIVE_STATUSES);
    }

    /**
     * Check in the guest and create hostel occupant record.
     */
    public function checkIn()
    {
        // Create hostel occupant if not already created
        if (! $this->hostel_occupant_id) {
            $this->createHostelOccupantFromGuestInfo();
        }

        // Update booking status to checked in
        $this->update([
            'status' => 'checked_in',
            'actual_check_in_at' => now(),
        ]);

        // Update bed status to occupied
        if ($this->bed) {
            $this->bed->update(['status' => 'occupied']);
        }

        // Fire the hostel occupant checked in event
        event(new HostelOccupantCheckedIn($this));

        return $this;
    }

    /**
     * Create a hostel occupant record from guest information.
     * For returning hostel occupants, reactivates existing accounts instead of creating new ones.
     */
    protected function createHostelOccupantFromGuestInfo()
    {
        // First, check if hostel occupant already exists using unique identifiers
        $existingHostelOccupant = HostelOccupant::where(function ($query) {
            $query->where('email', $this->guest_email)
                ->orWhere('phone', $this->guest_phone)
                ->orWhere('student_id', $this->guest_student_id)
                ->orWhere('national_id_number', $this->guest_national_id_number);
        })->first();

        if ($existingHostelOccupant) {
            $hostelOccupant = $existingHostelOccupant;

            // Update hostel occupant information with latest guest details (preserve existing values if new ones are empty)
            $hostelOccupant->update([
                'first_name' => $this->guest_first_name ?? $hostelOccupant->first_name,
                'last_name' => $this->guest_last_name ?? $hostelOccupant->last_name,
                'other_names' => $this->guest_other_names ?? $hostelOccupant->other_names,
                'full_name' => $this->guest_full_name ?? $hostelOccupant->full_name,
                'gender' => $this->guest_gender ?? $hostelOccupant->gender,
                'dob' => $this->guest_dob ?? $hostelOccupant->dob,
                'phone' => $this->guest_phone ?? $hostelOccupant->phone,
                'alt_phone' => $this->guest_alt_phone ?? $hostelOccupant->alt_phone,
                'email' => $this->guest_email ?? $hostelOccupant->email,
                'national_id_number' => $this->guest_national_id_number ?? $hostelOccupant->national_id_number,
                'student_id' => $this->guest_student_id ?? $hostelOccupant->student_id,
                'institution' => $this->guest_institution ?? $hostelOccupant->institution,
                'guardian_name' => $this->guest_guardian_name ?? $hostelOccupant->guardian_name,
                'guardian_phone' => $this->guardian_phone ?? $hostelOccupant->guardian_phone,
                'guardian_email' => $this->guest_guardian_email ?? $hostelOccupant->guardian_email,
                'address' => $this->guest_address ?? $hostelOccupant->address,
                'emergency_contact_name' => $this->guest_emergency_contact_name ?? $hostelOccupant->emergency_contact_name,
                'emergency_contact_phone' => $this->guest_emergency_contact_phone ?? $hostelOccupant->emergency_contact_phone,
                'id_card_front_photo' => $this->id_card_front_photo ?? $hostelOccupant->id_card_front_photo,
                'id_card_back_photo' => $this->id_card_back_photo ?? $hostelOccupant->id_card_back_photo,
                'profile_photo' => $this->profile_photo ?? $hostelOccupant->profile_photo,
                'status' => 'active', // Ensure hostel occupant is active
            ]);
        } else {
            // Create new hostel occupant for first-time guests
            $hostelOccupant = HostelOccupant::create([
                'hostel_id' => $this->hostel_id,
                'first_name' => $this->guest_first_name ?? 'Guest',
                'last_name' => $this->guest_last_name ?? $this->id,
                'other_names' => $this->guest_other_names,
                'full_name' => $this->guest_full_name ?? ($this->guest_first_name.' '.($this->guest_last_name ?? $this->id)),
                'gender' => $this->guest_gender,
                'dob' => $this->guest_dob,
                'phone' => $this->guest_phone,
                'alt_phone' => $this->guest_alt_phone,
                'email' => $this->guest_email,
                'national_id_number' => $this->guest_national_id_number,
                'student_id' => $this->guest_student_id,
                'institution' => $this->guest_institution,
                'guardian_name' => $this->guest_guardian_name,
                'guardian_phone' => $this->guest_guardian_phone,
                'guardian_email' => $this->guest_guardian_email,
                'address' => $this->guest_address,
                'emergency_contact_name' => $this->guest_emergency_contact_name,
                'emergency_contact_phone' => $this->guest_emergency_contact_phone,
                'id_card_front_photo' => $this->id_card_front_photo,
                'id_card_back_photo' => $this->id_card_back_photo,
                'profile_photo' => $this->profile_photo,
                'status' => 'active',
            ]);
        }

        // Handle HostelOccupantUser - REACTIVATE EXISTING OR CREATE NEW
        if ($this->guest_email) {
            $existingUser = HostelOccupantUser::where('email', $this->guest_email)->first();

            if ($existingUser) {
                // Reactivate existing user account
                $hostelOccupantUser = $existingUser;

                // Ensure the user is linked to the correct hostel occupant
                if ($existingUser->hostel_occupant_id !== $hostelOccupant->id) {
                    $existingUser->update(['hostel_occupant_id' => $hostelOccupant->id]);
                }

                // Send "welcome back" email instead of new credentials
                event(new \Modules\Hostels\Events\HostelOccupantUserReactivated($hostelOccupantUser));

            } else {
                // Create new HostelOccupantUser for first-time users
                $password = \Illuminate\Support\Str::random(12); // Secure random password
                $hostelOccupantUser = \Modules\Hostels\Models\HostelOccupantUser::create([
                    'hostel_occupant_id' => $hostelOccupant->id,
                    'email' => $this->guest_email,
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                ]);

                // Fire event to send welcome email with credentials
                event(new \Modules\Hostels\Events\HostelOccupantUserCreated($hostelOccupantUser, $password));
            }
        }

        // Associate hostel occupant with booking
        $this->updateQuietly(['hostel_occupant_id' => $hostelOccupant->id]);

        return $hostelOccupant;
    }

    /**
     * Get the payment description for this booking.
     */
    public function getPaymentDescription(): ?string
    {
        return "Hostel Booking #{$this->booking_reference}";
    }

    /**
     * Get the customer name for payment purposes.
     */
    public function getPaymentCustomerName(): ?string
    {
        return $this->hostelOccupant->full_name ?? $this->guest_full_name ?? null;
    }

    /**
     * Getthe customer email forpayment purposes.
     */
    public function getPaymentCustomerEmail(): ?string
    {
        return $this->hostelOccupant->email ?? $this->guest_email ?? null;
    }

    /**
     * Get the customer phone for payment purposes.
     */
    public function getPaymentCustomerPhone(): ?string
    {
        return $this->hostelOccupant->phone ?? $this->guest_phone ?? null;
    }

    /**
     * Get the payment amount for this booking.
     */
    public function getPaymentAmount(): float
    {
        return $this->total_amount;
    }

    /**
     * Get the payment currency for this booking.
     */
    public function getPaymentCurrency(): string
    {
        return 'GHS'; // Ghana Cedis
    }

    /**
     * Check if the booking hold has expired.
     */
    public function isHoldExpired(): bool
    {
        return $this->hold_expires_at && $this->hold_expires_at->isPast();
    }

    /**
     * Release the bed if the booking hold has expired.
     */
    public function releaseBedIfHoldExpired(): bool
    {
        if ($this->isHoldExpired() && $this->bed_id && $this->bed->status === 'reserved') {
            $this->bed->update(['status' => 'available']);
            $this->update(['status' => 'cancelled', 'notes' => 'Booking cancelled due to expired hold period']);

            return true;
        }

        return false;
    }

    /**
     * Set the hold expiry time based on hostel settings.
     */
    public function setHoldExpiry(): void
    {
        if ($this->hostel && $this->status === 'awaiting_payment') {
            $holdMinutes = $this->hostel->reservation_hold_minutes ?? 30;
            $this->update(['hold_expires_at' => now()->addMinutes($holdMinutes)]);
        }
    }

    /**
     * Scope a query to only include bookings with expired holds.
     */
    public function scopeWithExpiredHold($query)
    {
        return $query->where('hold_expires_at', '<', now())
            ->whereIn('status', ['awaiting_payment', 'pending']);
    }

    /**
     * Calculate and set the required deposit amount based on hostel configuration
     */
    public function calculateAndSetDeposit(): float
    {
        if (! $this->hostel) {
            return 0.0;
        }

        $depositAmount = $this->hostel->calculateDepositAmount($this->total_amount);

        $this->update([
            'deposit_amount' => $depositAmount,
            'deposit_balance' => $depositAmount,
        ]);

        return $depositAmount;
    }

    /**
     * Apply a payment to the booking
     */
    public function applyPayment(float $amount, string $paymentType = 'booking'): array
    {
        $remainingAmount = $amount;
        $appliedToDeposit = 0.0;
        $appliedToBalance = 0.0;

        // First apply to deposit if there's an outstanding deposit balance
        if ($this->deposit_balance > 0 && $remainingAmount > 0) {
            $appliedToDeposit = min($remainingAmount, $this->deposit_balance);
            $this->deposit_paid += $appliedToDeposit;
            $this->deposit_balance -= $appliedToDeposit;
            $remainingAmount -= $appliedToDeposit;
        }

        // Then apply to the main balance
        if ($remainingAmount > 0 && $this->balance_amount > 0) {
            $appliedToBalance = min($remainingAmount, $this->balance_amount);
            $this->amount_paid += $appliedToBalance;
            $this->balance_amount -= $appliedToBalance;
            $remainingAmount -= $appliedToBalance;
        }

        // Update payment status
        $this->updatePaymentStatus();

        $this->save();

        return [
            'applied_to_deposit' => $appliedToDeposit,
            'applied_to_balance' => $appliedToBalance,
            'remaining_amount' => $remainingAmount,
        ];
    }

    /**
     * Update the payment status based on current payment amounts
     */
    public function updatePaymentStatus(): void
    {
        $newStatus = self::PAYMENT_STATUS_PENDING;

        if ($this->amount_paid >= $this->total_amount) {
            $newStatus = self::PAYMENT_STATUS_FULLY_PAID;
        } elseif ($this->deposit_paid >= $this->deposit_amount && $this->deposit_amount > 0) {
            $newStatus = self::PAYMENT_STATUS_DEPOSIT_PAID;
        } elseif ($this->amount_paid > 0) {
            $newStatus = self::PAYMENT_STATUS_PARTIAL;
        }

        $this->payment_status = $newStatus;
    }

    /**
     * Check if the minimum payment requirement is met
     */
    public function meetsMinimumPaymentRequirement(): bool
    {
        if (! $this->hostel) {
            return false;
        }

        $totalPaid = $this->amount_paid + $this->deposit_paid;
        $minRequired = $this->hostel->getMinimumPaymentAmount($this->total_amount);

        return $totalPaid >= $minRequired;
    }

    /**
     * Check if deposit is fully paid
     */
    public function isDepositFullyPaid(): bool
    {
        return $this->deposit_paid >= $this->deposit_amount;
    }

    /**
     * Check if booking is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->amount_paid >= $this->total_amount;
    }

    /**
     * Refund deposit amount
     */
    public function refundDeposit(float $amount): bool
    {
        if ($amount > $this->deposit_paid - $this->deposit_refunded) {
            return false;
        }

        $this->deposit_refunded += $amount;
        $this->save();

        return true;
    }

    /**
     * Get the outstanding amount (balance + deposit balance)
     */
    public function getOutstandingAmount(): float
    {
        return $this->balance_amount + $this->deposit_balance;
    }

    /**
     * Get the total paid amount (including deposit)
     */
    public function getTotalPaidAmount(): float
    {
        return $this->amount_paid + $this->deposit_paid;
    }

    /**
     * Cancel booking and process refund according to policy
     */
    public function cancelBooking(?BookingCancellationPolicy $policy = null): array
    {
        if (! in_array($this->status, ['pending', 'awaiting_payment', 'confirmed'])) {
            throw new \Exception('Cannot cancel booking in current status: '.$this->status);
        }

        // Get applicable cancellation policy
        $policy = $policy ?? $this->getApplicableCancellationPolicy();

        // Check if cancellation is allowed
        if (! $policy->isCancellationAllowed($this->check_in_date)) {
            throw new \Exception('Cancellation not allowed per policy');
        }

        // Calculate refund amount
        $refundAmount = $policy->calculateRefundAmount(
            $this->getTotalPaidAmount(),
            $this->getTotalPaidAmount()
        );

        // Update booking status
        $this->update(['status' => 'cancelled']);

        // Process refund if applicable
        $refundProcessed = false;
        if ($refundAmount > 0) {
            $refundProcessed = $this->processRefund($refundAmount);
        }

        // Release the bed if assigned
        if ($this->bed_id) {
            $this->bed->update(['status' => 'available']);
        }

        return [
            'success' => true,
            'refund_amount' => $refundAmount,
            'refund_processed' => $refundProcessed,
            'policy_applied' => $policy->name,
        ];
    }

    /**
     * Get applicable cancellation policy for this booking
     */
    public function getApplicableCancellationPolicy(): BookingCancellationPolicy
    {
        // First try hostel-specific policies
        $policy = BookingCancellationPolicy::where('hostel_id', $this->hostel_id)
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->first();

        // Fall back to default policies
        if (! $policy) {
            $policy = BookingCancellationPolicy::whereNull('hostel_id')
                ->where('is_active', true)
                ->where('is_default', true)
                ->firstOrFail();
        }

        return $policy;
    }

    /**
     * Process refund through payment channel
     */
    public function processRefund(float $amount): bool
    {
        // Get the latest successful payment intent
        $payIntent = $this->payIntents()
            ->where('status', 'successful')
            ->latest()
            ->first();

        if (! $payIntent) {
            // No online payment found, mark as manual refund
            $this->deposit_refunded += $amount;
            $this->save();

            return true;
        }

        try {
            // Process refund through PaymentsChannel service
            $paymentService = app(PaymentService::class);
            $refundResult = $paymentService->refund($payIntent, (int) ($amount * 100), 'Hostel booking cancellation');

            if ($refundResult['success']) {
                // Update booking refund tracking
                $this->deposit_refunded += $amount;
                $this->refund_processed_at = now();
                $this->save();

                // Process finance reconciliation for the refund
                $this->processFinanceReconciliation($amount);

                return true;
            } else {
                // Refund failed through gateway
                Log::error('Refund processing failed: '.($refundResult['message'] ?? 'Unknown error'), [
                    'booking_id' => $this->id,
                    'pay_intent_id' => $payIntent->id,
                    'amount' => $amount,
                    'refund_result' => $refundResult,
                ]);

                return false;
            }
        } catch (\Exception $e) {
            // Log refund failure
            Log::error('Refund processing failed: '.$e->getMessage(), [
                'booking_id' => $this->id,
                'pay_intent_id' => $payIntent->id,
                'amount' => $amount,
            ]);

            return false;
        }
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        if (! in_array($this->status, ['pending', 'awaiting_payment', 'confirmed'])) {
            return false;
        }

        try {
            $policy = $this->getApplicableCancellationPolicy();

            return $policy->isCancellationAllowed($this->check_in_date);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get estimated refund amount if cancelled now
     */
    public function getEstimatedRefundAmount(): float
    {
        if (! $this->canBeCancelled()) {
            return 0.0;
        }

        try {
            $policy = $this->getApplicableCancellationPolicy();

            return $policy->calculateRefundAmount(
                $this->getTotalPaidAmount(),
                $this->getTotalPaidAmount()
            );
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Process finance reconciliation for refunds
     * This method integrates with the Finance module to create proper accounting entries
     * for refund transactions
     */
    protected function processFinanceReconciliation(float $refundAmount): void
    {
        // Check if Finance module is available and integration service exists
        if (! class_exists('\\Modules\\Finance\\App\\Services\\EnhancedIntegrationService')) {
            Log::warning('Finance module integration service not available for refund reconciliation');

            return;
        }

        try {
            $integrationService = app('\\Modules\\Finance\\App\\Services\\EnhancedIntegrationService');

            // Process the refund through the finance integration service
            $payment = $integrationService->processBookingRefund(
                $this,
                $refundAmount,
                'Booking cancellation refund'
            );

            if ($payment) {
                Log::info('Finance reconciliation completed for booking refund', [
                    'booking_id' => $this->id,
                    'refund_amount' => $refundAmount,
                    'payment_id' => $payment->id,
                ]);
            } else {
                Log::warning('Finance reconciliation failed for booking refund - no invoice found', [
                    'booking_id' => $this->id,
                    'refund_amount' => $refundAmount,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Finance reconciliation error for booking refund: '.$e->getMessage(), [
                'booking_id' => $this->id,
                'refund_amount' => $refundAmount,
                'exception' => $e,
            ]);
        }
    }

    /**
     * Approve a pending booking
     */
    public function approveBooking(): array
    {
        if ($this->status !== 'pending_approval') {
            throw new \Exception('Cannot approve booking in current status: '.$this->status);
        }

        DB::transaction(function () {
            // Update booking status
            $this->update([
                'status' => 'confirmed',
                'payment_status' => 'pending_payment',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            // Create hostel occupant from guest info
            $this->createHostelOccupantFromGuestInfo();

            // Update bed status if assigned
            if ($this->bed_id) {
                $this->bed->update(['status' => 'reserved']);
            }
        });

        return [
            'success' => true,
            'message' => 'Booking approved successfully',
            'hostel_occupant_created' => ! is_null($this->hostel_occupant_id),
        ];
    }

    /**
     * Reject a pending booking
     */
    public function rejectBooking(string $reason): array
    {
        if ($this->status !== 'pending_approval') {
            throw new \Exception('Cannot reject booking in current status: '.$this->status);
        }

        DB::transaction(function () use ($reason) {
            // Update booking status
            $this->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejected_by' => Auth::id(),
                'rejection_reason' => $reason,
            ]);

            // Release the bed if assigned
            if ($this->bed_id) {
                $this->bed->update(['status' => 'available']);
            }
        });

        return [
            'success' => true,
            'message' => 'Booking rejected successfully',
            'bed_released' => ! is_null($this->bed_id),
        ];
    }

    /**
     * Check if booking is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Check if booking can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Check if booking can be rejected
     */
    public function canBeRejected(): bool
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Get the approval status badge color
     */
    public function getApprovalStatusColor(): string
    {
        return match ($this->status) {
            'pending_approval' => 'warning',
            'confirmed' => 'success',
            'rejected' => 'danger',
            default => 'gray'
        };
    }

    /**
     * Scope: Only pending approval bookings
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Scope: Only approved bookings
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope: Only rejected bookings
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
