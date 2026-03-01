# Farms Module — Audit Findings & Farmbrite-Parity Implementation Plan

**Audit Date:** 2026-03-01
**Current Completion:** ~10%
**Target:** Farmbrite-parity farm management platform
**Stack:** Filament v4 Schemas, Laravel Modules, Livewire 3

---

## 1. Current State Summary

### 1.1 What Exists

**Database Migrations (6 tables):**

| Table | Key Columns | Status |
|---|---|---|
| `farms` | name, slug, type, location, total_area, area_unit, owner_name/phone, status | OK |
| `farm_plots` | farm_id, company_id, name, area, soil_type, status | OK |
| `crop_cycles` | farm_id, farm_plot_id, company_id, crop_name, variety, season, planting/harvest dates, area, expected_yield, yield_unit, status | OK |
| `livestock_batches` | farm_id, company_id, batch_reference, animal_type, breed, initial_count, current_count, acquisition_date/cost, status | OK |
| `harvest_records` | farm_id, crop_cycle_id, company_id, harvest_date, quantity, unit, unit_price, total_revenue, buyer_name, storage_location | OK |
| `farm_expenses` | farm_id, crop_cycle_id, company_id, expense_date, category, description, amount, supplier | OK |

**Models (6):** Farm, FarmPlot, CropCycle, LivestockBatch, HarvestRecord, FarmExpense
**Policies (5):** Farm, CropCycle, LivestockBatch, HarvestRecord, FarmExpense
**Services:** FarmService — totalRevenue, totalExpenses, netProfit, recordHarvest, updateLivestockCount
**Events:** CropCycleStarted (unused)
**Filament Plugin:** FarmsFilamentPlugin — only registers FarmResource, FarmExpenseResource
**Filament Resources:** FarmResource (ViewFarm stub), FarmExpenseResource (no view page)
**Relation Managers (5):** CropCycles, FarmPlots, LivestockBatches, HarvestRecords, FarmExpenses

### 1.2 Critical Bugs (Must Fix First)

**Bug 1 — Wrong Filament v4 Action Imports (ALL files)**
Every resource and relation manager uses `Tables\Actions\*` — this is Filament v3.
Filament v4 rule: ALL actions must import from `Filament\Actions\*`.

Files affected:
- `FarmResource.php` — `Tables\Actions\ViewAction`, `EditAction`, `BulkActionGroup`, `DeleteBulkAction`
- `FarmExpenseResource.php` — `Tables\Actions\EditAction`, `DeleteAction`, `BulkActionGroup`, `DeleteBulkAction`
- `CropCyclesRelationManager.php` — `Tables\Actions\CreateAction`, `EditAction`, `DeleteAction`, `BulkActionGroup`, `DeleteBulkAction`
- `LivestockBatchesRelationManager.php` — same
- `HarvestRecordsRelationManager.php` — same
- `FarmExpensesRelationManager.php` — same
- `FarmPlotsRelationManager.php` — same

**Bug 2 — ViewFarm.php uses wrong Section/Grid imports**
Uses `Filament\Infolists\Components\Section` and `Filament\Infolists\Components\Grid`.
Must be `Filament\Schemas\Components\Section` and `Filament\Schemas\Components\Grid` in Filament v4.

**Bug 3 — Missing FarmPlot Policy**
FarmPlot model has no policy and is not registered in FarmsServiceProvider.

**Bug 4 — FarmExpenseResource missing view page**
`getPages()` has no `'view'` route. No `ViewFarmExpense.php` exists.

---

## 2. Farmbrite Gap Analysis

### 2.1 Features Farmbrite Provides vs What We Have

