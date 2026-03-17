<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class FarmShopBlogPost extends Model
{
    protected $table = 'farm_shop_blog_posts';

    protected $fillable = [
        'company_id', 'title', 'slug', 'category', 'excerpt', 'content',
        'cover_image_path', 'tags', 'ingredients', 'reading_time_minutes',
        'is_published', 'published_at',
    ];

    protected $casts = [
        'tags'            => 'array',
        'ingredients'     => 'array',
        'is_published'    => 'boolean',
        'published_at'    => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
