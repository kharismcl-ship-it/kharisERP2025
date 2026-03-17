<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelBook extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'hostel_id',
        'title',
        'author',
        'isbn',
        'description',
        'cover_image',
        'price',
        'book_type',
        'digital_file',
        'stock_qty',
        'is_active',
        'is_globally_available',
    ];

    protected $casts = [
        'is_active'             => 'boolean',
        'is_globally_available' => 'boolean',
        'price'                 => 'decimal:2',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function orderItems()
    {
        return $this->hasMany(HostelBookOrderItem::class);
    }

    public function isAvailable(): bool
    {
        return $this->is_active && ($this->book_type === 'digital' || $this->stock_qty > 0);
    }
}