| Farmbrite Feature Area | Current State | Gap Level |
|---|---|---|
| Farm CRUD + Plots | Partial (basic form) | Medium |
| Crop Cycle Planning | Partial (basic) | High |
| Crop Activities / Tasks | None | Critical |
| Crop Input Applications | None (only expense categories) | High |
| Field Scouting / Observations | None | Medium |
| Livestock Batch Management | Partial (count only) | High |
| Individual Animal Health Records | None | Critical |
| Livestock Weight Tracking | None | High |
| Livestock Feed Records | None | Medium |
| Livestock Mortality Log | None (only count decrement) | High |
| Breeding Records | None | Medium |
| Farm Tasks & Checklists | None | Critical |
| Team / Worker Management | None | High |
| Harvest Records (basic) | Exists | Low |
| Produce Sales + Invoicing | None | High |
| Expense Tracking (basic) | Exists | Medium |
| Budget vs Actual | None | High |
| Finance Integration (Journals/Invoices) | None | High |
| Inventory/Supplies Management | None | High |
| Equipment Tracking | None (use Fleet module) | Medium |
| Farm Dashboard & KPIs | None | Critical |
| Crop Yield Reports | None | High |
| Livestock Reports | None | High |
| Financial P&L Reports | None | High |
| Harvest Due Alerts | None | High |
| Livestock Health Reminders | None | High |
| HR Employee Integration | None | High |

---

## 3. Implementation Plan

### Phase 1 — Fix Existing Code + View Pages + Standalone Resources
**Priority: Critical — must be done before any new work**

#### 1.1 Fix Filament v4 action imports across all files
Replace `use Filament\Tables;` blanket import + all `Tables\Actions\*` calls.
Correct pattern:
```php
use Filament\Actions\{CreateAction, EditAction, DeleteAction, ViewAction, BulkActionGroup, DeleteBulkAction};
```
All action calls: `EditAction::make()`, `DeleteAction::make()`, `ViewAction::make()`, etc.

Files to fix:
- `FarmResource.php`
- `FarmExpenseResource.php`
- All 5 RelationManagers

#### 1.2 Upgrade ViewFarm.php
Fix Section/Grid imports → `Filament\Schemas\Components\{Section, Grid}`
Add proper KPI summary section with `getStateUsing()`:
- Active crop cycles count
- Livestock batches count / total animal count
- Total harvest revenue (YTD)
- Total expenses (YTD)
- Net profit (YTD)

Full infolist sections:
- **Farm Summary** (5 KPI cards via getStateUsing)
- **Farm Identity** (name, type badge, status badge, location, area)
- **Owner Details** (owner_name, owner_phone — collapsible)
- **Description & Notes** (collapsible)
- **Audit** (created_at, updated_at — collapsed)

#### 1.3 Add FarmPlot Policy + register it
Create `FarmPlotPolicy.php` with standard permission methods.
Register in `FarmsServiceProvider::registerPolicies()`.

#### 1.4 Add ViewFarmExpense.php
Infolist: farm name, crop_cycle, date, category badge, amount, supplier, description, notes, audit.

#### 1.5 Add standalone CropCycleResource
Navigation: group 'Farms', sort 2, label 'Crop Cycles'
Form: all crop cycle fields with proper sections
Table: crop_name, farm.name, plot.name, planting_date, expected_harvest_date, status badge, expected_yield
View page: ViewCropCycle.php with P&L KPIs (total harvest revenue, total expenses, net profit, yield %, days remaining to harvest)
Relation managers: HarvestRecordsRelationManager, FarmExpensesRelationManager, CropActivitiesRelationManager (placeholder for Phase 3)

#### 1.6 Add standalone LivestockBatchResource
Navigation: group 'Farms', sort 3, label 'Livestock'
Form: animal_type, breed, acquisition_date, initial/current count, acquisition_cost, status
Table: batch_reference badge, animal_type badge, breed, farm.name, current_count, mortality_rate%, status badge
View page: ViewLivestockBatch.php with KPIs (current count, mortality rate, acquisition cost, cost per head)
Relation managers: LivestockHealthRecordsRelationManager (Phase 2), LivestockWeightRecordsRelationManager (Phase 2)

#### 1.7 Register all new resources in FarmsFilamentPlugin

---

### Phase 2 — Livestock Management Expansion

#### 2.1 New Models + Migrations

**`livestock_health_records` table:**
```
id, livestock_batch_id, farm_id, company_id,
event_type (treatment|vaccination|vet_visit|deworming|other),
event_date, description, medicine_used, dosage, cost,
administered_by (text), next_due_date, notes, timestamps
```

