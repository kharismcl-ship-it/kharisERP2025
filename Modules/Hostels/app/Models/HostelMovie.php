<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelMovie extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'hostel_id',
        'title',
        'description',
        'genre',
        'thumbnail',
        'video_url',
        'video_file',
        'price',
        'duration_minutes',
        'is_active',
        'requires_payment',
        'is_globally_available',
        'uploaded_by',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'requires_payment'     => 'boolean',
        'is_globally_available' => 'boolean',
        'price'                => 'decimal:2',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function company()
    {
        return $this->belongsTo(\Modules\Core\Models\Company::class);
    }

    public function purchases()
    {
        return $this->hasMany(HostelMoviePurchase::class);
    }

    public function requests()
    {
        return $this->hasMany(HostelMovieRequest::class, 'fulfilled_movie_id');
    }

    /**
     * Check if a given occupant has paid access to this movie.
     */
    public function hasAccessFor(int $occupantId): bool
    {
        if (! $this->requires_payment) {
            return true;
        }

        return $this->purchases()
            ->where('hostel_occupant_id', $occupantId)
            ->where('status', 'paid')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();
    }
}
