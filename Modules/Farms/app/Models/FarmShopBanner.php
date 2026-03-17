<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmShopBanner extends Model
{
    protected $fillable = [
        'company_id',
        'title',
        'subtitle',
        'cta_text',
        'cta_url',
        'image_path',
        'overlay_color',
        'overlay_opacity',
        'sort_order',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'starts_at'   => 'datetime',
        'ends_at'     => 'datetime',
        'sort_order'  => 'integer',
        'overlay_opacity' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** Banners that are active right now */
    public function scopeVisible(Builder $query): Builder
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->orderBy('sort_order');
    }
}