**`livestock_weight_records` table:**
```
id, livestock_batch_id, farm_id, company_id,
record_date, sample_size (int), avg_weight_kg (decimal:3),
min_weight_kg (decimal:3), max_weight_kg (decimal:3),
notes, timestamps
```

**`livestock_feed_records` table:**
```
id, livestock_batch_id, farm_id, company_id,
feed_date, feed_type, quantity_kg (decimal:3), unit_cost (decimal:4),
total_cost (decimal:2), notes, timestamps
```

**`livestock_mortality_logs` table:**
```
id, livestock_batch_id, farm_id, company_id,
event_date, count (int), cause (disease|injury|natural|unknown|other),
description, notes, timestamps
```

#### 2.2 New Models
- `LivestockHealthRecord` — BelongsTo LivestockBatch, Farm, Company
- `LivestockWeightRecord` — BelongsTo LivestockBatch, auto-calc daily_gain accessor
- `LivestockFeedRecord` — BelongsTo LivestockBatch; booted() auto-calc total_cost
- `LivestockMortalityLog` — BelongsTo LivestockBatch; booted() decrement batch.current_count on create

#### 2.3 Model Expansions
**LivestockBatch additions:**
- `healthRecords(): HasMany`
- `weightRecords(): HasMany`
- `feedRecords(): HasMany`
- `mortalityLogs(): HasMany`
- `getLatestWeightAttribute()` — most recent avg_weight
- `getTotalFeedCostAttribute()` — sum of feed costs

#### 2.4 FarmService Additions
- `livestockHealthSummary(LivestockBatch)` — recent events, next due treatments
- `livestockGrowthRate(LivestockBatch)` — avg daily weight gain from weight records
- `feedConversionRatio(LivestockBatch)` — total feed / weight gained

#### 2.5 New Filament Resources
- `LivestockHealthRecordResource` (standalone + RelationManager on LivestockBatchResource)
- Relation managers added to ViewLivestockBatch: HealthRecords, WeightRecords, FeedRecords, MortalityLogs

#### 2.6 Policies
- LivestockHealthRecordPolicy, LivestockWeightRecordPolicy, LivestockFeedRecordPolicy, LivestockMortalityLogPolicy

---

### Phase 3 — Crop Management Expansion

#### 3.1 New Models + Migrations

**`crop_activities` table:**
```
id, crop_cycle_id, farm_id, farm_plot_id, company_id,
activity_type (planting|weeding|spraying|irrigation|pruning|harvesting|soil_prep|other),
activity_date, description, duration_hours (decimal:2),
labour_count (int), cost (decimal:2), notes, timestamps
```

**`crop_input_applications` table:**
```
id, crop_cycle_id, farm_id, company_id,
application_date,
input_type (seed|fertilizer|pesticide|herbicide|irrigation_water|other),
product_name, quantity (decimal:4), unit, unit_cost (decimal:4),
total_cost (decimal:2), application_method, notes, timestamps
```

**`crop_scouting_records` table:**
```
id, crop_cycle_id, farm_id, farm_plot_id, company_id,
scouting_date, scouted_by (text),
finding_type (pest|disease|weed|nutrient_deficiency|weather_damage|normal|other),
severity (low|medium|high|critical),
description, recommended_action, follow_up_date, resolved_at, notes, timestamps
```

#### 3.2 New Models
- `CropActivity` — booted() auto-calc cost if duration × rate
- `CropInputApplication` — booted() auto-calc total_cost
- `CropScoutingRecord` — scoped by company_id

#### 3.3 Model Expansions
**CropCycle additions:**
- `activities(): HasMany`
- `inputApplications(): HasMany`
- `scoutingRecords(): HasMany`
- `getTotalInputCostAttribute()` — sum of input applications
- `getTotalLabourCostAttribute()` — sum of crop activities

#### 3.4 FarmService Additions
- `cropCyclePnL(CropCycle)` — returns array: revenue, input_cost, labour_cost, other_expense, net_profit, yield_achievement_%
- `yieldVsTarget(CropCycle)` — actual total harvested / expected_yield × 100
- `costPerUnit(CropCycle)` — total cost / total harvested quantity

