<?php

namespace Modules\Farms\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Farms\Models\FarmShopSetting;

class ShopSettingsService
{
    public function get(int $companyId): FarmShopSetting
    {
        return Cache::remember("farm_shop_settings_{$companyId}", 600, function () use ($companyId) {
            return FarmShopSetting::firstOrNew(
                ['company_id' => $companyId],
                [
                    'shop_name'    => 'Alpha Farms',
                    'tagline'      => 'Fresh from the farm to your table',
                    'primary_color' => '#15803d',
                    'delivery_fee' => 20.00,
                ]
            );
        });
    }

    public function forget(int $companyId): void
    {
        Cache::forget("farm_shop_settings_{$companyId}");
    }

    /** Resolve settings from the current request context (domain → company). */
    public function forCurrentDomain(): FarmShopSetting
    {
        // In shop context we store company_id in the cart session
        $companyId = session('farm_shop_cart.company_id')
            ?? session('farm_shop_company_id');

        if ($companyId) {
            return $this->get((int) $companyId);
        }

        // Fallback — return a default settings object (not persisted)
        return new FarmShopSetting([
            'shop_name'    => config('app.name', 'Alpha Farms'),
            'tagline'      => 'Fresh from the farm to your table',
            'primary_color' => '#15803d',
            'delivery_fee' => 20.00,
        ]);
    }
}
