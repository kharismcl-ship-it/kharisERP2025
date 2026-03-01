<?php

namespace Modules\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Sales\Events\DiningOrderSentToKitchen;

class DiningOrder extends Model
{
    protected $fillable = [
        'table_id',
        'waiter_id',
        'status',
        'subtotal',
        'tax',
        'total',
        'invoice_id',
        'notes',
        'sent_to_kitchen_at',
        'served_at',
        'paid_at',
    ];

    protected $casts = [
        'subtotal'           => 'decimal:2',
        'tax'                => 'decimal:2',
        'total'              => 'decimal:2',
        'sent_to_kitchen_at' => 'datetime',
        'served_at'          => 'datetime',
        'paid_at'            => 'datetime',
    ];

    const STATUSES = ['open', 'in_kitchen', 'ready', 'served', 'paid', 'cancelled'];

    protected static function booted(): void
    {
        static::updated(function (self $order) {
            if ($order->wasChanged('status') && $order->status === 'in_kitchen') {
                $order->updateQuietly(['sent_to_kitchen_at' => now()]);
                DiningOrderSentToKitchen::dispatch($order);
            }
        });
    }

    public function recalculate(): void
    {
        $this->subtotal = $this->items->sum('line_total');
        $table          = $this->table()->with('restaurant')->first();
        $taxRate        = optional($table?->restaurant)->default_vat_rate ?? 15.0;
        $this->tax      = round($this->subtotal * ($taxRate / 100), 2);
        $this->total    = round($this->subtotal + $this->tax, 2);
        $this->saveQuietly();
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class, 'table_id');
    }

    public function waiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DiningOrderItem::class, 'dining_order_id');
    }

    public function kitchenTickets(): HasMany
    {
        return $this->hasMany(KitchenTicket::class, 'dining_order_id');
    }
}