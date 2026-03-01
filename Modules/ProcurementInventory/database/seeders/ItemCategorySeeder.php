<?php

namespace Modules\ProcurementInventory\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\ProcurementInventory\Models\ItemCategory;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (! $company) {
            $this->command->warn('ItemCategorySeeder: no company found — skipping.');
            return;
        }

        $categories = [
            'Raw Materials',
            'Office Supplies',
            'Chemicals & Reagents',
            'Spare Parts & Components',
            'Packaging Materials',
            'Safety & PPE',
            'Fuel & Lubricants',
            'Cleaning Supplies',
            'Agricultural Inputs',
            'Electronics & IT Equipment',
        ];

        foreach ($categories as $name) {
            ItemCategory::firstOrCreate(
                ['company_id' => $company->id, 'slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        $this->command->info('ItemCategorySeeder: ' . count($categories) . ' categories seeded.');
    }
}
