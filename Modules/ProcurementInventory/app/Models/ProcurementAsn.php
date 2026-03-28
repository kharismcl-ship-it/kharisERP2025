<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcurementAsn extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'procurement_asns';

    protected $fillable = [
        'company_id',
        'purchase_order_id',
        'vendor_id',
        'vendor_contact_id',
        'asn_number',
        'expected_delivery_date',
        'carrier_name',
        'tracking_number',
        'notes',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'submitted_at'           => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function vendorContact(): BelongsTo
    {
        return $this->belongsTo(VendorContact::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(ProcurementAsnLine::class, 'asn_id');
    }
}