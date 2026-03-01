<?php

namespace Modules\ProcurementInventory\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\ProcurementInventory\Models\Item;
use Modules\ProcurementInventory\Models\ItemCategory;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (! $company) {
            $this->command->warn('ItemSeeder: no company found — skipping.');
            return;
        }

        $categoryMap = ItemCategory::where('company_id', $company->id)
            ->pluck('id', 'name');

        $items = [
            // Raw Materials
            ['category' => 'Raw Materials', 'name' => 'Wood Pulp (Softwood)', 'sku' => 'RM-WP-001', 'unit' => 'kg', 'price' => 4.50, 'reorder' => 500, 'reorder_qty' => 2000],
            ['category' => 'Raw Materials', 'name' => 'Recycled Fibre', 'sku' => 'RM-RF-001', 'unit' => 'kg', 'price' => 2.80, 'reorder' => 1000, 'reorder_qty' => 5000],
            ['category' => 'Raw Materials', 'name' => 'Limestone Powder', 'sku' => 'RM-LS-001', 'unit' => 'kg', 'price' => 1.20, 'reorder' => 200, 'reorder_qty' => 1000],

            // Chemicals & Reagents
            ['category' => 'Chemicals & Reagents', 'name' => 'Chlorine (Liquid)', 'sku' => 'CH-CL-001', 'unit' => 'litre', 'price' => 8.00, 'reorder' => 50, 'reorder_qty' => 200],
            ['category' => 'Chemicals & Reagents', 'name' => 'Caustic Soda (NaOH)', 'sku' => 'CH-CS-001', 'unit' => 'kg', 'price' => 6.50, 'reorder' => 100, 'reorder_qty' => 500],
            ['category' => 'Chemicals & Reagents', 'name' => 'Alum (Aluminium Sulphate)', 'sku' => 'CH-AL-001', 'unit' => 'kg', 'price' => 3.20, 'reorder' => 100, 'reorder_qty' => 400],
            ['category' => 'Chemicals & Reagents', 'name' => 'Hydrogen Peroxide (35%)', 'sku' => 'CH-HP-001', 'unit' => 'litre', 'price' => 12.00, 'reorder' => 30, 'reorder_qty' => 100],

            // Fuel & Lubricants
            ['category' => 'Fuel & Lubricants', 'name' => 'Diesel Fuel', 'sku' => 'FL-DS-001', 'unit' => 'litre', 'price' => 13.50, 'reorder' => 200, 'reorder_qty' => 1000],
            ['category' => 'Fuel & Lubricants', 'name' => 'Engine Oil (15W-40)', 'sku' => 'FL-EO-001', 'unit' => 'litre', 'price' => 28.00, 'reorder' => 20, 'reorder_qty' => 50],
            ['category' => 'Fuel & Lubricants', 'name' => 'Hydraulic Oil', 'sku' => 'FL-HO-001', 'unit' => 'litre', 'price' => 35.00, 'reorder' => 10, 'reorder_qty' => 30],

            // Spare Parts & Components
            ['category' => 'Spare Parts & Components', 'name' => 'V-Belt (B-Section)', 'sku' => 'SP-VB-001', 'unit' => 'pcs', 'price' => 45.00, 'reorder' => 5, 'reorder_qty' => 20],
            ['category' => 'Spare Parts & Components', 'name' => 'Ball Bearing (6205)', 'sku' => 'SP-BB-001', 'unit' => 'pcs', 'price' => 80.00, 'reorder' => 5, 'reorder_qty' => 15],
            ['category' => 'Spare Parts & Components', 'name' => 'Filter Element (Hydraulic)', 'sku' => 'SP-FE-001', 'unit' => 'pcs', 'price' => 120.00, 'reorder' => 3, 'reorder_qty' => 10],

            // Office Supplies
            ['category' => 'Office Supplies', 'name' => 'A4 Copy Paper (80gsm)', 'sku' => 'OS-AP-001', 'unit' => 'ream', 'price' => 22.00, 'reorder' => 10, 'reorder_qty' => 50],
            ['category' => 'Office Supplies', 'name' => 'Printer Ink Cartridge (Black)', 'sku' => 'OS-PI-001', 'unit' => 'pcs', 'price' => 85.00, 'reorder' => 2, 'reorder_qty' => 6],

            // Safety & PPE
            ['category' => 'Safety & PPE', 'name' => 'Safety Helmet (Hard Hat)', 'sku' => 'PP-SH-001', 'unit' => 'pcs', 'price' => 35.00, 'reorder' => 5, 'reorder_qty' => 20],
            ['category' => 'Safety & PPE', 'name' => 'Safety Boots (Steel Toe)', 'sku' => 'PP-SB-001', 'unit' => 'pairs', 'price' => 120.00, 'reorder' => 3, 'reorder_qty' => 10],
            ['category' => 'Safety & PPE', 'name' => 'Nitrile Gloves (Box/100)', 'sku' => 'PP-NG-001', 'unit' => 'box', 'price' => 45.00, 'reorder' => 5, 'reorder_qty' => 20],

            // Agricultural Inputs
            ['category' => 'Agricultural Inputs', 'name' => 'NPK Fertilizer (15-15-15)', 'sku' => 'AG-NK-001', 'unit' => 'kg', 'price' => 5.50, 'reorder' => 200, 'reorder_qty' => 1000],
            ['category' => 'Agricultural Inputs', 'name' => 'Herbicide (Glyphosate)', 'sku' => 'AG-HB-001', 'unit' => 'litre', 'price' => 18.00, 'reorder' => 20, 'reorder_qty' => 50],
            ['category' => 'Agricultural Inputs', 'name' => 'Insecticide (Lambda-cyhalothrin)', 'sku' => 'AG-IN-001', 'unit' => 'litre', 'price' => 25.00, 'reorder' => 10, 'reorder_qty' => 30],

            // Packaging Materials
            ['category' => 'Packaging Materials', 'name' => 'Cardboard Boxes (Medium)', 'sku' => 'PK-CB-001', 'unit' => 'pcs', 'price' => 8.00, 'reorder' => 50, 'reorder_qty' => 200],
            ['category' => 'Packaging Materials', 'name' => 'Stretch Wrap Film', 'sku' => 'PK-SW-001', 'unit' => 'roll', 'price' => 35.00, 'reorder' => 5, 'reorder_qty' => 20],

            // Cleaning Supplies
            ['category' => 'Cleaning Supplies', 'name' => 'Industrial Detergent (20L)', 'sku' => 'CS-ID-001', 'unit' => 'pcs', 'price' => 65.00, 'reorder' => 5, 'reorder_qty' => 15],
            ['category' => 'Cleaning Supplies', 'name' => 'Disinfectant (5L)', 'sku' => 'CS-DS-001', 'unit' => 'pcs', 'price' => 45.00, 'reorder' => 5, 'reorder_qty' => 15],
        ];

        $count = 0;
        foreach ($items as $data) {
            $categoryId = $categoryMap[$data['category']] ?? null;

            Item::firstOrCreate(
                ['company_id' => $company->id, 'sku' => $data['sku']],
                [
                    'company_id'       => $company->id,
                    'item_category_id' => $categoryId,
                    'name'             => $data['name'],
                    'sku'              => $data['sku'],
                    'slug'             => Str::slug($data['name']),
                    'type'             => 'physical',
                    'unit_of_measure'  => $data['unit'],
                    'unit_price'       => $data['price'],
                    'reorder_level'    => $data['reorder'],
                    'reorder_quantity' => $data['reorder_qty'],
                    'is_active'        => true,
                ]
            );
            $count++;
        }

        $this->command->info("ItemSeeder: {$count} items seeded.");
    }
}
