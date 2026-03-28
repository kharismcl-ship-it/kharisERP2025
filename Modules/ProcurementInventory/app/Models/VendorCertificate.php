<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorCertificate extends Model
{
    use HasFactory;

    protected $table = 'procurement_vendor_certificates';

    protected $fillable = [
        'company_id',
        'vendor_id',
        'certificate_type',
        'certificate_number',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'file_path',
        'status',
        'notes',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'expiry_date' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $certificate) {
            if ($certificate->expiry_date) {
                if ($certificate->expiry_date->isPast()) {
                    $certificate->status = 'expired';
                } elseif ($certificate->expiry_date->diffInDays(now()) <= 30) {
                    $certificate->status = 'expiring_soon';
                } else {
                    $certificate->status = 'valid';
                }
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date
            && ! $this->expiry_date->isPast()
            && $this->expiry_date->diffInDays(now()) <= $days;
    }
}