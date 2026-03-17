<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmShopSetting extends Model
{
    protected $fillable = [
        'company_id',
        'shop_name',
        'tagline',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'phone',
        'whatsapp_number',
        'email',
        'address',
        'delivery_fee',
        'free_delivery_above',
        'delivery_days',
        'order_cutoff_time',
        'hero_heading',
        'hero_subheading',
        'hero_image_path',
        'announcement_bar_text',
        'announcement_bar_active',
        'meta_title',
        'meta_description',
        'og_image_path',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'footer_about_text',
        'loyalty_enabled',
        'loyalty_points_per_ghs',
        'loyalty_points_value_ghs',
        // Popup
        'popup_active',
        'popup_title',
        'popup_body',
        'popup_cta_text',
        'popup_cta_url',
        'popup_starts_at',
        'popup_ends_at',
        // Announcement bar scheduling
        'announcement_bar_starts_at',
        'announcement_bar_ends_at',
    ];

    protected $casts = [
        'delivery_fee'                  => 'decimal:2',
        'free_delivery_above'           => 'decimal:2',
        'delivery_days'                 => 'array',
        'announcement_bar_active'       => 'boolean',
        'announcement_bar_starts_at'    => 'datetime',
        'announcement_bar_ends_at'      => 'datetime',
        'loyalty_enabled'               => 'boolean',
        'loyalty_points_per_ghs'        => 'decimal:2',
        'loyalty_points_value_ghs'      => 'decimal:4',
        'popup_active'                  => 'boolean',
        'popup_starts_at'               => 'datetime',
        'popup_ends_at'                 => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
