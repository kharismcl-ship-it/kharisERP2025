<?php

namespace Modules\Requisition\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\Models\Employee;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionItem;

class RequisitionDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RequisitionCommTemplateSeeder::class);
        $this->call(RequisitionTemplateSeeder::class);

        $this->seedSampleRequisitions();
    }

    private function seedSampleRequisitions(): void
    {
        // Need at least one employee to act as requester
        $employees = Employee::withoutGlobalScopes()->limit(5)->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found — skipping sample requisitions.');
            return;
        }

        $samples = [
            [
                'request_type'         => 'material',
                'title'                => 'Office Stationery Replenishment',
                'description'          => 'Monthly replenishment of pens, papers, folders and other stationery items.',
                'urgency'              => 'medium',
                'status'               => 'submitted',
                'total_estimated_cost' => 850.00,
                'items' => [
                    ['description' => 'A4 Printing Paper (Reams)', 'quantity' => 10, 'unit' => 'reams', 'unit_cost' => 45.00],
                    ['description' => 'Ballpoint Pens (Box)', 'quantity' => 5, 'unit' => 'box',   'unit_cost' => 30.00],
                    ['description' => 'Stapler + Staples Set',  'quantity' => 3, 'unit' => 'pcs',  'unit_cost' => 55.00],
                ],
            ],
            [
                'request_type'         => 'equipment',
                'title'                => 'Laptop Replacement for Finance Team',
                'description'          => 'Three laptops in the finance department have exceeded their useful life and require replacement.',
                'urgency'              => 'high',
                'status'               => 'approved',
                'total_estimated_cost' => 9600.00,
                'items' => [
                    ['description' => 'Laptop — Core i7, 16GB RAM, 512GB SSD', 'quantity' => 3, 'unit' => 'pcs', 'unit_cost' => 3200.00],
                ],
            ],
            [
                'request_type'         => 'fund',
                'title'                => 'Staff Training Budget — Q2',
                'description'          => 'Budget request for external skills training for operations staff in Q2.',
                'urgency'              => 'medium',
                'status'               => 'draft',
                'total_estimated_cost' => 5000.00,
                'items' => [
                    ['description' => 'Safety & Compliance Training (per head)', 'quantity' => 10, 'unit' => 'person', 'unit_cost' => 300.00],
                    ['description' => 'Leadership Workshop', 'quantity' => 4, 'unit' => 'person', 'unit_cost' => 500.00],
                ],
            ],
            [
                'request_type'         => 'service',
                'title'                => 'Office Deep Cleaning Service',
                'description'          => 'Quarterly deep cleaning of all office floors and common areas.',
                'urgency'              => 'low',
                'status'               => 'fulfilled',
                'total_estimated_cost' => 1200.00,
                'items' => [
                    ['description' => 'Deep Cleaning — Ground Floor', 'quantity' => 1, 'unit' => 'service', 'unit_cost' => 600.00],
                    ['description' => 'Deep Cleaning — First Floor',  'quantity' => 1, 'unit' => 'service', 'unit_cost' => 600.00],
                ],
            ],
            [
                'request_type'         => 'general',
                'title'                => 'Vehicle Maintenance & Servicing',
                'description'          => 'Routine servicing for 2 company vehicles due in March.',
                'urgency'              => 'medium',
                'status'               => 'under_review',
                'total_estimated_cost' => 2400.00,
                'items' => [
                    ['description' => 'Full Service — Toyota Hilux (GR-1234-22)',     'quantity' => 1, 'unit' => 'service', 'unit_cost' => 1200.00],
                    ['description' => 'Full Service — Mitsubishi L200 (GR-5678-21)',  'quantity' => 1, 'unit' => 'service', 'unit_cost' => 1200.00],
                ],
            ],
        ];

        foreach ($samples as $index => $data) {
            $employee = $employees[$index % $employees->count()];

            $items = $data['items'];
            unset($data['items']);

            $requisition = Requisition::withoutGlobalScopes()->create(array_merge($data, [
                'company_id'            => $employee->company_id,
                'requester_employee_id' => $employee->id,
            ]));

            foreach ($items as $item) {
                RequisitionItem::create(array_merge($item, [
                    'requisition_id' => $requisition->id,
                    'total_cost'     => ($item['quantity'] ?? 1) * ($item['unit_cost'] ?? 0),
                ]));
            }
        }

        $this->command->info('Requisition: seeded ' . count($samples) . ' sample requisitions with items.');
    }
}