#### 3.5 New Filament Resources
- `CropActivityResource` (standalone + RelationManager on CropCycleResource)
- `CropScoutingResource` (standalone + RelationManager on CropCycleResource)
- CropInputApplications embedded in CropCycleResource RelationManager

---

### Phase 4 — Farm Tasks & Team (HR Integration)

#### 4.1 New Models + Migrations

**`farm_workers` table:**
```
id, farm_id, company_id,
employee_id (FK → hr_employees.id, nullable),
name, role, phone, hourly_rate (decimal:4), daily_rate (decimal:2),
hire_date, is_active (bool), notes, timestamps
```

**`farm_tasks` table:**
```
id, farm_id, crop_cycle_id (nullable), livestock_batch_id (nullable), company_id,
assigned_to_id (FK → farm_workers.id, nullable),
title, description, task_type (planting|watering|weeding|feeding|treatment|harvest|maintenance|other),
priority (low|medium|high|urgent),
due_date, completed_at, status (pending|in_progress|completed|cancelled),
estimated_hours (decimal:2), actual_hours (decimal:2),
notes, timestamps
```

#### 4.2 New Models
- `FarmWorker` — BelongsTo Farm, Company; optionally BelongsTo HR\Employee; HasMany FarmTasks
- `FarmTask` — BelongsTo Farm, FarmWorker, CropCycle, LivestockBatch; booted() auto-set completed_at on status=completed

#### 4.3 Model Expansions
**Farm additions:**
- `workers(): HasMany`
- `tasks(): HasMany`

#### 4.4 HR Integration
- `FarmWorker.employee_id` FK → `hr_employees.id` (nullOnDelete, optional)
- ViewFarmWorker infolist: HR employee section with live leave status (same pattern as DriverAssignment)
- Labour cost: FarmWorker hours (from completed FarmTasks) → FarmExpense creation for category='labour'

#### 4.5 FarmService Additions
- `labourCostForCycle(CropCycle)` — sum of completed task hours × worker rate
- `openTasksByFarm(Farm)` — count of pending/in_progress tasks
- `overdueTasksByFarm(Farm)` — tasks where due_date < today and not completed

#### 4.6 New Filament Resources
- `FarmWorkerResource` — nav group 'Farms', sort 5
- `FarmTaskResource` — nav group 'Farms', sort 6
  - Table with priority badge, status badge, due_date color (red if overdue)
  - `CompleteTask` action (set status=completed, record actual_hours)
  - Relation managers on FarmResource: TasksRelationManager

#### 4.7 Policies
- FarmWorkerPolicy, FarmTaskPolicy

---

### Phase 5 — Financial Integration

#### 5.1 New Models + Migrations

**`farm_sales` table:**
```
id, farm_id, harvest_record_id (nullable), crop_cycle_id (nullable), company_id,
sale_date, buyer_name, buyer_phone,
product_description, quantity (decimal:4), unit, unit_price (decimal:4),
total_amount (decimal:2),
payment_status (unpaid|partial|paid),
invoice_id (nullable FK → invoices.id),
notes, timestamps
```

**`farm_budgets` table:**
```
id, farm_id, crop_cycle_id (nullable), company_id,
budget_name, season_year (year),
budgeted_seed_cost (decimal:2), budgeted_fertilizer_cost (decimal:2),
budgeted_pesticide_cost (decimal:2), budgeted_labour_cost (decimal:2),
budgeted_irrigation_cost (decimal:2), budgeted_equipment_cost (decimal:2),
budgeted_other_cost (decimal:2),
budgeted_revenue (decimal:2),
notes, timestamps
```

#### 5.2 New Models
- `FarmSale` — BelongsTo Farm, HarvestRecord, CropCycle, Company; optionally BelongsTo Finance\Invoice
- `FarmBudget` — BelongsTo Farm, CropCycle; accessor `getBudgetedTotalCostAttribute()`

#### 5.3 FarmService Additions
- `createHarvestInvoice(FarmSale, array $invoiceData)` — creates `Finance\Models\Invoice` + `InvoiceLines`, stores `invoice_id` on FarmSale
- `budgetVsActual(FarmBudget)` — compare budget totals vs actual expenses by category

