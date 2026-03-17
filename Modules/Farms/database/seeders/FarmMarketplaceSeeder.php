<?php

namespace Modules\Farms\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmProduceInventory;

/**
 * Seeds a demo farm and 20 marketplace-listed produce items.
 *
 * Run: php artisan db:seed --class=Modules\\Farms\\Database\\Seeders\\FarmMarketplaceSeeder
 */
class FarmMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create the farms company
        $company = Company::where('slug', 'kharis-farms')->first()
            ?? Company::firstOrCreate(
                ['slug' => 'kharis-farms'],
                ['name' => 'Kharis Farms', 'type' => 'farm', 'is_active' => true]
            );

        // Create two demo farms
        $alphaFarm = Farm::firstOrCreate(
            ['slug' => 'alpha-farm', 'company_id' => $company->id],
            [
                'name'        => 'Alpha Farm',
                'description' => 'Our flagship crop farm located in the Volta Region.',
                'location'    => 'Volta Region, Ghana',
                'type'        => 'mixed',
                'total_area'  => 120.00,
                'area_unit'   => 'acres',
                'owner_name'  => 'Kwame Mensah',
                'owner_phone' => '+233201234567',
                'status'      => 'active',
            ]
        );

        $betaFarm = Farm::firstOrCreate(
            ['slug' => 'beta-livestock-farm', 'company_id' => $company->id],
            [
                'name'        => 'Beta Livestock Farm',
                'description' => 'Specialised poultry and small ruminant farm in Ashanti.',
                'location'    => 'Ashanti Region, Ghana',
                'type'        => 'livestock',
                'total_area'  => 40.00,
                'area_unit'   => 'acres',
                'owner_name'  => 'Ama Boateng',
                'owner_phone' => '+233209876543',
                'status'      => 'active',
            ]
        );

        $this->command->info("Farms ready: {$alphaFarm->name}, {$betaFarm->name}");

        // 20 produce items — mix of in_stock, low_stock, one depleted (not listed)
        $products = [
            // Alpha Farm — Crops
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Fresh Tomatoes',
                'unit'               => 'kg',
                'total_quantity'     => 500,
                'current_stock'      => 320,
                'unit_cost'          => 4.50,
                'unit_price'         => 8.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Vine-ripened, firm and juicy tomatoes freshly harvested from our fields.',
                'harvest_date'       => now()->subDays(2)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Garden Eggs (Eggplant)',
                'unit'               => 'kg',
                'total_quantity'     => 300,
                'current_stock'      => 180,
                'unit_cost'          => 3.00,
                'unit_price'         => 6.50,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Small, tender garden eggs ideal for soups and stews.',
                'harvest_date'       => now()->subDays(3)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Green Pepper',
                'unit'               => 'kg',
                'total_quantity'     => 200,
                'current_stock'      => 95,
                'unit_cost'          => 5.00,
                'unit_price'         => 10.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Crisp green peppers, great for salads, stir-fries, and sauces.',
                'harvest_date'       => now()->subDays(1)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Cassava',
                'unit'               => 'kg',
                'total_quantity'     => 2000,
                'current_stock'      => 1200,
                'unit_cost'          => 1.80,
                'unit_price'         => 3.50,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Premium white cassava, harvested at peak starch content.',
                'harvest_date'       => now()->subDays(5)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'White Yam',
                'unit'               => 'kg',
                'total_quantity'     => 1000,
                'current_stock'      => 650,
                'unit_cost'          => 6.00,
                'unit_price'         => 12.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Premium white yam from the Brong-Ahafo belt — soft and floury.',
                'harvest_date'       => now()->subDays(7)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Plantain (Unripe)',
                'unit'               => 'dozen',
                'total_quantity'     => 500,
                'current_stock'      => 280,
                'unit_cost'          => 8.00,
                'unit_price'         => 15.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Green plantains, perfect for kelewele, chips, or fufu.',
                'harvest_date'       => now()->subDays(4)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Plantain (Ripe)',
                'unit'               => 'dozen',
                'total_quantity'     => 200,
                'current_stock'      => 60,
                'unit_cost'          => 9.00,
                'unit_price'         => 18.00,
                'status'             => 'low_stock',
                'marketplace_listed' => true,
                'description'        => 'Perfectly ripened yellow plantains, naturally sweet.',
                'harvest_date'       => now()->subDays(2)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Fresh Maize (Corn)',
                'unit'               => 'dozen',
                'total_quantity'     => 800,
                'current_stock'      => 420,
                'unit_cost'          => 10.00,
                'unit_price'         => 20.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Freshly harvested sweet maize cobs, great for roasting or boiling.',
                'harvest_date'       => now()->subDays(1)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Cabbage',
                'unit'               => 'head',
                'total_quantity'     => 300,
                'current_stock'      => 140,
                'unit_cost'          => 4.00,
                'unit_price'         => 8.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Firm, leafy cabbages — freshly cut and ready for delivery.',
                'harvest_date'       => now()->subDays(2)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Spring Onions',
                'unit'               => 'bundle',
                'total_quantity'     => 400,
                'current_stock'      => 200,
                'unit_cost'          => 2.50,
                'unit_price'         => 5.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Fresh spring onion bundles — great for garnishing and cooking.',
                'harvest_date'       => now()->subDays(1)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Fresh Ginger',
                'unit'               => 'kg',
                'total_quantity'     => 150,
                'current_stock'      => 80,
                'unit_cost'          => 14.00,
                'unit_price'         => 25.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Aromatic, high-oil ginger — excellent for teas, sauces, and export.',
                'harvest_date'       => now()->subDays(10)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Dried Chili Pepper',
                'unit'               => 'kg',
                'total_quantity'     => 100,
                'current_stock'      => 45,
                'unit_cost'          => 16.00,
                'unit_price'         => 30.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Sun-dried, deep-red chili peppers with intense heat and flavour.',
                'harvest_date'       => now()->subDays(14)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Watermelon',
                'unit'               => 'piece',
                'total_quantity'     => 200,
                'current_stock'      => 75,
                'unit_cost'          => 12.00,
                'unit_price'         => 25.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Large, sweet, seedless watermelons — refreshing and nutritious.',
                'harvest_date'       => now()->subDays(3)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Pineapple',
                'unit'               => 'piece',
                'total_quantity'     => 400,
                'current_stock'      => 200,
                'unit_cost'          => 5.00,
                'unit_price'         => 12.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Smooth Cayenne pineapple — sweet, golden, and farm-fresh.',
                'harvest_date'       => now()->subDays(2)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Pawpaw (Papaya)',
                'unit'               => 'piece',
                'total_quantity'     => 250,
                'current_stock'      => 120,
                'unit_cost'          => 4.50,
                'unit_price'         => 10.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Ripe, orange-fleshed pawpaw — naturally sweet and rich in vitamins.',
                'harvest_date'       => now()->subDays(1)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Groundnuts (Peanuts)',
                'unit'               => 'kg',
                'total_quantity'     => 500,
                'current_stock'      => 8,
                'unit_cost'          => 8.00,
                'unit_price'         => 15.00,
                'status'             => 'low_stock',
                'marketplace_listed' => true,
                'description'        => 'Shelled, dry-roasted groundnuts — protein-rich snack or cooking staple.',
                'harvest_date'       => now()->subDays(20)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Cowpea (Black-eyed Peas)',
                'unit'               => 'kg',
                'total_quantity'     => 400,
                'current_stock'      => 230,
                'unit_cost'          => 9.50,
                'unit_price'         => 18.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Dried cowpeas, perfect for red red, waakye, and soups.',
                'harvest_date'       => now()->subDays(30)->toDateString(),
            ],
            [
                'farm_id'            => $alphaFarm->id,
                'product_name'       => 'Okra',
                'unit'               => 'kg',
                'total_quantity'     => 200,
                'current_stock'      => 110,
                'unit_cost'          => 5.50,
                'unit_price'         => 12.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Fresh, tender okra pods — ideal for light soups and stews.',
                'harvest_date'       => now()->subDays(1)->toDateString(),
            ],
            // Beta Farm — Yam belt produce
            [
                'farm_id'            => $betaFarm->id,
                'product_name'       => 'Sweet Potato',
                'unit'               => 'kg',
                'total_quantity'     => 600,
                'current_stock'      => 350,
                'unit_cost'          => 3.00,
                'unit_price'         => 6.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Orange-fleshed sweet potatoes — naturally sweet and rich in beta-carotene.',
                'harvest_date'       => now()->subDays(4)->toDateString(),
            ],
            [
                'farm_id'            => $betaFarm->id,
                'product_name'       => 'Cocoyam',
                'unit'               => 'kg',
                'total_quantity'     => 300,
                'current_stock'      => 160,
                'unit_cost'          => 4.00,
                'unit_price'         => 8.00,
                'status'             => 'in_stock',
                'marketplace_listed' => true,
                'description'        => 'Fresh cocoyam corms — starchy and perfect for ampesi, fufu, or kontomire stew.',
                'harvest_date'       => now()->subDays(6)->toDateString(),
            ],
        ];

        $count = 0;
        foreach ($products as $data) {
            $data['company_id']      = $company->id;
            $data['reserved_stock']  = 0;
            $data['sold_stock']      = 0;

            FarmProduceInventory::updateOrCreate(
                [
                    'farm_id'      => $data['farm_id'],
                    'product_name' => $data['product_name'],
                    'company_id'   => $company->id,
                ],
                $data
            );
            $count++;
        }

        $this->command->info("Farm marketplace seeded: {$count} products across 2 farms.");
    }
}
