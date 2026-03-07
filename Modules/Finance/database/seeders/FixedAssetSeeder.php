<?php

namespace Modules\Finance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\Finance\Models\AssetCategory;
use Modules\Finance\Models\FixedAsset;

class FixedAssetSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = \App\Models\Company::first()?->id ?? 1;

        // ── Category map icons — downloaded once and stored in public disk ───
        $iconDir = 'asset-categories/icons';
        Storage::disk('public')->makeDirectory($iconDir);

        $iconSources = [
            'land-buildings.png'   => 'https://img.icons8.com/color/96/city-buildings.png',
            'motor-vehicles.png'   => 'https://img.icons8.com/color/96/car--v1.png',
            'office-furniture.png' => 'https://img.icons8.com/color/96/office.png',
            'plant-machinery.png'  => 'https://img.icons8.com/color/96/factory.png',
            'computer-it.png'      => 'https://img.icons8.com/color/96/laptop.png',
        ];

        foreach ($iconSources as $filename => $url) {
            $path = $iconDir . '/' . $filename;
            if (! Storage::disk('public')->exists($path)) {
                try {
                    $response = Http::timeout(10)->get($url);
                    if ($response->successful()) {
                        Storage::disk('public')->put($path, $response->body());
                    }
                } catch (\Throwable) {
                    // Skip if network unavailable — icons can be uploaded manually
                }
            }
        }

        // ── Asset Categories ─────────────────────────────────────────────────
        $categories = [
            [
                'name'                  => 'Land & Buildings',
                'map_icon'              => $iconDir . '/land-buildings.png',
                'depreciation_method'   => 'straight_line',
                'useful_life_years'     => 40,
                'residual_rate'         => 10,
            ],
            [
                'name'                  => 'Motor Vehicles',
                'map_icon'              => $iconDir . '/motor-vehicles.png',
                'depreciation_method'   => 'straight_line',
                'useful_life_years'     => 5,
                'residual_rate'         => 15,
            ],
            [
                'name'                  => 'Office Equipment & Furniture',
                'map_icon'              => $iconDir . '/office-furniture.png',
                'depreciation_method'   => 'straight_line',
                'useful_life_years'     => 5,
                'residual_rate'         => 5,
            ],
            [
                'name'                  => 'Plant & Machinery',
                'map_icon'              => $iconDir . '/plant-machinery.png',
                'depreciation_method'   => 'straight_line',
                'useful_life_years'     => 10,
                'residual_rate'         => 10,
            ],
            [
                'name'                  => 'Computer & IT Equipment',
                'map_icon'              => $iconDir . '/computer-it.png',
                'depreciation_method'   => 'declining_balance',
                'useful_life_years'     => 3,
                'residual_rate'         => 5,
            ],
        ];

        $categoryMap = [];
        foreach ($categories as $cat) {
            $category = AssetCategory::updateOrCreate(
                ['name' => $cat['name']],
                array_merge($cat, ['company_id' => $companyId])
            );
            $categoryMap[$cat['name']] = $category->id;
        }

        // ── Fixed Assets ─────────────────────────────────────────────────────
        // Coordinates spread across major Ghana cities:
        //   Accra ~5.556,-0.197  |  Kumasi ~6.688,-1.623  |  Tema ~5.668,0.009
        //   Takoradi ~4.898,-1.756  |  Tamale ~9.403,-0.840  |  Cape Coast ~5.105,-1.246
        //   Ho ~6.601,0.471  |  Sunyani ~7.335,-2.326  |  Koforidua ~6.094,-0.261
        $assets = [
            // ── Accra ──────────────────────────────────────────────────────
            [
                'asset_code'               => 'AST-0001',
                'name'                     => 'Head Office Building',
                'category'                 => 'Land & Buildings',
                'location'                 => 'No. 5 Independence Ave, Accra',
                'acquisition_date'         => '2015-01-15',
                'depreciation_start_date'  => '2015-02-01',
                'cost'                     => 2500000.00,
                'residual_value'           => 250000.00,
                'accumulated_depreciation' => 168750.00,
                'status'                   => 'active',
                'latitude'                 => 5.5560,
                'longitude'                => -0.1969,
            ],
            [
                'asset_code'               => 'AST-0002',
                'name'                     => 'Toyota Land Cruiser (GH-1234-22)',
                'category'                 => 'Motor Vehicles',
                'serial_number'            => 'JTMHX7JH2N5123456',
                'location'                 => 'Head Office — Motor Pool, Accra',
                'acquisition_date'         => '2022-03-10',
                'depreciation_start_date'  => '2022-04-01',
                'cost'                     => 320000.00,
                'residual_value'           => 48000.00,
                'accumulated_depreciation' => 85800.00,
                'status'                   => 'active',
                'latitude'                 => 5.5583,
                'longitude'                => -0.1944,
            ],
            [
                'asset_code'               => 'AST-0004',
                'name'                     => 'Industrial Generator 100KVA',
                'category'                 => 'Plant & Machinery',
                'serial_number'            => 'GEN-CAT-C100-2021-004',
                'location'                 => 'Head Office — Generator Room, Accra',
                'acquisition_date'         => '2021-08-20',
                'depreciation_start_date'  => '2021-09-01',
                'cost'                     => 95000.00,
                'residual_value'           => 9500.00,
                'accumulated_depreciation' => 28500.00,
                'status'                   => 'active',
                'latitude'                 => 5.5572,
                'longitude'                => -0.1981,
            ],
            [
                'asset_code'               => 'AST-0005',
                'name'                     => 'HP ProBook Laptop Fleet (10 Units)',
                'category'                 => 'Computer & IT Equipment',
                'serial_number'            => 'HP-FLEET-2023-005',
                'location'                 => 'Head Office — IT Department, Accra',
                'acquisition_date'         => '2023-01-05',
                'depreciation_start_date'  => '2023-02-01',
                'cost'                     => 45000.00,
                'residual_value'           => 2250.00,
                'accumulated_depreciation' => 21000.00,
                'status'                   => 'active',
                'latitude'                 => 5.5565,
                'longitude'                => -0.1958,
            ],
            [
                'asset_code'               => 'AST-0006',
                'name'                     => 'Executive Office Furniture Set',
                'category'                 => 'Office Equipment & Furniture',
                'location'                 => 'Head Office — Executive Suite, Accra',
                'acquisition_date'         => '2019-11-01',
                'depreciation_start_date'  => '2019-12-01',
                'cost'                     => 28000.00,
                'residual_value'           => 1400.00,
                'accumulated_depreciation' => 22880.00,
                'status'                   => 'active',
                'latitude'                 => 5.5568,
                'longitude'                => -0.2001,
            ],
            [
                'asset_code'               => 'AST-0008',
                'name'                     => 'Photocopier Ricoh MP C4504',
                'category'                 => 'Office Equipment & Furniture',
                'serial_number'            => 'RICOH-C4504-2017-008',
                'location'                 => 'Head Office — Admin, Accra',
                'acquisition_date'         => '2017-03-20',
                'depreciation_start_date'  => '2017-04-01',
                'cost'                     => 12500.00,
                'residual_value'           => 625.00,
                'accumulated_depreciation' => 11875.00,
                'status'                   => 'written_off',
                'disposal_date'            => '2024-06-30',
                'latitude'                 => 5.5555,
                'longitude'                => -0.1975,
            ],

            // ── Kumasi ─────────────────────────────────────────────────────
            [
                'asset_code'               => 'AST-0003',
                'name'                     => 'Pickup Truck — Field Operations (GH-5678-20)',
                'category'                 => 'Motor Vehicles',
                'serial_number'            => 'JTMHX7JH2N5789012',
                'location'                 => 'Kumasi Branch',
                'acquisition_date'         => '2020-06-01',
                'depreciation_start_date'  => '2020-07-01',
                'cost'                     => 180000.00,
                'residual_value'           => 27000.00,
                'accumulated_depreciation' => 109350.00,
                'status'                   => 'active',
                'latitude'                 => 6.6884,
                'longitude'                => -1.6244,
            ],
            [
                'asset_code'               => 'AST-0009',
                'name'                     => 'Kumasi Branch Office',
                'category'                 => 'Land & Buildings',
                'location'                 => 'Ahodwo, Kumasi',
                'acquisition_date'         => '2010-05-20',
                'depreciation_start_date'  => '2010-06-01',
                'cost'                     => 1200000.00,
                'residual_value'           => 120000.00,
                'accumulated_depreciation' => 202500.00,
                'status'                   => 'active',
                'latitude'                 => 6.6867,
                'longitude'                => -1.6226,
            ],
            [
                'asset_code'               => 'AST-0010',
                'name'                     => 'Kumasi Server Room Equipment',
                'category'                 => 'Computer & IT Equipment',
                'serial_number'            => 'DELL-SERVER-KSI-010',
                'location'                 => 'Kumasi Branch — Server Room',
                'acquisition_date'         => '2022-09-15',
                'depreciation_start_date'  => '2022-10-01',
                'cost'                     => 62000.00,
                'residual_value'           => 3100.00,
                'accumulated_depreciation' => 24533.33,
                'status'                   => 'active',
                'latitude'                 => 6.6871,
                'longitude'                => -1.6238,
            ],
            [
                'asset_code'               => 'AST-0011',
                'name'                     => 'Kumasi Branch Generator 60KVA',
                'category'                 => 'Plant & Machinery',
                'serial_number'            => 'GEN-KSI-60KVA-2020',
                'location'                 => 'Kumasi Branch — Generator Bay',
                'acquisition_date'         => '2020-02-10',
                'depreciation_start_date'  => '2020-03-01',
                'cost'                     => 55000.00,
                'residual_value'           => 5500.00,
                'accumulated_depreciation' => 24750.00,
                'status'                   => 'active',
                'latitude'                 => 6.6855,
                'longitude'                => -1.6255,
            ],

            // ── Tema ───────────────────────────────────────────────────────
            [
                'asset_code'               => 'AST-0007',
                'name'                     => 'Company Van — Delivery (GH-9900-18)',
                'category'                 => 'Motor Vehicles',
                'serial_number'            => 'VAN2018GH9900XYZ',
                'location'                 => 'Warehouse — Tema',
                'acquisition_date'         => '2018-05-15',
                'depreciation_start_date'  => '2018-06-01',
                'cost'                     => 95000.00,
                'residual_value'           => 9500.00,
                'accumulated_depreciation' => 85500.00,
                'status'                   => 'disposed',
                'disposal_date'            => '2024-12-31',
                'disposal_amount'          => 12000.00,
                'latitude'                 => 5.6677,
                'longitude'                => 0.0085,
            ],
            [
                'asset_code'               => 'AST-0012',
                'name'                     => 'Tema Bonded Warehouse',
                'category'                 => 'Land & Buildings',
                'location'                 => 'Community 1, Tema',
                'acquisition_date'         => '2012-03-01',
                'depreciation_start_date'  => '2012-04-01',
                'cost'                     => 1800000.00,
                'residual_value'           => 180000.00,
                'accumulated_depreciation' => 243000.00,
                'status'                   => 'active',
                'latitude'                 => 5.6682,
                'longitude'                => 0.0076,
            ],
            [
                'asset_code'               => 'AST-0013',
                'name'                     => 'Forklift — Tema Warehouse',
                'category'                 => 'Plant & Machinery',
                'serial_number'            => 'TOYOTA-FORKLIFT-8FGU25',
                'location'                 => 'Tema Bonded Warehouse',
                'acquisition_date'         => '2019-07-01',
                'depreciation_start_date'  => '2019-08-01',
                'cost'                     => 78000.00,
                'residual_value'           => 7800.00,
                'accumulated_depreciation' => 42900.00,
                'status'                   => 'active',
                'latitude'                 => 5.6672,
                'longitude'                => 0.0094,
            ],

            // ── Takoradi ───────────────────────────────────────────────────
            [
                'asset_code'               => 'AST-0014',
                'name'                     => 'Takoradi Office Block',
                'category'                 => 'Land & Buildings',
                'location'                 => 'Effia, Takoradi',
                'acquisition_date'         => '2014-08-01',
                'depreciation_start_date'  => '2014-09-01',
                'cost'                     => 950000.00,
                'residual_value'           => 95000.00,
                'accumulated_depreciation' => 115312.50,
                'status'                   => 'active',
                'latitude'                 => 4.8979,
                'longitude'                => -1.7552,
            ],
            [
                'asset_code'               => 'AST-0015',
                'name'                     => 'Takoradi Branch Vehicle (GH-2211-21)',
                'category'                 => 'Motor Vehicles',
                'serial_number'            => 'NISSAN-PATROL-TDI-2021',
                'location'                 => 'Takoradi Branch — Parking',
                'acquisition_date'         => '2021-04-15',
                'depreciation_start_date'  => '2021-05-01',
                'cost'                     => 210000.00,
                'residual_value'           => 31500.00,
                'accumulated_depreciation' => 94500.00,
                'status'                   => 'active',
                'latitude'                 => 4.8964,
                'longitude'                => -1.7584,
            ],
            [
                'asset_code'               => 'AST-0016',
                'name'                     => 'Takoradi Compressor Unit',
                'category'                 => 'Plant & Machinery',
                'serial_number'            => 'ATLAS-COPCO-GA45-2018',
                'location'                 => 'Takoradi Technical Yard',
                'acquisition_date'         => '2018-11-20',
                'depreciation_start_date'  => '2018-12-01',
                'cost'                     => 120000.00,
                'residual_value'           => 12000.00,
                'accumulated_depreciation' => 64800.00,
                'status'                   => 'active',
                'latitude'                 => 4.8941,
                'longitude'                => -1.7619,
            ],

            // ── Tamale ─────────────────────────────────────────────────────
            [
                'asset_code'               => 'AST-0017',
                'name'                     => 'Tamale Northern Office',
                'category'                 => 'Land & Buildings',
                'location'                 => 'Kaladan Road, Tamale',
                'acquisition_date'         => '2016-09-01',
                'depreciation_start_date'  => '2016-10-01',
                'cost'                     => 780000.00,
                'residual_value'           => 78000.00,
                'accumulated_depreciation' => 82687.50,
                'status'                   => 'active',
                'latitude'                 => 9.4034,
                'longitude'                => -0.8424,
            ],
            [
                'asset_code'               => 'AST-0018',
                'name'                     => 'Tamale IT Infrastructure',
                'category'                 => 'Computer & IT Equipment',
                'serial_number'            => 'HP-NW-TAMALE-2021',
                'location'                 => 'Tamale Northern Office — Server Room',
                'acquisition_date'         => '2021-06-01',
                'depreciation_start_date'  => '2021-07-01',
                'cost'                     => 38000.00,
                'residual_value'           => 1900.00,
                'accumulated_depreciation' => 24033.33,
                'status'                   => 'active',
                'latitude'                 => 9.4021,
                'longitude'                => -0.8445,
            ],
            [
                'asset_code'               => 'AST-0019',
                'name'                     => 'Tamale Office Furniture',
                'category'                 => 'Office Equipment & Furniture',
                'location'                 => 'Tamale Northern Office',
                'acquisition_date'         => '2016-10-01',
                'depreciation_start_date'  => '2016-11-01',
                'cost'                     => 18500.00,
                'residual_value'           => 925.00,
                'accumulated_depreciation' => 15985.00,
                'status'                   => 'active',
                'latitude'                 => 9.4018,
                'longitude'                => -0.8431,
            ],

            // ── Cape Coast ─────────────────────────────────────────────────
            [
                'asset_code'               => 'AST-0020',
                'name'                     => 'Cape Coast Regional Office',
                'category'                 => 'Land & Buildings',
                'location'                 => 'Pedu Road, Cape Coast',
                'acquisition_date'         => '2013-06-01',
                'depreciation_start_date'  => '2013-07-01',
                'cost'                     => 620000.00,
                'residual_value'           => 62000.00,
                'accumulated_depreciation' => 107437.50,
                'status'                   => 'active',
                'latitude'                 => 5.1054,
                'longitude'                => -1.2466,
            ],
            [
                'asset_code'               => 'AST-0021',
                'name'                     => 'Cape Coast Office Equipment',
                'category'                 => 'Office Equipment & Furniture',
                'location'                 => 'Cape Coast Regional Office',
                'acquisition_date'         => '2020-04-01',
                'depreciation_start_date'  => '2020-05-01',
                'cost'                     => 22000.00,
                'residual_value'           => 1100.00,
                'accumulated_depreciation' => 12540.00,
                'status'                   => 'active',
                'latitude'                 => 5.1038,
                'longitude'                => -1.2491,
            ],
            [
                'asset_code'               => 'AST-0022',
                'name'                     => 'Cape Coast Generator 40KVA',
                'category'                 => 'Plant & Machinery',
                'serial_number'            => 'PERKINS-40KVA-2019-022',
                'location'                 => 'Cape Coast Regional Office — Yard',
                'acquisition_date'         => '2019-03-01',
                'depreciation_start_date'  => '2019-04-01',
                'cost'                     => 42000.00,
                'residual_value'           => 4200.00,
                'accumulated_depreciation' => 21168.00,
                'status'                   => 'active',
                'latitude'                 => 5.1045,
                'longitude'                => -1.2479,
            ],

            // ── Ho ─────────────────────────────────────────────────────────
            [
                'asset_code'               => 'AST-0023',
                'name'                     => 'Ho Volta Regional Office',
                'category'                 => 'Land & Buildings',
                'location'                 => 'Ho Polytechnic Road, Ho',
                'acquisition_date'         => '2017-02-01',
                'depreciation_start_date'  => '2017-03-01',
                'cost'                     => 540000.00,
                'residual_value'           => 54000.00,
                'accumulated_depreciation' => 59737.50,
                'status'                   => 'active',
                'latitude'                 => 6.6013,
                'longitude'                => 0.4718,
            ],
            [
                'asset_code'               => 'AST-0024',
                'name'                     => 'Ho Branch Vehicle (GH-4400-19)',
                'category'                 => 'Motor Vehicles',
                'serial_number'            => 'FORD-RANGER-HO-2019',
                'location'                 => 'Ho Volta Regional Office',
                'acquisition_date'         => '2019-09-01',
                'depreciation_start_date'  => '2019-10-01',
                'cost'                     => 155000.00,
                'residual_value'           => 23250.00,
                'accumulated_depreciation' => 99237.50,
                'status'                   => 'active',
                'latitude'                 => 6.6028,
                'longitude'                => 0.4735,
            ],
        ];

        foreach ($assets as $assetData) {
            $categoryName = $assetData['category'];
            unset($assetData['category']);

            FixedAsset::updateOrCreate(
                ['asset_code' => $assetData['asset_code']],
                array_merge($assetData, [
                    'company_id'  => $companyId,
                    'category_id' => $categoryMap[$categoryName],
                ])
            );
        }
    }
}