#### 5.4 New Filament Resources
- `FarmSaleResource` — nav group 'Farms', sort 7; CreateInvoice action on view page
- `FarmBudgetResource` — nav group 'Farms', sort 8; view shows budget vs actual table

#### 5.5 Policies
- FarmSalePolicy, FarmBudgetPolicy

---

### Phase 6 — Analytics, Dashboard & Alerts

#### 6.1 FarmService Analytics Additions
- `farmDashboardStats(int $companyId)` — returns: total_farms, active_crop_cycles, total_livestock, harvest_revenue_ytd, total_expenses_ytd, net_profit_ytd, open_tasks, overdue_tasks, upcoming_harvests (within 14 days)
- `cropYieldSummary(int $companyId, string $from, string $to)` — per crop: planted_area, expected_yield, actual_yield, yield%, total_revenue, total_cost, net_profit
- `livestockPopulationSummary(int $companyId)` — per animal_type: total_batches, total_count, total_acquisition_cost, mortality_rate%
- `farmFinancialSummary(int $companyId, string $from, string $to)` — per farm: total_revenue, total_expenses, net_profit, expense_breakdown by category

#### 6.2 New Filament Pages
- **FarmDashboard** — nav icon `heroicon-o-sun`, sort 1
  - KPI cards: total farms, active crop cycles, total livestock count, YTD harvest revenue, YTD expenses, YTD net profit
  - Upcoming harvests table (within 14 days)
  - Open/overdue tasks summary
  - Top 5 farms by revenue

- **CropYieldReport** — nav icon `heroicon-o-chart-bar`, sort 9
  - Period filter (last30/MTD/QTD/YTD)
  - Per-crop summary: planted area, expected vs actual yield, yield achievement %, revenue, cost, net

- **LivestockReport** — nav icon `heroicon-o-user-group`, sort 10
  - Summary by animal type: batch count, total animals, mortality rate
  - Recent health events table

- **FarmFinancialReport** — nav icon `heroicon-o-banknotes`, sort 11
  - Period filter
  - Per-farm P&L table
  - Expense breakdown pie/bar (blade table only)

#### 6.3 Alert Commands
- `farms:harvest-due-alert {--days=14} {--company=}` — crops with expected_harvest_date within window; CommTemplate `farm_harvest_due`
- `farms:livestock-health-reminder {--days=7} {--company=}` — health records with next_due_date within window; CommTemplate `farm_livestock_health`

Scheduling:
- `farms:harvest-due-alert` — daily 07:00
- `farms:livestock-health-reminder` — daily 07:30

#### 6.4 FarmsCommTemplateSeeder
Templates:
1. `farm_harvest_due` (email) — variables: farm_name, crop_name, variety, expected_harvest_date, days_remaining, plot_name
2. `farm_harvest_due_sms` (sms) — short version
3. `farm_livestock_health` (email) — variables: farm_name, batch_reference, animal_type, treatment_type, next_due_date, days_remaining
4. `farm_livestock_health_sms` (sms) — short version
5. `farm_crop_planted` (email) — variables: farm_name, crop_name, planting_date, plot_name, expected_harvest_date
6. `farm_task_assigned` (email) — variables: worker_name, task_title, farm_name, due_date, priority

---

## 4. Cross-Module Integration Map

| Farms Feature | Module | Integration Method |
|---|---|---|
| Farm workers | HR | `farm_workers.employee_id` → `hr_employees.id` (nullable FK) |
| Worker live leave status | HR | Query `hr_leave_requests` with date overlap at infolist render |
| Labour cost tracking | HR | FarmTask hours × FarmWorker rate → FarmExpense (category='labour') |
| Produce sales invoicing | Finance | `FarmSale.invoice_id` → `invoices.id`; FarmService creates Invoice + InvoiceLines |
| Expense journal entries | Finance | FarmService.createExpenseJournalEntry() → JournalEntry + JournalLines |
| Farm supplies/inventory | ProcurementInventory | CropInputApplication.item_id → items.id (optional, Phase 3+) |
| Farm vehicles | Fleet | FarmTask.vehicle_id → vehicles.id (for transport/tractor tasks, optional) |
| Harvest/health alerts | CommunicationCentre | CommunicationService with CommTemplate codes |
| All notifications | CommunicationCentre | CommTemplates seeded in FarmsCommTemplateSeeder |

