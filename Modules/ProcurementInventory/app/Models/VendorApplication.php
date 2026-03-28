<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VendorApplication extends Model
{
    use HasFactory;

    protected $table = 'procurement_vendor_applications';

    protected $fillable = [
        'company_id',
        'name',
        'trading_name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'contact_person',
        'contact_phone',
        'tax_number',
        'payment_terms',
        'currency',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'business_type',
        'years_in_business',
        'annual_revenue_band',
        'categories_supplied',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
        'rejection_reason',
        'application_token',
    ];

    protected $casts = [
        'categories_supplied' => 'array',
        'reviewed_at'         => 'datetime',
        'payment_terms'       => 'integer',
        'years_in_business'   => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $application) {
            if (empty($application->application_token)) {
                $application->application_token = Str::random(64);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function approve(User $reviewer): Vendor
    {
        $vendor = Vendor::create([
            'company_id'         => $this->company_id,
            'name'               => $this->name,
            'email'              => $this->email,
            'phone'              => $this->phone,
            'address'            => $this->address,
            'city'               => $this->city,
            'country'            => $this->country,
            'contact_person'     => $this->contact_person,
            'contact_phone'      => $this->contact_phone,
            'tax_number'         => $this->tax_number,
            'payment_terms'      => $this->payment_terms,
            'currency'           => $this->currency,
            'bank_name'          => $this->bank_name,
            'bank_account_number'=> $this->bank_account_number,
            'bank_branch'        => $this->bank_branch,
            'status'             => 'active',
        ]);

        $this->update([
            'status'              => 'approved',
            'reviewed_by_user_id' => $reviewer->id,
            'reviewed_at'         => now(),
        ]);

        return $vendor;
    }

    public function reject(User $reviewer, string $reason): void
    {
        $this->update([
            'status'              => 'rejected',
            'reviewed_by_user_id' => $reviewer->id,
            'reviewed_at'         => now(),
            'rejection_reason'    => $reason,
        ]);
    }
}