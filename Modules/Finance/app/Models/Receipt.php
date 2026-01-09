<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'invoice_id',
        'payment_id',
        'receipt_number',
        'receipt_date',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_type',
        'customer_id',
        'amount',
        'payment_method',
        'reference',
        'notes',
        'status',
        'sent_at',
        'viewed_at',
        'downloaded_at',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'meta' => 'array',
    ];

    /**
     * Get the company that owns this receipt.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the invoice this receipt is for.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Get the payment this receipt is for.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Generate a receipt number.
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = 'RCT';
        $year = date('Y');
        $month = date('m');
        $sequence = static::where('receipt_number', 'like', "{$prefix}-{$year}{$month}-%")->count() + 1;

        return sprintf('%s-%s%02d-%06d', $prefix, $year, $month, $sequence);
    }

    /**
     * Mark receipt as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark receipt as viewed.
     */
    public function markAsViewed(): void
    {
        $this->update([
            'status' => 'viewed',
            'viewed_at' => now(),
        ]);
    }

    /**
     * Mark receipt as downloaded.
     */
    public function markAsDownloaded(): void
    {
        $this->update([
            'status' => 'downloaded',
            'downloaded_at' => now(),
        ]);
    }
}