---

## 5. New Files to Create (All Phases)

### Phase 1
```
Migrations:
  (no new migrations — Phase 1 only fixes code)

Policies:
  Modules/Farms/app/Policies/FarmPlotPolicy.php

Models:
  (no changes, only fix existing)

Resources:
  Modules/Farms/app/Filament/Resources/CropCycleResource.php
  Modules/Farms/app/Filament/Resources/CropCycleResource/Pages/ListCropCycles.php
  Modules/Farms/app/Filament/Resources/CropCycleResource/Pages/CreateCropCycle.php
  Modules/Farms/app/Filament/Resources/CropCycleResource/Pages/EditCropCycle.php
  Modules/Farms/app/Filament/Resources/CropCycleResource/Pages/ViewCropCycle.php
  Modules/Farms/app/Filament/Resources/LivestockBatchResource.php
  Modules/Farms/app/Filament/Resources/LivestockBatchResource/Pages/ListLivestockBatches.php
  Modules/Farms/app/Filament/Resources/LivestockBatchResource/Pages/CreateLivestockBatch.php
  Modules/Farms/app/Filament/Resources/LivestockBatchResource/Pages/EditLivestockBatch.php
  Modules/Farms/app/Filament/Resources/LivestockBatchResource/Pages/ViewLivestockBatch.php
  Modules/Farms/app/Filament/Resources/FarmExpenseResource/Pages/ViewFarmExpense.php
```

### Phase 2
```
Migrations:
  Modules/Farms/database/migrations/2026_03_01_100001_create_livestock_health_records_table.php
  Modules/Farms/database/migrations/2026_03_01_100002_create_livestock_weight_records_table.php
  Modules/Farms/database/migrations/2026_03_01_100003_create_livestock_feed_records_table.php
  Modules/Farms/database/migrations/2026_03_01_100004_create_livestock_mortality_logs_table.php

Models:
  Modules/Farms/app/Models/LivestockHealthRecord.php
  Modules/Farms/app/Models/LivestockWeightRecord.php
  Modules/Farms/app/Models/LivestockFeedRecord.php
  Modules/Farms/app/Models/LivestockMortalityLog.php

Policies:
  Modules/Farms/app/Policies/LivestockHealthRecordPolicy.php
  Modules/Farms/app/Policies/LivestockWeightRecordPolicy.php
  Modules/Farms/app/Policies/LivestockFeedRecordPolicy.php
  Modules/Farms/app/Policies/LivestockMortalityLogPolicy.php

Resources:
  Modules/Farms/app/Filament/Resources/LivestockHealthRecordResource.php
  Modules/Farms/app/Filament/Resources/LivestockHealthRecordResource/Pages/ (List/Create/Edit/View)
  RelationManagers added to LivestockBatchResource: HealthRecords, WeightRecords, FeedRecords, MortalityLogs
```

### Phase 3
```
Migrations:
  Modules/Farms/database/migrations/2026_03_01_200001_create_crop_activities_table.php
  Modules/Farms/database/migrations/2026_03_01_200002_create_crop_input_applications_table.php
  Modules/Farms/database/migrations/2026_03_01_200003_create_crop_scouting_records_table.php

Models:
  Modules/Farms/app/Models/CropActivity.php
  Modules/Farms/app/Models/CropInputApplication.php
  Modules/Farms/app/Models/CropScoutingRecord.php

Policies:
  Modules/Farms/app/Policies/CropActivityPolicy.php
  Modules/Farms/app/Policies/CropInputApplicationPolicy.php
  Modules/Farms/app/Policies/CropScoutingRecordPolicy.php

Resources:
  Modules/Farms/app/Filament/Resources/CropActivityResource.php (+ Pages)
  Modules/Farms/app/Filament/Resources/CropScoutingResource.php (+ Pages)
  RelationManagers added to CropCycleResource: Activities, InputApplications, ScoutingRecords
```

