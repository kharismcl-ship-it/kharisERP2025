<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelBookOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_book_order_id',
        'hostel_book_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(HostelBookOrder::class, 'hostel_book_order_id');
    }

    public function book()
    {
        return $this->belongsTo(HostelBook::class, 'hostel_book_id');
    }
}
