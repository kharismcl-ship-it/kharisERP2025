<?php

namespace Modules\Farms\Console\Commands;

use Illuminate\Console\Command;
use Modules\Farms\Models\FarmCommodityPrice;

class FetchCommodityPricesCommand extends Command
{
    protected $signature = 'farms:fetch-commodity-prices';

    protected $description = 'Fetch commodity prices from MoFA MIS or Esoko Ghana API';

    public function handle(): void
    {
        // Placeholder implementation — replace with actual Esoko/MoFA MIS API call
        // when API key is configured via FARMS_ESOKO_API_KEY in .env

        $apiKey = config('farms.esoko_api_key', env('FARMS_ESOKO_API_KEY'));

        if (empty($apiKey)) {
            $this->warn('FARMS_ESOKO_API_KEY not configured. Skipping price fetch.');
            $this->info('To enable live commodity prices: add FARMS_ESOKO_API_KEY=your_key to .env');
            // Insert sample prices for demonstration
            $this->seedSamplePrices();
            return;
        }

        // TODO: implement Esoko API call when key is available
        // POST https://api2.esoko.com/api/v3/prices with API key
        $this->info('Esoko API integration ready — implement API call with key.');
    }

    private function seedSamplePrices(): void
    {
        $samples = [
            ['Maize', 'Makola Market', 2.50, 'kg'],
            ['Rice (Local)', 'Makola Market', 6.80, 'kg'],
            ['Tomato', 'Kumasi Central', 1.20, 'kg'],
            ['Yam', 'Kumasi Central', 3.50, 'kg'],
            ['Cassava', 'Bolgatanga Market', 0.80, 'kg'],
            ['Cocoa (dry)', 'CMC Depot', 52.00, 'kg'],
            ['Cashew (raw)', 'Techiman Market', 4.20, 'kg'],
            ['Plantain', 'Makola Market', 0.95, 'kg'],
        ];

        $today = today()->toDateString();

        foreach ($samples as [$commodity, $market, $price, $unit]) {
            FarmCommodityPrice::updateOrCreate(
                [
                    'commodity_name' => $commodity,
                    'market_name'    => $market,
                    'price_date'     => $today,
                ],
                [
                    'price_per_unit' => $price,
                    'unit'           => $unit,
                    'source'         => 'manual',
                    'company_id'     => null,
                ]
            );
        }

        $this->info('Sample commodity prices seeded for demonstration (' . count($samples) . ' entries).');
    }
}