### Phase 4
```
Migrations:
  Modules/Farms/database/migrations/2026_03_01_300001_create_farm_workers_table.php
  Modules/Farms/database/migrations/2026_03_01_300002_create_farm_tasks_table.php

Models:
  Modules/Farms/app/Models/FarmWorker.php
  Modules/Farms/app/Models/FarmTask.php

Policies:
  Modules/Farms/app/Policies/FarmWorkerPolicy.php
  Modules/Farms/app/Policies/FarmTaskPolicy.php

Resources:
  Modules/Farms/app/Filament/Resources/FarmWorkerResource.php (+ Pages)
  Modules/Farms/app/Filament/Resources/FarmTaskResource.php (+ Pages)
```

### Phase 5
```
Migrations:
  Modules/Farms/database/migrations/2026_03_01_400001_create_farm_sales_table.php
  Modules/Farms/database/migrations/2026_03_01_400002_create_farm_budgets_table.php

Models:
  Modules/Farms/app/Models/FarmSale.php
  Modules/Farms/app/Models/FarmBudget.php

Policies:
  Modules/Farms/app/Policies/FarmSalePolicy.php
  Modules/Farms/app/Policies/FarmBudgetPolicy.php

Resources:
  Modules/Farms/app/Filament/Resources/FarmSaleResource.php (+ Pages)
  Modules/Farms/app/Filament/Resources/FarmBudgetResource.php (+ Pages)
```

### Phase 6
```
Pages:
  Modules/Farms/app/Filament/Pages/FarmDashboard.php
  Modules/Farms/app/Filament/Pages/CropYieldReport.php
  Modules/Farms/app/Filament/Pages/LivestockReport.php
  Modules/Farms/app/Filament/Pages/FarmFinancialReport.php

Views:
  Modules/Farms/resources/views/filament/pages/farm-dashboard.blade.php
  Modules/Farms/resources/views/filament/pages/crop-yield-report.blade.php
  Modules/Farms/resources/views/filament/pages/livestock-report.blade.php
  Modules/Farms/resources/views/filament/pages/farm-financial-report.blade.php

Commands:
  Modules/Farms/app/Console/Commands/FarmsHarvestDueAlertCommand.php
  Modules/Farms/app/Console/Commands/FarmsLivestockHealthReminderCommand.php

Seeders:
  Modules/CommunicationCentre/database/seeders/FarmsCommTemplateSeeder.php
```

---

## 6. Model Column Summary (Reference)

### Models to Add/Expand

| Model | New Fields |
|---|---|
| LivestockBatch | (via relations) healthRecords, weightRecords, feedRecords, mortalityLogs |
| CropCycle | (via relations) activities, inputApplications, scoutingRecords |
| Farm | (via relations) workers, tasks, sales, budgets |

---

## 7. Navigation Structure (Final)

```
Farms (group)
  [1] Farm Dashboard (page)
  [2] Farms (FarmResource)
  [3] Crop Cycles (CropCycleResource)
  [4] Livestock (LivestockBatchResource)
  [5] Livestock Health (LivestockHealthRecordResource)
  [6] Crop Activities (CropActivityResource)
  [7] Scouting (CropScoutingResource)
  [8] Farm Workers (FarmWorkerResource)
  [9] Farm Tasks (FarmTaskResource)
  [10] Farm Sales (FarmSaleResource)
  [11] Farm Budgets (FarmBudgetResource)
  [12] Expenses (FarmExpenseResource)
  [13] Crop Yield Report (page)
  [14] Livestock Report (page)
  [15] Financial Report (page)
```

---

## 8. Estimated Scope

| Phase | New Files | DB Changes | Complexity |
|---|---|---|---|
| Phase 1 — Fix + Core UI | ~12 files | None | Medium |
| Phase 2 — Livestock Expansion | ~16 files | 4 new tables | Medium |
| Phase 3 — Crop Expansion | ~14 files | 3 new tables | Medium |
| Phase 4 — Tasks & HR | ~10 files | 2 new tables + 1 FK | Medium |
| Phase 5 — Financial | ~12 files | 2 new tables + 1 FK | High (cross-module) |
| Phase 6 — Analytics | ~14 files | None | Medium |
| **Total** | **~78 files** | **11 new tables** | |
