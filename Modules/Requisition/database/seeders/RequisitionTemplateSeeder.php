<?php

namespace Modules\Requisition\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Modules\Requisition\Models\RequisitionTemplate;

class RequisitionTemplateSeeder extends Seeder
{
    /**
     * Templates are seeded per company that exists in the system.
     * If called statically (from a Filament action), pass the company_id.
     */
    public static function importForCompany(int $companyId): int
    {
        $templates = static::definitions();
        $created   = 0;

        foreach ($templates as $template) {
            $exists = RequisitionTemplate::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->where('name', $template['name'])
                ->exists();

            if (! $exists) {
                RequisitionTemplate::create(array_merge($template, ['company_id' => $companyId]));
                $created++;
            }
        }

        return $created;
    }

    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->warn('No companies found — skipping template seeding.');
            return;
        }

        $total = 0;
        foreach ($companies as $company) {
            $total += static::importForCompany($company->id);
        }

        $this->command->info("RequisitionTemplate: seeded {$total} templates across {$companies->count()} company/companies.");
    }

    /**
     * 10 default template definitions (no company_id — that is injected at import time).
     */
    public static function definitions(): array
    {
        return [
            [
                'name'          => 'Office Stationery — Monthly Replenishment',
                'description'   => 'Standard monthly request for pens, paper, folders, and general stationery.',
                'request_type'  => 'material',
                'urgency'       => 'medium',
                'default_title' => 'Office Stationery Replenishment — ' . now()->format('M Y'),
                'is_active'     => true,
                'default_items' => [
                    ['description' => 'A4 Printing Paper (Ream)', 'quantity' => 10, 'unit' => 'reams', 'unit_cost' => 45.00],
                    ['description' => 'Ballpoint Pens (Box of 50)', 'quantity' => 3, 'unit' => 'box', 'unit_cost' => 30.00],
                    ['description' => 'Stapler with Staples', 'quantity' => 2, 'unit' => 'pcs', 'unit_cost' => 55.00],
                    ['description' => 'Ring Binders (A4)', 'quantity' => 10, 'unit' => 'pcs', 'unit_cost' => 12.00],
                    ['description' => 'Correction Fluid/Tape', 'quantity' => 5, 'unit' => 'pcs', 'unit_cost' => 8.00],
                ],
            ],
            [
                'name'          => 'IT Equipment Request',
                'description'   => 'Standard request for laptops, monitors, accessories or other IT hardware.',
                'request_type'  => 'equipment',
                'urgency'       => 'high',
                'default_title' => 'IT Equipment Request',
                'is_active'     => true,
                'default_items' => [
                    ['description' => 'Laptop — Core i5, 8GB RAM, 256GB SSD', 'quantity' => 1, 'unit' => 'pcs', 'unit_cost' => 2500.00],
                    ['description' => 'Wireless Mouse & Keyboard Combo', 'quantity' => 1, 'unit' => 'pcs', 'unit_cost' => 150.00],
                    ['description' => '24" Full-HD Monitor', 'quantity' => 1, 'unit' => 'pcs', 'unit_cost' => 800.00],
                ],
            ],
            [
                'name'          => 'Staff Training & Workshop',
                'description'   => 'Budget request for external or internal training programs and professional development workshops.',
                'request_type'  => 'fund',
                'urgency'       => 'medium',
                'default_title' => 'Staff Training — ' . now()->format('Q\q\t\r Y'),
                'is_active'     => true,
                'default_items' => [
                    ['description' => 'Training Registration Fee (per participant)', 'quantity' => 5, 'unit' => 'person', 'unit_cost' => 500.00],
                    ['description' => 'Travel & Accommodation Allowance', 'quantity' => 5, 'unit' => 'person', 'unit_cost' => 200.00],
                    ['description' => 'Course Materials & Handouts', 'quantity' => 5, 'unit' => 'pcs', 'unit_cost' => 50.00],
                ],
            ],
            [
                'name'          => 'Office Cleaning Services',
                'description'   => 'Quarterly deep-cleaning or regular janitorial service request for office premises.',
                'request_type'  => 'service',
                'urgency'       => 'low',
                'default_title' => 'Office Cleaning Service — ' . now()->format('M Y'),
                'is_active'     => true,
                'default_items' => [
                    ['description' => 'Deep Cleaning — Ground Floor', 'quantity' => 1, 'unit' => 'service', 'unit_cost' => 600.00],
                    ['description' => 'Deep Cleaning — First Floor',  'quantity' => 1, 'unit' => 'service', 'unit_cost' => 600.00],
                    ['description' => 'Cleaning Supplies & Consumables', 'quantity' => 1, 'unit' => 'lot', 'unit_cost' => 250.00],
                ],
            ],
            [
                'name'          => 'Vehicle Servicing & Maintenance',
                'description'   => 'Routine servicing, oil change, tyre check and general maintenance for company vehicles.',
                'request_type'  => 'general',
                'urgency'       => 'medium',
                'default_title' => 'Vehicle Servicing — ' . now()->format('M Y'),
                'is_active'     => true,
                'default_items' => [
                    ['description' => 'Full Vehicle Service (oil, filters, inspection)', 'quantity' => 1, 'unit' => 'service', 'unit_cost' => 1200.00],
                    ['description' => 'Tyre Replacement (if needed)', 'quantity' => 4, 'unit' => 'pcs', 'unit_cost' => 350.00],
                    ['description' => 'Car Wash & Detailing', 'quantity' => 1, 'unit' => 'service', 'unit_cost' => 80.00],
                ],
            ],
            [
                'name'          => 'Printing & Branding Materials',
                'description'   => 'Marketing and branding print jobs — banners, flyers, branded stationery, rollup stands.',
                'request_type'  => 'material',
                'urgency'       => 'medium',
                'default_title' => 'Printing & Branding Materials',
                'is_active'     => true,
                'default_items' => [
                    ['description' => 'A5 Flyers (Full Colour, 500 pcs)', 'quantity' => 500, 'unit' => 'pcs', 'unit_cost' => 1.20],
                    ['description' => 'Roll-Up Banner (85x200cm)', 'quantity' => 2, 'unit' => 'pcs', 'unit_cost' => 250.00],
                    ['description' => 'Branded Company Letterheads (Ream)', 'quantity' => 5, 'unit' => 'reams', 'unit_cost' => 120.00],
                    ['description' => 'Business Cards (Box of 500)', 'quantity' => 3, 'unit' => 'box', 'unit_cost' => 80.00],
                ],
            ],
            [
                'name'          => 'Safety & Protective Equipment (PPE)',
                'description'   => 'Standard PPE request for site workers — helmets, gloves, vests, boots, and goggles.',
                'request_type'  => 'material',
                'urgency'       => 'urgent',
                'default_title' => 'PPE Replenishment',
                'is_active'     => true,
                'default_items' => [
                    ['description' => 'Hard Hat (Safety Helmet)', 'quantity' => 10, 'unit' => 'pcs', 'unit_cost' => 35.00],
                    ['description' => 'High-Visibility Safety Vest', 'quantity' => 10, 'unit' => 'pcs', 'unit_cost' => 25.00],
                    ['description' => 'Safety Boots (assorted sizes)', 'quantity' => 10, 'unit' => 'pairs', 'unit_cost' => 120.00],
                    ['description' => 'Work Gloves', 'quantity' => 20, 'unit' => 'pairs', 'unit_cost' => 15.00],
                    ['description' => 'Safety Goggles', 'quantity' => 10, 'unit' => 'pcs', 'unit_cost' => 20.00],
                ],
            ],
            [
                'name'          => 'Software License Renewal',
                'description'   => 'Annual or quarterly renewal of software subscriptions (Office, Antivirus, ERP, design tools).',
                'request_type'  => 'service',
                'urgency'       => 'high',
                'default_title' => 'Software License Renewal — ' . now()->format('Y'),
                'is_active'     => true,
                'default_items' => [
                    ['description' => 'Microsoft 365 Business (per user/year)', 'quantity' => 10, 'unit' => 'license', 'unit_cost' => 180.00],
                    ['description' => 'Antivirus Subscription (per device/year)', 'quantity' => 10, 'unit' => 'license', 'unit_cost' => 60.00],
                    ['description' => 'Adobe Creative Cloud (per user/year)', 'quantity' => 2, 'unit' => 'license', 'unit_cost' => 650.00],
                ],
            ],
            [
                'name'          => 'Office Furniture & Fixtures',
                'description'   => 'Request for desks, chairs, filing cabinets, partitions or other office furniture.',
                'request_type'  => 'equipment',
                'urgency'       => 'low',
                'default_title' => 'Office Furniture Request',
                'is_active'     => true,
                'default_items' => [
                    ['description' => 'Ergonomic Office Chair', 'quantity' => 4, 'unit' => 'pcs', 'unit_cost' => 350.00],
                    ['description' => 'Office Work Desk (120cm)', 'quantity' => 2, 'unit' => 'pcs', 'unit_cost' => 500.00],
                    ['description' => '3-Drawer Filing Cabinet', 'quantity' => 2, 'unit' => 'pcs', 'unit_cost' => 280.00],
                ],
            ],
            [
                'name'          => 'First Aid & Medical Supplies',
                'description'   => 'Replenishment of first-aid kits, OTC medicines, and medical consumables for the office.',
                'request_type'  => 'material',
                'urgency'       => 'high',
                'default_title' => 'Medical Supplies Replenishment',
                'is_active'     => true,
                'default_items' => [
                    ['description' => 'First Aid Kit (Complete)', 'quantity' => 3, 'unit' => 'kits', 'unit_cost' => 120.00],
                    ['description' => 'Paracetamol 500mg (Box of 100)', 'quantity' => 5, 'unit' => 'box', 'unit_cost' => 25.00],
                    ['description' => 'Antiseptic Solution (500ml)', 'quantity' => 6, 'unit' => 'bottles', 'unit_cost' => 18.00],
                    ['description' => 'Adhesive Bandages / Plasters (Box)', 'quantity' => 4, 'unit' => 'box', 'unit_cost' => 12.00],
                    ['description' => 'Disposable Gloves (Box of 100)', 'quantity' => 3, 'unit' => 'box', 'unit_cost' => 35.00],
                ],
            ],
        ];
    }
}