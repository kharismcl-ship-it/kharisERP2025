<?php

namespace Modules\Construction\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConstructionProjectsSeeder extends Seeder
{
    public function run(): void
    {
        $company = DB::table('companies')->first();

        if (! $company) {
            $this->command->warn('No company found. Skipping ConstructionProjectsSeeder.');
            return;
        }

        $companyId = $company->id;
        $now = now();

        // ─── Project 1: Accra Office Complex ───────────────────────────────────
        DB::table('construction_projects')->updateOrInsert(
            ['company_id' => $companyId, 'slug' => 'accra-office-complex'],
            [
                'company_id'        => $companyId,
                'name'              => 'Accra Office Complex',
                'slug'              => 'accra-office-complex',
                'description'       => 'Multi-storey commercial office complex in Accra CBD.',
                'location'          => 'Accra Central Business District',
                'client_name'       => 'Skyline Developments Ltd',
                'client_contact'    => 'Kwame Asante',
                'client_email'      => 'k.asante@skylinedev.gh',
                'client_phone'      => '+233 24 111 2222',
                'project_manager'   => 'Ama Boateng',
                'start_date'        => '2025-01-15',
                'expected_end_date' => '2026-06-30',
                'actual_end_date'   => null,
                'contract_value'    => 2500000.00,
                'budget'            => 2500000.00,
                'total_spent'       => 980000.00,
                'status'            => 'active',
                'notes'             => 'Phase 1 (Foundation) completed on schedule.',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]
        );
        $project1 = DB::table('construction_projects')
            ->where('company_id', $companyId)
            ->where('slug', 'accra-office-complex')
            ->first();

        // Phases for Project 1
        DB::table('project_phases')->updateOrInsert(
            ['construction_project_id' => $project1->id, 'name' => 'Foundation'],
            [
                'construction_project_id' => $project1->id,
                'company_id'              => $companyId,
                'name'                    => 'Foundation',
                'description'             => 'Site preparation, excavation and foundation works.',
                'order'                   => 1,
                'planned_start'           => '2025-01-15',
                'planned_end'             => '2025-04-30',
                'actual_start'            => '2025-01-20',
                'actual_end'              => '2025-05-10',
                'budget'                  => 450000.00,
                'spent'                   => 462000.00,
                'progress_percent'        => 100,
                'status'                  => 'completed',
                'created_at'              => $now,
                'updated_at'              => $now,
            ]
        );
        DB::table('project_phases')->updateOrInsert(
            ['construction_project_id' => $project1->id, 'name' => 'Structural Frame'],
            [
                'construction_project_id' => $project1->id,
                'company_id'              => $companyId,
                'name'                    => 'Structural Frame',
                'description'             => 'Steel and concrete structural framework erection.',
                'order'                   => 2,
                'planned_start'           => '2025-05-01',
                'planned_end'             => '2025-11-30',
                'actual_start'            => '2025-05-15',
                'actual_end'              => null,
                'budget'                  => 900000.00,
                'spent'                   => 518000.00,
                'progress_percent'        => 65,
                'status'                  => 'in_progress',
                'created_at'              => $now,
                'updated_at'              => $now,
            ]
        );
        DB::table('project_phases')->updateOrInsert(
            ['construction_project_id' => $project1->id, 'name' => 'Finishing & MEP'],
            [
                'construction_project_id' => $project1->id,
                'company_id'              => $companyId,
                'name'                    => 'Finishing & MEP',
                'description'             => 'Interior finishing, mechanical, electrical and plumbing.',
                'order'                   => 3,
                'planned_start'           => '2025-12-01',
                'planned_end'             => '2026-06-30',
                'actual_start'            => null,
                'actual_end'              => null,
                'budget'                  => 1150000.00,
                'spent'                   => 0.00,
                'progress_percent'        => 0,
                'status'                  => 'pending',
                'created_at'              => $now,
                'updated_at'              => $now,
            ]
        );

        $phase1_1 = DB::table('project_phases')
            ->where('construction_project_id', $project1->id)
            ->where('name', 'Foundation')
            ->first();
        $phase1_2 = DB::table('project_phases')
            ->where('construction_project_id', $project1->id)
            ->where('name', 'Structural Frame')
            ->first();
        $phase1_3 = DB::table('project_phases')
            ->where('construction_project_id', $project1->id)
            ->where('name', 'Finishing & MEP')
            ->first();

        // Tasks for Project 1
        $tasks1 = [
            [
                'name'             => 'Site Survey & Soil Testing',
                'project_phase_id' => $phase1_1->id,
                'status'           => 'completed',
                'priority'         => 3,
                'due_date'         => '2025-01-25',
                'completed_at'     => '2025-01-24',
            ],
            [
                'name'             => 'Excavation Works',
                'project_phase_id' => $phase1_1->id,
                'status'           => 'completed',
                'priority'         => 3,
                'due_date'         => '2025-02-28',
                'completed_at'     => '2025-03-05',
            ],
            [
                'name'             => 'Concrete Pouring — Ground Floor Slab',
                'project_phase_id' => $phase1_2->id,
                'status'           => 'completed',
                'priority'         => 3,
                'due_date'         => '2025-06-30',
                'completed_at'     => '2025-07-02',
            ],
            [
                'name'             => 'Steel Column Erection — Floors 1–4',
                'project_phase_id' => $phase1_2->id,
                'status'           => 'in_progress',
                'priority'         => 3,
                'due_date'         => '2025-10-31',
                'completed_at'     => null,
            ],
            [
                'name'             => 'Electrical Conduit Rough-In',
                'project_phase_id' => $phase1_3->id,
                'status'           => 'pending',
                'priority'         => 2,
                'due_date'         => '2026-01-31',
                'completed_at'     => null,
            ],
            [
                'name'             => 'Interior Plastering & Painting',
                'project_phase_id' => $phase1_3->id,
                'status'           => 'pending',
                'priority'         => 1,
                'due_date'         => '2026-04-30',
                'completed_at'     => null,
            ],
        ];
        foreach ($tasks1 as $task) {
            DB::table('project_tasks')->updateOrInsert(
                ['construction_project_id' => $project1->id, 'name' => $task['name']],
                array_merge($task, [
                    'construction_project_id' => $project1->id,
                    'company_id'              => $companyId,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ])
            );
        }

        // Material Usage for Project 1
        $materials1 = [
            ['phase' => $phase1_1->id, 'material_name' => 'Portland Cement (50kg bags)', 'unit' => 'bags',  'quantity' => 2400,   'unit_cost' => 85.00,    'total_cost' => 204000.00, 'usage_date' => '2025-02-10', 'supplier' => 'Ghana Cement Ltd'],
            ['phase' => $phase1_1->id, 'material_name' => 'Reinforced Steel Bar (12mm)', 'unit' => 'tonnes','quantity' => 32.5,    'unit_cost' => 4200.00,  'total_cost' => 136500.00, 'usage_date' => '2025-02-20', 'supplier' => 'Accra Steel Works'],
            ['phase' => $phase1_2->id, 'material_name' => 'Structural Steel Sections',  'unit' => 'tonnes','quantity' => 85.0,    'unit_cost' => 5800.00,  'total_cost' => 493000.00, 'usage_date' => '2025-06-15', 'supplier' => 'West Africa Steel Co.'],
            ['phase' => $phase1_2->id, 'material_name' => 'Ready-Mix Concrete (25 MPa)','unit' => 'm³',    'quantity' => 450.0,   'unit_cost' => 550.00,   'total_cost' => 247500.00, 'usage_date' => '2025-07-05', 'supplier' => 'AccraMix Concrete'],
            ['phase' => $phase1_1->id, 'material_name' => 'Aggregate (Granite Chips)',  'unit' => 'tonnes','quantity' => 180.0,   'unit_cost' => 200.00,   'total_cost' => 36000.00,  'usage_date' => '2025-03-01', 'supplier' => 'Quarry Masters GH'],
        ];
        foreach ($materials1 as $mat) {
            DB::table('material_usages')->updateOrInsert(
                ['construction_project_id' => $project1->id, 'material_name' => $mat['material_name'], 'usage_date' => $mat['usage_date']],
                [
                    'construction_project_id' => $project1->id,
                    'project_phase_id'         => $mat['phase'],
                    'company_id'               => $companyId,
                    'material_name'            => $mat['material_name'],
                    'unit'                     => $mat['unit'],
                    'quantity'                 => $mat['quantity'],
                    'unit_cost'                => $mat['unit_cost'],
                    'total_cost'               => $mat['total_cost'],
                    'usage_date'               => $mat['usage_date'],
                    'supplier'                 => $mat['supplier'],
                    'created_at'               => $now,
                    'updated_at'               => $now,
                ]
            );
        }

        // Budget Items for Project 1
        $budget1 = [
            ['category' => 'labour',      'description' => 'Skilled and unskilled labour costs', 'budgeted_amount' => 600000.00, 'actual_amount' => 320000.00],
            ['category' => 'materials',   'description' => 'All building materials procurement',  'budgeted_amount' => 900000.00, 'actual_amount' => 498000.00],
            ['category' => 'equipment',   'description' => 'Heavy machinery hire and fuel',        'budgeted_amount' => 250000.00, 'actual_amount' => 162000.00],
            ['category' => 'overhead',    'description' => 'Site administration and insurance',    'budgeted_amount' => 120000.00, 'actual_amount' =>  72000.00],
        ];
        foreach ($budget1 as $item) {
            DB::table('project_budget_items')->updateOrInsert(
                ['construction_project_id' => $project1->id, 'category' => $item['category'], 'description' => $item['description']],
                array_merge($item, [
                    'construction_project_id' => $project1->id,
                    'company_id'              => $companyId,
                    'notes'                   => null,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ])
            );
        }

        // ─── Project 2: Highway Bridge Rehabilitation ──────────────────────────
        DB::table('construction_projects')->updateOrInsert(
            ['company_id' => $companyId, 'slug' => 'highway-bridge-rehabilitation'],
            [
                'company_id'        => $companyId,
                'name'              => 'Highway Bridge Rehabilitation',
                'slug'              => 'highway-bridge-rehabilitation',
                'description'       => 'Structural rehabilitation of the N1 highway bridge over the Densu River.',
                'location'          => 'Nsawam Road, Greater Accra',
                'client_name'       => 'Ghana Highway Authority',
                'client_contact'    => 'Ing. Kofi Mensah',
                'client_email'      => 'k.mensah@gha.gov.gh',
                'client_phone'      => '+233 30 261 9900',
                'project_manager'   => 'Eric Darko',
                'start_date'        => '2026-02-01',
                'expected_end_date' => '2026-10-31',
                'actual_end_date'   => null,
                'contract_value'    => 800000.00,
                'budget'            => 800000.00,
                'total_spent'       => 0.00,
                'status'            => 'planning',
                'notes'             => 'Mobilisation pending budget release from GHA.',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]
        );
        $project2 = DB::table('construction_projects')
            ->where('company_id', $companyId)
            ->where('slug', 'highway-bridge-rehabilitation')
            ->first();

        DB::table('project_phases')->updateOrInsert(
            ['construction_project_id' => $project2->id, 'name' => 'Condition Assessment'],
            [
                'construction_project_id' => $project2->id,
                'company_id'              => $companyId,
                'name'                    => 'Condition Assessment',
                'description'             => 'Structural inspection and detailed engineering assessment.',
                'order'                   => 1,
                'planned_start'           => '2026-02-15',
                'planned_end'             => '2026-03-31',
                'actual_start'            => null,
                'actual_end'              => null,
                'budget'                  => 40000.00,
                'spent'                   => 0.00,
                'progress_percent'        => 0,
                'status'                  => 'pending',
                'created_at'              => $now,
                'updated_at'              => $now,
            ]
        );
        DB::table('project_phases')->updateOrInsert(
            ['construction_project_id' => $project2->id, 'name' => 'Main Rehabilitation'],
            [
                'construction_project_id' => $project2->id,
                'company_id'              => $companyId,
                'name'                    => 'Main Rehabilitation',
                'description'             => 'Structural repairs, deck overlay, and bearing replacement.',
                'order'                   => 2,
                'planned_start'           => '2026-04-01',
                'planned_end'             => '2026-10-31',
                'actual_start'            => null,
                'actual_end'              => null,
                'budget'                  => 760000.00,
                'spent'                   => 0.00,
                'progress_percent'        => 0,
                'status'                  => 'pending',
                'created_at'              => $now,
                'updated_at'              => $now,
            ]
        );

        $phase2_1 = DB::table('project_phases')
            ->where('construction_project_id', $project2->id)
            ->where('name', 'Condition Assessment')
            ->first();
        $phase2_2 = DB::table('project_phases')
            ->where('construction_project_id', $project2->id)
            ->where('name', 'Main Rehabilitation')
            ->first();

        $tasks2 = [
            ['name' => 'Load Test & Structural Analysis',   'project_phase_id' => $phase2_1->id, 'status' => 'pending', 'priority' => 3, 'due_date' => '2026-03-15'],
            ['name' => 'Deck Overlay & Waterproofing',      'project_phase_id' => $phase2_2->id, 'status' => 'pending', 'priority' => 3, 'due_date' => '2026-07-31'],
            ['name' => 'Expansion Joint Replacement',       'project_phase_id' => $phase2_2->id, 'status' => 'pending', 'priority' => 2, 'due_date' => '2026-09-30'],
        ];
        foreach ($tasks2 as $task) {
            DB::table('project_tasks')->updateOrInsert(
                ['construction_project_id' => $project2->id, 'name' => $task['name']],
                array_merge($task, [
                    'construction_project_id' => $project2->id,
                    'company_id'              => $companyId,
                    'completed_at'            => null,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ])
            );
        }

        $materials2 = [
            ['phase' => $phase2_2->id, 'material_name' => 'Epoxy Concrete Overlay Mix', 'unit' => 'bags',  'quantity' => 500.0, 'unit_cost' => 320.00, 'total_cost' => 160000.00, 'usage_date' => '2026-05-01', 'supplier' => 'Bridge Supplies Ltd'],
            ['phase' => $phase2_2->id, 'material_name' => 'Elastomeric Bridge Bearings','unit' => 'units', 'quantity' => 24.0,  'unit_cost' => 4500.00,'total_cost' => 108000.00, 'usage_date' => '2026-06-01', 'supplier' => 'Freyssinet GH'],
        ];
        foreach ($materials2 as $mat) {
            DB::table('material_usages')->updateOrInsert(
                ['construction_project_id' => $project2->id, 'material_name' => $mat['material_name'], 'usage_date' => $mat['usage_date']],
                [
                    'construction_project_id' => $project2->id,
                    'project_phase_id'         => $mat['phase'],
                    'company_id'               => $companyId,
                    'material_name'            => $mat['material_name'],
                    'unit'                     => $mat['unit'],
                    'quantity'                 => $mat['quantity'],
                    'unit_cost'                => $mat['unit_cost'],
                    'total_cost'               => $mat['total_cost'],
                    'usage_date'               => $mat['usage_date'],
                    'supplier'                 => $mat['supplier'],
                    'created_at'               => $now,
                    'updated_at'               => $now,
                ]
            );
        }

        $budget2 = [
            ['category' => 'labour',    'description' => 'Specialist bridge repair labour',   'budgeted_amount' => 200000.00, 'actual_amount' => 0.00],
            ['category' => 'materials', 'description' => 'Structural repair materials',       'budgeted_amount' => 380000.00, 'actual_amount' => 0.00],
            ['category' => 'equipment', 'description' => 'Specialist bridge equipment hire',  'budgeted_amount' => 150000.00, 'actual_amount' => 0.00],
        ];
        foreach ($budget2 as $item) {
            DB::table('project_budget_items')->updateOrInsert(
                ['construction_project_id' => $project2->id, 'category' => $item['category'], 'description' => $item['description']],
                array_merge($item, [
                    'construction_project_id' => $project2->id,
                    'company_id'              => $companyId,
                    'notes'                   => null,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ])
            );
        }

        // ─── Project 3: School Building — Kumasi ──────────────────────────────
        DB::table('construction_projects')->updateOrInsert(
            ['company_id' => $companyId, 'slug' => 'school-building-kumasi'],
            [
                'company_id'        => $companyId,
                'name'              => 'School Building — Kumasi',
                'slug'              => 'school-building-kumasi',
                'description'       => '12-classroom block for Kumasi Metropolitan Assembly.',
                'location'          => 'Asokwa, Kumasi',
                'client_name'       => 'Kumasi Metropolitan Assembly',
                'client_contact'    => 'Madam Akua Owusu',
                'client_email'      => 'a.owusu@kma.gov.gh',
                'client_phone'      => '+233 32 202 5050',
                'project_manager'   => 'Samuel Osei',
                'start_date'        => '2024-06-01',
                'expected_end_date' => '2025-04-30',
                'actual_end_date'   => '2025-05-15',
                'contract_value'    => 450000.00,
                'budget'            => 450000.00,
                'total_spent'       => 438500.00,
                'status'            => 'completed',
                'notes'             => 'Project completed within budget. Defects liability period active.',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]
        );
        $project3 = DB::table('construction_projects')
            ->where('company_id', $companyId)
            ->where('slug', 'school-building-kumasi')
            ->first();

        DB::table('project_phases')->updateOrInsert(
            ['construction_project_id' => $project3->id, 'name' => 'Substructure'],
            [
                'construction_project_id' => $project3->id,
                'company_id'              => $companyId,
                'name'                    => 'Substructure',
                'description'             => 'Foundation and ground floor construction.',
                'order'                   => 1,
                'planned_start'           => '2024-06-01',
                'planned_end'             => '2024-09-30',
                'actual_start'            => '2024-06-05',
                'actual_end'              => '2024-09-28',
                'budget'                  => 120000.00,
                'spent'                   => 115000.00,
                'progress_percent'        => 100,
                'status'                  => 'completed',
                'created_at'              => $now,
                'updated_at'              => $now,
            ]
        );
        DB::table('project_phases')->updateOrInsert(
            ['construction_project_id' => $project3->id, 'name' => 'Superstructure & Finishes'],
            [
                'construction_project_id' => $project3->id,
                'company_id'              => $companyId,
                'name'                    => 'Superstructure & Finishes',
                'description'             => 'Block walls, roofing, doors, windows, and finishes.',
                'order'                   => 2,
                'planned_start'           => '2024-10-01',
                'planned_end'             => '2025-04-30',
                'actual_start'            => '2024-10-03',
                'actual_end'              => '2025-05-15',
                'budget'                  => 330000.00,
                'spent'                   => 323500.00,
                'progress_percent'        => 100,
                'status'                  => 'completed',
                'created_at'              => $now,
                'updated_at'              => $now,
            ]
        );

        $phase3_1 = DB::table('project_phases')
            ->where('construction_project_id', $project3->id)
            ->where('name', 'Substructure')
            ->first();
        $phase3_2 = DB::table('project_phases')
            ->where('construction_project_id', $project3->id)
            ->where('name', 'Superstructure & Finishes')
            ->first();

        $tasks3 = [
            ['name' => 'Strip Footing & DPC',               'project_phase_id' => $phase3_1->id, 'status' => 'completed', 'priority' => 3, 'due_date' => '2024-07-31', 'completed_at' => '2024-07-29'],
            ['name' => 'Ground Floor Slab',                 'project_phase_id' => $phase3_1->id, 'status' => 'completed', 'priority' => 3, 'due_date' => '2024-09-30', 'completed_at' => '2024-09-28'],
            ['name' => 'Block Wall Construction',           'project_phase_id' => $phase3_2->id, 'status' => 'completed', 'priority' => 3, 'due_date' => '2025-01-31', 'completed_at' => '2025-01-28'],
            ['name' => 'Roofing, Doors & Window Frames',   'project_phase_id' => $phase3_2->id, 'status' => 'completed', 'priority' => 2, 'due_date' => '2025-04-30', 'completed_at' => '2025-05-15'],
        ];
        foreach ($tasks3 as $task) {
            DB::table('project_tasks')->updateOrInsert(
                ['construction_project_id' => $project3->id, 'name' => $task['name']],
                array_merge($task, [
                    'construction_project_id' => $project3->id,
                    'company_id'              => $companyId,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ])
            );
        }

        $materials3 = [
            ['phase' => $phase3_1->id, 'material_name' => 'Portland Cement (50kg bags)', 'unit' => 'bags',  'quantity' => 800.0,  'unit_cost' => 85.00,  'total_cost' => 68000.00, 'usage_date' => '2024-07-01', 'supplier' => 'Ghana Cement Ltd'],
            ['phase' => $phase3_1->id, 'material_name' => 'BRC Mesh A393',               'unit' => 'sheets','quantity' => 120.0,  'unit_cost' => 220.00, 'total_cost' => 26400.00, 'usage_date' => '2024-08-15', 'supplier' => 'Accra Steel Works'],
            ['phase' => $phase3_2->id, 'material_name' => 'Sandcrete Blocks (6")',        'unit' => 'units', 'quantity' => 24000.0,'unit_cost' => 4.50,   'total_cost' => 108000.00,'usage_date' => '2024-10-10', 'supplier' => 'Kumasi Block Factory'],
            ['phase' => $phase3_2->id, 'material_name' => 'Corrugated Aluminium Roofing Sheet', 'unit' => 'sheets', 'quantity' => 360.0, 'unit_cost' => 120.00, 'total_cost' => 43200.00, 'usage_date' => '2025-02-01', 'supplier' => 'Roofex GH'],
        ];
        foreach ($materials3 as $mat) {
            DB::table('material_usages')->updateOrInsert(
                ['construction_project_id' => $project3->id, 'material_name' => $mat['material_name'], 'usage_date' => $mat['usage_date']],
                [
                    'construction_project_id' => $project3->id,
                    'project_phase_id'         => $mat['phase'],
                    'company_id'               => $companyId,
                    'material_name'            => $mat['material_name'],
                    'unit'                     => $mat['unit'],
                    'quantity'                 => $mat['quantity'],
                    'unit_cost'                => $mat['unit_cost'],
                    'total_cost'               => $mat['total_cost'],
                    'usage_date'               => $mat['usage_date'],
                    'supplier'                 => $mat['supplier'],
                    'created_at'               => $now,
                    'updated_at'               => $now,
                ]
            );
        }

        $budget3 = [
            ['category' => 'labour',    'description' => 'Construction labour costs',    'budgeted_amount' => 130000.00, 'actual_amount' => 124000.00],
            ['category' => 'materials', 'description' => 'All building materials',        'budgeted_amount' => 220000.00, 'actual_amount' => 215500.00],
            ['category' => 'equipment', 'description' => 'Plant and machinery hire',      'budgeted_amount' =>  60000.00, 'actual_amount' =>  58000.00],
        ];
        foreach ($budget3 as $item) {
            DB::table('project_budget_items')->updateOrInsert(
                ['construction_project_id' => $project3->id, 'category' => $item['category'], 'description' => $item['description']],
                array_merge($item, [
                    'construction_project_id' => $project3->id,
                    'company_id'              => $companyId,
                    'notes'                   => null,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ])
            );
        }

        $this->command->info('ConstructionProjectsSeeder: 3 projects seeded successfully.');
    }
}
