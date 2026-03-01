<?php

namespace Modules\ProcurementInventory\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\ProcurementInventory\Models\Vendor;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (! $company) {
            $this->command->warn('VendorSeeder: no company found — skipping.');
            return;
        }

        $vendors = [
            [
                'name'           => 'Accra Industrial Supplies Ltd',
                'email'          => 'orders@accra-industrial.gh',
                'phone'          => '+233302123456',
                'city'           => 'Accra',
                'country'        => 'Ghana',
                'contact_person' => 'Kofi Mensah',
                'contact_phone'  => '+233244111222',
                'contact_email'  => 'kofi@accra-industrial.gh',
                'payment_terms'  => 30,
                'currency'       => 'GHS',
                'status'         => 'active',
            ],
            [
                'name'           => 'Kumasi Chemical & Reagents Co.',
                'email'          => 'sales@kumasi-chemicals.gh',
                'phone'          => '+233322987654',
                'city'           => 'Kumasi',
                'country'        => 'Ghana',
                'contact_person' => 'Ama Asante',
                'contact_phone'  => '+233277333444',
                'contact_email'  => 'ama@kumasi-chemicals.gh',
                'payment_terms'  => 14,
                'currency'       => 'GHS',
                'status'         => 'active',
            ],
            [
                'name'           => 'Tema Port Trading Ltd',
                'email'          => 'procurement@tematrading.gh',
                'phone'          => '+233303654321',
                'city'           => 'Tema',
                'country'        => 'Ghana',
                'contact_person' => 'Kwame Boateng',
                'contact_phone'  => '+233208555666',
                'contact_email'  => 'kwame@tematrading.gh',
                'payment_terms'  => 60,
                'currency'       => 'GHS',
                'status'         => 'active',
            ],
            [
                'name'           => 'SafeGuard PPE Distributors',
                'email'          => 'info@safeguard-ppe.gh',
                'phone'          => '+233302777888',
                'city'           => 'Accra',
                'country'        => 'Ghana',
                'contact_person' => 'Abena Owusu',
                'contact_phone'  => '+233244999000',
                'contact_email'  => 'abena@safeguard-ppe.gh',
                'payment_terms'  => 30,
                'currency'       => 'GHS',
                'status'         => 'active',
            ],
            [
                'name'           => 'FuelMart Ghana',
                'email'          => 'fleet@fuelmart.gh',
                'phone'          => '+233302456789',
                'city'           => 'Accra',
                'country'        => 'Ghana',
                'contact_person' => 'Yaw Darko',
                'contact_phone'  => '+233277123123',
                'contact_email'  => 'yaw@fuelmart.gh',
                'payment_terms'  => 7,
                'currency'       => 'GHS',
                'status'         => 'active',
            ],
        ];

        foreach ($vendors as $data) {
            Vendor::firstOrCreate(
                ['company_id' => $company->id, 'slug' => Str::slug($data['name'])],
                array_merge($data, ['company_id' => $company->id, 'slug' => Str::slug($data['name'])])
            );
        }

        $this->command->info('VendorSeeder: ' . count($vendors) . ' vendors seeded.');
    }
}
