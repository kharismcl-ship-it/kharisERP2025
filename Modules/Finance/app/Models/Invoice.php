<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'customer_name',
        'customer_type',
        'customer_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'sub_total',
        'tax_total',
        'total',
        'hostel_id',
        'farm_id',
        'construction_project_id',
        'plant_id',
        'module',
        'entity_type',
        'entity_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sub_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the company that owns this invoice.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the lines for this invoice.
     */
    public function lines()
    {
        return $this->hasMany(InvoiceLine::class, 'invoice_id');
    }

    /**
     * Get the payments for this invoice.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }
}
