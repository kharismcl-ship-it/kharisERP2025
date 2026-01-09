<?php

namespace Modules\Hostels\Models;

use App\Enums\GhanaRegions;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;
use Modules\Hostels\Database\factories\HostelFactory;
use Modules\HR\Models\EmployeeCompanyAssignment;
use Modules\HR\Models\HostelStaffAssignment;

class Hostel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'code',
        'location',
        'city',
        'region',
        'latitude',
        'longitude',
        'country',
        'contact_phone',
        'contact_email',
        'contact_name',
        'photo',
        'description',
        'capacity',
        'gender_policy',
        'check_in_time_default',
        'check_out_time_default',
        'status',
        'require_payment_before_checkin',
        'reservation_hold_minutes',
        'require_deposit',
        'deposit_amount',
        'deposit_percentage',
        'deposit_type',
        'deposit_refund_policy',
        'allow_partial_payments',
        'partial_payment_min_percentage',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'location' => 'array',
        'latitude' => 'decimal:9,6',
        'longitude' => 'decimal:9,6',
        'country' => 'string',
        'region' => 'string', // Keep as string to support regions for any country
        'require_payment_before_checkin' => 'boolean',
        'require_deposit' => 'boolean',
        'deposit_amount' => 'float',
        'deposit_percentage' => 'float',
        'allow_partial_payments' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hostel) {
            if (empty($hostel->slug)) {
                $hostel->slug = Str::slug($hostel->name);
            }
        });

        static::updating(function ($hostel) {
            if (empty($hostel->slug)) {
                $hostel->slug = Str::slug($hostel->name);
            }
        });
    }

    protected static function newFactory(): HostelFactory
    {
        return HostelFactory::new();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function blocks()
    {
        return $this->hasMany(HostelBlock::class);
    }

    public function floors()
    {
        return $this->hasMany(HostelFloor::class);
    }

    public function hostelOccupants()
    {
        return $this->hasMany(HostelOccupant::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function feeTypes()
    {
        return $this->hasMany(FeeType::class);
    }

    public function hostelCharges()
    {
        return $this->hasMany(HostelCharge::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function visitorLogs()
    {
        return $this->hasMany(VisitorLog::class);
    }

    public function whatsappGroups()
    {
        return $this->hasMany(HostelWhatsAppGroup::class);
    }

    /**
     * Get the beds for the hostel through rooms.
     */
    public function beds(): HasManyThrough
    {
        return $this->hasManyThrough(Bed::class, Room::class);
    }

    /**
     * Get the staff assignments for this hostel.
     */
    public function staffAssignments()
    {
        //        return $this->hasMany(EmployeeCompanyAssignment::class);
        return $this->hasMany(HostelStaffAssignment::class);
    }

    /**
     * Get the company assignments for this hostel's company.
     */
    public function employeeCompanyAssignments()
    {
        return $this->hasMany(EmployeeCompanyAssignment::class, 'company_id', 'company_id');
    }

    /**
     * Get the Ghana region enum if the country is Ghana
     *
     * @return GhanaRegions|null
     */
    //    public function getGhanaRegionAttribute(): ?GhanaRegions
    //    {
    //        if ($this->country && $this->country->value === 'Ghana' && $this->region) {
    //            return GhanaRegions::fromName($this->region);
    //        }
    //
    //        return null;
    //    }

    /**
     * Calculate the required deposit amount for a given booking total
     */
    public function calculateDepositAmount(float $bookingTotal): float
    {
        if (! $this->require_deposit) {
            return 0.0;
        }

        if ($this->deposit_type === 'fixed' && $this->deposit_amount) {
            return min($this->deposit_amount, $bookingTotal);
        }

        if ($this->deposit_type === 'percentage' && $this->deposit_percentage) {
            return $bookingTotal * ($this->deposit_percentage / 100);
        }

        return 0.0;
    }

    /**
     * Validate if a payment amount meets the minimum deposit requirement
     */
    public function validateDepositPayment(float $bookingTotal, float $paymentAmount): bool
    {
        $requiredDeposit = $this->calculateDepositAmount($bookingTotal);

        if ($requiredDeposit > 0 && $paymentAmount < $requiredDeposit) {
            return false;
        }

        return true;
    }

    /**
     * Validate if a partial payment meets the minimum percentage requirement
     */
    public function validatePartialPayment(float $bookingTotal, float $paymentAmount): bool
    {
        if (! $this->allow_partial_payments) {
            return $paymentAmount >= $bookingTotal;
        }

        if ($this->partial_payment_min_percentage > 0) {
            $minAmount = $bookingTotal * ($this->partial_payment_min_percentage / 100);

            return $paymentAmount >= $minAmount;
        }

        return true;
    }

    /**
     * Get the minimum payment amount required for a booking
     */
    public function getMinimumPaymentAmount(float $bookingTotal): float
    {
        if ($this->require_deposit && $this->deposit_type && $this->deposit_amount > 0) {
            return $this->calculateDepositAmount($bookingTotal);
        }

        if (! $this->allow_partial_payments) {
            return $bookingTotal;
        }

        if ($this->partial_payment_min_percentage > 0) {
            return $bookingTotal * ($this->partial_payment_min_percentage / 100);
        }

        return 0.0;
    }

    /**
     * Check if full payment is required before check-in
     */
    public function requiresFullPaymentBeforeCheckin(): bool
    {
        return $this->require_payment_before_checkin && ! $this->allow_partial_payments;
    }

    /**
     * Get validation rules for deposit configuration
     */
    public static function getDepositValidationRules(): array
    {
        return [
            'require_deposit' => 'boolean',
            'deposit_amount' => 'nullable|decimal:0,2|min:0',
            'deposit_percentage' => 'nullable|decimal:0,2|min:0|max:100',
            'deposit_type' => 'required_if:require_deposit,true|in:fixed,percentage',
            'deposit_refund_policy' => 'nullable|string|max:1000',
            'allow_partial_payments' => 'boolean',
            'partial_payment_min_percentage' => 'nullable|integer|min:0|max:100',
        ];
    }

    /**
     * Get the pricing policies for this hostel
     */
    public function pricingPolicies()
    {
        return $this->hasMany(PricingPolicy::class);
    }
}
