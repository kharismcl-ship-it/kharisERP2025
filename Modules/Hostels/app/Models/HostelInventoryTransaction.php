<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelInventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'inventory_item_id',
        'room_id',
        'processed_by',
        'transaction_type',
        'quantity',
        'balance_after',
        'notes',
        'reference_number',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'quantity' => 'integer',
        'balance_after' => 'integer',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(HostelInventoryItem::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by');
    }

    public function scopeReceipts($query)
    {
        return $query->where('transaction_type', 'receipt');
    }

    public function scopeIssues($query)
    {
        return $query->where('transaction_type', 'issue');
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }
}
