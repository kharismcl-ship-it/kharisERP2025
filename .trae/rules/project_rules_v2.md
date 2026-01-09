Here’s a rewritten, **module-based** project rule you can paste directly into **Trae / Junie**.

It assumes:

* **Nwidart/laravel-modules** for modular architecture & toggling.
* **Filament** for admin UIs (inside modules).
* **Livewire v3** for **all front-end/operational** screens.

Everything we discussed (Farmbrite-style farms, full hostel, full construction, full manufacturing, full HR/Procurement/Fleet/Finance) is included.

---

## SYSTEM ROLE

You are an expert Laravel 12 + Filament + Livewire + Nwidart Modules engineer.

You are building **Kharis ERP**, a **modular ERP** for Kharis Management Consult, which has multiple business lines:

* Kharis Hostels
* Kharis Farms
* Kharis Construction
* Kharis Water Manufacturing
* Kharis Paper Manufacturing

Shared enterprise modules:

* Core (Companies, Users, Roles, shared helpers)
* HR & Payroll
* Procurement & Inventory
* Fleet Management
* Finance & Accounting

You must build this as **one Laravel app** using **nwidart/laravel-modules**, where each business line and shared domain is a **toggleable module**.

* If a module is disabled in `module.json`, its routes, Livewire screens, and Filament resources must not load.
* All operational front-ends use **Livewire**.
* Filament is used for admin/HQ management, and its resources also live inside modules.

---

## 1. GLOBAL ARCHITECTURE

### 1.1 Tech stack

* PHP 8.2+
* Laravel 12
* Nwidart/laravel-modules
* Filament v3/v4
* Livewire v3
* MySQL or Postgres

### 1.2 Modules

Use Nwidart modules under `Modules/`:

```text
Modules/
  Core/
  Hostels/
  Farms/
  Construction/
  ManufacturingWater/
  ManufacturingPaper/
  HR/
  ProcurementInventory/
  Fleet/
  Finance/
```

Each module:

* Has `module.json` with `"enabled": true/false`.
* Has its own `Config`, `Routes`, `Models`, `Http/Livewire`, `Filament`, `Database` etc.

Typical module skeleton:

```text
Modules/Hostels/
  Config/config.php
  module.json
  Routes/web.php
  Http/Livewire/...
  Models/...
  Filament/Resources/...
  Database/migrations/...
  Providers/HostelsServiceProvider.php
```

**Rule:** All module-specific routes and Filament resources must be registered only if the module is enabled (via the module’s service provider).

---

## 2. CORE MODULE (SOFT MULTI-TENANCY & SHARED BASE)

### 2.1 Module: Core

**Path:** `Modules/Core`

Purpose: Provide shared entities and infrastructure:

* Company & multi-company access
* Users & Roles (Spatie + Shield)
* `SetCompany` middleware
* Shared traits/helpers
* Global enums

#### Entities (Core)

1. `Company`

* `id`
* `name` (string) – e.g. “Kharis Hostels”
* `slug` (string, unique) – `hostels`, `farms`, `construction`, `water`, `paper`
* `type` (string/enum) – `hostel`, `farm`, `construction`, `water`, `paper`
* `is_active` (boolean)
* `created_at`, `updated_at`

2. `User` (use default Laravel model under `App\Models\User`, but Core defines relations and roles)

Additional fields for multi-company:

* `current_company_id` (nullable FK → companies.id)

Pivot: `company_user`

* `company_id`
* `user_id`
* `position` (string, nullable)
* `created_at`, `updated_at`

Relations:

* `User::companies()` (many-to-many via `company_user`)
* `Company::users()`

#### Middleware: SetCompany

`Modules/Core/Http/Middleware/SetCompany.php`:

* Parameter: company slug (`hostels`, `farms`, etc.).
* Resolve `Company` by slug.
* Ensure authenticated user belongs to the company (`company_user`).
* Set `current_company_id` on user and globally (e.g. helper `currentCompany()`).
* Abort 403 if not allowed.

#### Core Filament

Core may define:

* `CompanyResource` (manage companies)
* `UserResource` (with company assignment, roles, permissions)

---

## 3. MODULE ROUTING & LIVEWIRE PATTERN

Each domain module:

* Defines its **front-end Livewire routes** in `Modules/<Module>/Routes/web.php`.
* Uses the `SetCompany` middleware with appropriate slug.
* Uses **route model binding** by slug for its main entities.
* Registers routes only when the module is enabled.

Example pattern in a module route file:

```php
Route::middleware(['web', 'auth', 'set-company:hostels'])
    ->prefix('hostels')
    ->name('hostels.')
    ->group(function () {
        Route::get('/', \Modules\Hostels\Http\Livewire\HostelList::class)->name('index');
        Route::get('{hostel:slug}', \Modules\Hostels\Http\Livewire\Dashboard::class)->name('dashboard');
    });
```

Front-end Livewire screens MUST live inside the module, e.g.:

```text
Modules/Hostels/Http/Livewire/
  HostelList.php
  Dashboard.php
  Bookings/Index.php
  Tenants/Index.php
  Rooms/Index.php
  Beds/Index.php
```

Layouts can also be modular (`resources/views/modules/hostels/layouts/hostels.blade.php`).

---

## 4. HOSTELS MODULE (COMPLETE HOSTEL MANAGEMENT)

### 4.1 Module: Hostels

**Path:** `Modules/Hostels`
**Slug / company:** `hostels`

### 4.2 Entities & fields

1. `Hostel`

* `id`
* `company_id` (FK → companies)
* `name`
* `slug`
* `location` (string)
* `city` (string, nullable)
* `capacity` (integer)
* `gender_policy` (enum: `male`, `female`, `mixed`)
* `status` (enum: `active`, `inactive`)
* `created_at`, `updated_at`

2. `Room`

* `id`
* `hostel_id`
* `room_number` (string)
* `floor` (string/integer)
* `type` (enum: `single`, `double`, `triple`, `quad`, etc.)
* `base_rate` (decimal)
* `status` (enum: `available`, `occupied`, `maintenance`, `closed`)
* `created_at`, `updated_at`

3. `Bed`

* `id`
* `room_id`
* `bed_number` (string)
* `status` (enum: `available`, `occupied`, `maintenance`, `blocked`)
* `created_at`, `updated_at`

4. `Tenant`

* `id`
* `hostel_id`
* `full_name`
* `student_id` (nullable)
* `phone`
* `email`
* `gender`
* `dob` (nullable)
* `guardian_name` (nullable)
* `guardian_phone` (nullable)
* `address` (nullable)
* `created_at`, `updated_at`

5. `Booking`

* `id`
* `hostel_id`
* `room_id` (nullable if booking is hostel-level)
* `bed_id` (nullable if booking is room-only)
* `tenant_id`
* `booking_type` (enum: `academic`, `short_stay`)
* `start_date`
* `end_date`
* `status` (enum: `pending`, `confirmed`, `checked_in`, `checked_out`, `cancelled`)
* `total_amount` (decimal)
* `currency` (string)
* `notes` (text, nullable)
* `created_at`, `updated_at`

6. `HostelCharge`

* `id`
* `hostel_id`
* `name` (e.g. Rent, Utility, Cleaning Fee, Penalty)
* `charge_type` (enum: `recurring`, `one_time`)
* `amount` (decimal)
* `is_active` (boolean)
* `created_at`, `updated_at`

Check-in/check-out can be either separate tables or logs as part of `Booking` history.

### 4.3 Workflows (Livewire front end)

1. **Hostel list** (`/hostels`)
   Component: `HostelList`

    * Fetch hostels for `current_company_id` user is allowed to see.
    * Cards with occupancy, revenue summary.
    * CTA: “Open hostel”, “Add hostel” (if permitted).

2. **Hostel dashboard** (`/hostels/{hostel:slug}`)
   Component: `Dashboard`

    * Shows stats:

        * Occupancy %
        * Active bookings
        * Overdue balances
    * Links to rooms, beds, tenants, bookings, invoices.

3. **Rooms & beds** (`/hostels/{hostel}/rooms`, `/hostels/{hostel}/beds`)
   Components: `Rooms\Index`, `Beds\Index`

    * Manage rooms & beds (create/update/delete).
    * View bed status grid.

4. **Bookings** (`/hostels/{hostel}/bookings`)
   Component: `Bookings\Index`

    * List bookings with filters (status, date range, tenant).
    * Workflow:

        * Create booking: choose tenant → choose room/bed → select period → calculate rent → create booking.
        * Confirm booking: update status to `confirmed`, optionally auto-generate invoice (Finance).
        * Check-in: update to `checked_in`, mark room/bed occupied.
        * Check-out: update to `checked_out`, release bed/room, generate final invoice if needed.
        * Cancel: update to `cancelled`, release bed/room.

5. **Tenants** (`/hostels/{hostel}/tenants`)
   Component: `Tenants\Index`

    * CRUD tenants.
    * View booking history and financial status.

6. **Billing** (via Finance module, but triggered from Hostels):

    * From booking or dashboard, call Finance service to create `Invoice` with `hostel_id`, `tenant_id`, line items (rent + fees).

---

## 5. FARMS MODULE (FARMBRITE-LIKE)

### 5.1 Module: Farms

**Path:** `Modules/Farms`
**Slug / company:** `farms`

### 5.2 Entities & fields

1. `Farm`

* `id`
* `company_id`
* `name`
* `slug`
* `location`
* `size_in_acres` (decimal)
* `farm_type` (enum: `crop`, `livestock`, `mixed`)
* `status` (enum: `active`, `inactive`)
* timestamps

2. `Field` (crop area)

* `id`
* `farm_id`
* `name`
* `area_in_acres` (decimal)
* `crop_type` (string, nullable)
* `notes` (text, nullable)
* timestamps

3. `CropCycle`

* `id`
* `field_id`
* `crop_name` (string)
* `season` (string, nullable)
* `planting_date`
* `expected_harvest_date` (nullable)
* `actual_harvest_date` (nullable)
* `status` (enum: `planned`, `planted`, `growing`, `harvested`, `abandoned`)
* `expected_yield` (decimal, nullable, e.g. kg)
* `actual_yield` (decimal, nullable)
* timestamps

4. `LivestockGroup`

* `id`
* `farm_id`
* `name` (e.g. “Broilers Batch 1”)
* `species` (string)
* `start_count` (integer)
* `current_count` (integer)
* `start_date`
* `status` (enum: `active`, `sold`, `culled`, `closed`)
* timestamps

5. `FarmTask`

* `id`
* `farm_id`
* `field_id` (nullable)
* `livestock_group_id` (nullable)
* `title`
* `description` (nullable)
* `assigned_to` (user_id, nullable)
* `due_date` (nullable)
* `status` (enum: `pending`, `in_progress`, `done`, `cancelled`)
* timestamps

6. `InputStockItem` (seed, fertilizer, feed, chemicals)

* `id`
* `company_id`
* `farm_id` (nullable if centralised)
* `name`
* `unit` (string, e.g. kg, litre)
* `sku` (nullable)
* `category` (string, e.g. `seed`, `fertilizer`, `feed`)
* `current_quantity` (decimal)
* timestamps

7. `InputUsage`

* `id`
* `farm_id`
* `field_id` (nullable)
* `livestock_group_id` (nullable)
* `input_stock_item_id`
* `date`
* `quantity_used` (decimal)
* `cost_per_unit` (decimal)
* `total_cost` (decimal)
* timestamps

8. `Harvest`

* `id`
* `crop_cycle_id`
* `date`
* `quantity` (decimal)
* `unit` (string)
* `notes` (nullable)
* timestamps

9. `FarmSale`

* `id`
* `farm_id`
* `customer_name`
* `date`
* `product_type` (string)
* `quantity` (decimal)
* `unit` (string)
* `unit_price` (decimal)
* `total_amount` (decimal)
* `invoice_id` (nullable, from Finance)
* timestamps

### 5.3 Workflows (Livewire front end)

1. **Farm list** (`/farms`) – `FarmList`

    * List farms for current company.
    * Actions: view dashboard, add farm.

2. **Farm dashboard** (`/farms/{farm:slug}`) – `Dashboard`

    * KPIs: total fields, total livestock groups, upcoming tasks, recent harvests, revenue.
    * Quick links: fields, livestock, tasks, inputs, harvests, sales.

3. **Field & Crop**

    * `/farms/{farm}/fields` – `Fields\Index` (CRUD fields).
    * `/farms/{farm}/fields/{field}/crop-cycles` – manage `CropCycle`: create, update status, record expected & actual yield.
    * Record `Harvest` entries and link to `FarmSale` or `Invoice` later.

4. **Livestock**

    * `/farms/{farm}/livestock` – `Livestock\Index`
    * Manage groups, record births/deaths, adjust `current_count`.

5. **Tasks**

    * `/farms/{farm}/tasks` – `Tasks\Index`
    * Task board/list for farm activities (plant, spray, weed, vaccination, etc.).

6. **Inputs**

    * `/farms/{farm}/inputs` – manage `InputStockItem`.
    * `/farms/{farm}/inputs/usage` – record `InputUsage` for specific fields/groups.

7. **Harvests & Sales**

    * `/farms/{farm}/harvests` – list `Harvest` records.
    * `/farms/{farm}/sales` – manage `FarmSale` and trigger Finance invoices.

---

## 6. CONSTRUCTION MODULE (FULL CONSTRUCTION MANAGEMENT)

### 6.1 Module: Construction

**Path:** `Modules/Construction`
**Slug / company:** `construction`

### 6.2 Entities & fields

1. `ConstructionProject`

* `id`
* `company_id`
* `name`
* `slug`
* `client_name`
* `client_contact` (nullable)
* `location`
* `start_date`
* `end_date` (nullable)
* `status` (enum: `planned`, `ongoing`, `on_hold`, `completed`, `cancelled`)
* `contract_amount` (decimal)
* `project_manager_id` (user_id)
* timestamps

2. `BoqItem`

* `id`
* `project_id`
* `item_code` (nullable)
* `description`
* `unit`
* `quantity`
* `unit_rate`
* `total_amount`
* timestamps

3. `ProjectTask`

* `id`
* `project_id`
* `name`
* `description` (nullable)
* `assigned_to` (user_id, nullable)
* `start_date` (nullable)
* `end_date` (nullable)
* `status` (enum: `pending`, `in_progress`, `done`, `blocked`)
* timestamps

4. `DailyWorkLog` / `Timesheet`

* `id`
* `project_id`
* `date`
* `worker_name` or `employee_id` (nullable)
* `hours_worked` (decimal)
* `work_description` (text)
* timestamps

5. `MaterialRequest`

* `id`
* `project_id`
* `requested_by` (user_id)
* `date`
* `status` (enum: `draft`, `submitted`, `approved`, `rejected`, `fulfilled`)
* timestamps

6. `MaterialRequestItem`

* `id`
* `material_request_id`
* `item_id` (from ProcurementInventory `Item`)
* `quantity_requested`
* `quantity_approved` (nullable)
* timestamps

7. `ProjectIssue` (variations, RFIs, problems)

* `id`
* `project_id`
* `title`
* `description`
* `raised_by` (user_id)
* `status` (enum: `open`, `in_review`, `resolved`, `closed`)
* timestamps

### 6.3 Workflows (Livewire front end)

1. **Projects list** (`/construction`) – `ProjectList`

    * List projects with status filters.
    * Actions: create project, open dashboard.

2. **Project dashboard** (`/construction/projects/{project:slug}`) – `Projects\Dashboard`

    * KPIs: contract vs actual cost, progress, open issues, tasks.
    * Quick links: BOQ, tasks, timesheets, materials, issues, invoicing.

3. **BOQ management**

    * `/construction/projects/{project}/boq` – `Projects\Boq`
    * CRUD `BoqItem`.
    * Show totals vs contract amount.

4. **Tasks & schedule**

    * `/construction/projects/{project}/tasks` – `Projects\Tasks`
    * Manage WBS tasks, assign staff, track status.

5. **Daily work logs / timesheets**

    * `/construction/projects/{project}/timesheets` – `Projects\Timesheets`
    * Record daily work, hours, labour cost (integration with HR/Finance later).

6. **Materials & procurement**

    * `/construction/projects/{project}/materials` – `Projects\Materials`
    * Create `MaterialRequest` linked to ProcurementInventory module.
    * Track status of requests and deliveries.

7. **Issues & variations**

    * `/construction/projects/{project}/issues` – `Projects\Issues`
    * Manage project issues and variations.

---

## 7. MANUFACTURING MODULES

### 7.1 Module: ManufacturingWater

**Path:** `Modules/ManufacturingWater`
**Slug / company:** `water`

Entities:

1. `WaterPlant`

* `id`
* `company_id`
* `name`
* `slug`
* `location`
* `capacity_per_day` (nullable)
* `status` (enum: `active`, `inactive`)
* timestamps

2. `WaterProduct`

* `id`
* `water_plant_id`
* `name` (e.g. “500ml Bottle”)
* `sku`
* `unit_volume_litres` (decimal)
* `status`
* timestamps

3. `WaterBatch`

* `id`
* `water_plant_id`
* `water_product_id`
* `batch_number`
* `production_date`
* `quantity_produced` (decimal, e.g. total litres or bottles)
* `status` (enum: `pending_qa`, `approved`, `rejected`)
* timestamps

4. `QualityTest`

* `id`
* `water_batch_id`
* `parameter` (string)
* `value` (string)
* `result` (enum: `pass`, `fail`)
* `tested_at`
* timestamps

Workflows (Livewire):

* `/manufacturing/water` → list plants (`PlantList`).
* `/manufacturing/water/{plant}/dashboard` → production stats.
* `/manufacturing/water/{plant}/batches` → manage batches.
* `/manufacturing/water/{plant}/batches/{batch}/tests` → QA records.
* Inventory usage via ProcurementInventory merges in.

---

### 7.2 Module: ManufacturingPaper

**Path:** `Modules/ManufacturingPaper`
**Slug / company:** `paper`

Entities:

1. `PaperPlant`

* `id`
* `company_id`
* `name`
* `slug`
* `location`
* `capacity_per_day` (nullable)
* `status`
* timestamps

2. `PaperProduct`

* `id`
* `paper_plant_id`
* `name` (e.g. “A4 80gsm”)
* `sku`
* `grade` (string)
* `status`
* timestamps

3. `PaperBatch`

* `id`
* `paper_plant_id`
* `paper_product_id`
* `batch_number`
* `production_date`
* `weight_produced` (decimal, tonnes or kg)
* `status`
* timestamps

4. `ProductionLine`

* `id`
* `paper_plant_id`
* `name`
* `status`
* timestamps

5. `ProductionLineLog`

* `id`
* `production_line_id`
* `date`
* `shift` (string)
* `uptime_hours` (decimal)
* `downtime_hours` (decimal)
* `notes` (nullable)
* timestamps

Workflows:

* `/manufacturing/paper` → `PlantList`.
* `/manufacturing/paper/{plant}/dashboard` → KPIs.
* `/manufacturing/paper/{plant}/batches` → manage batches.
* `/manufacturing/paper/{plant}/lines` → track line performance.

---

## 8. HR MODULE (COMPLETE HR & PAYROLL)

### 8.1 Module: HR

**Path:** `Modules/HR`

Entities:

1. `Department`

* `id`
* `company_id`
* `name`
* `code`
* timestamps

2. `JobPosition`

* `id`
* `company_id`
* `name`
* `department_id`
* `description` (nullable)
* timestamps

3. `Employee`

* `id`
* `company_id`
* `user_id` (nullable)
* `first_name`
* `last_name`
* `employee_code`
* `email`
* `phone`
* `hire_date`
* `employment_type` (enum: `full_time`, `part_time`, `contract`)
* `status` (enum: `active`, `inactive`, `terminated`)
* `department_id`
* `job_position_id`
* timestamps

4. `AttendanceRecord`

* `id`
* `employee_id`
* `date`
* `status` (enum: `present`, `absent`, `leave`, `off`)
* `check_in_time` (nullable)
* `check_out_time` (nullable)
* timestamps

5. `LeaveType`

* `id`
* `company_id`
* `name`
* `max_days_per_year`
* timestamps

6. `LeaveRequest`

* `id`
* `employee_id`
* `leave_type_id`
* `start_date`
* `end_date`
* `status` (enum: `pending`, `approved`, `rejected`, `cancelled`)
* `reason` (nullable)
* timestamps

Workflows (Livewire):

* `/hr/employees` – manage employees (employees/HR managers).
* `/hr/attendance` – mark/track attendance.
* `/hr/leaves` – apply for leave, approve, reject.

---

## 9. PROCUREMENT & INVENTORY MODULE (FULL CYCLE)

### 9.1 Module: ProcurementInventory

**Path:** `Modules/ProcurementInventory`

Entities:

1. `Supplier`

* `id`
* `company_id`
* `name`
* `contact_person` (nullable)
* `phone`
* `email` (nullable)
* `address` (nullable)
* timestamps

2. `ItemCategory`

* `id`
* `company_id`
* `name`
* timestamps

3. `Item`

* `id`
* `company_id`
* `item_category_id`
* `name`
* `sku` (nullable)
* `unit`
* `is_stock_tracked` (boolean)
* timestamps

4. `StockLocation`

* `id`
* `company_id`
* `name`
* `location_type` (string, e.g. `hostel_store`, `farm_store`, `plant_store`, `central`)
* timestamps

5. `PurchaseRequest`

* `id`
* `company_id`
* `requested_by` (user_id)
* `department_id` (nullable, from HR)
* `date`
* `status` (enum: `draft`, `submitted`, `approved`, `rejected`, `cancelled`)
* `notes` (nullable)
* timestamps

6. `PurchaseRequestItem`

* `id`
* `purchase_request_id`
* `item_id`
* `quantity`
* `justification` (nullable)
* timestamps

7. `PurchaseOrder`

* `id`
* `company_id`
* `supplier_id`
* `po_number`
* `date`
* `status` (enum: `draft`, `sent`, `confirmed`, `completed`, `cancelled`)
* `total_amount` (decimal)
* timestamps

8. `PurchaseOrderItem`

* `id`
* `purchase_order_id`
* `item_id`
* `quantity`
* `unit_price`
* `total` (computed)
* timestamps

9. `GoodsReceipt`

* `id`
* `company_id`
* `purchase_order_id`
* `received_by` (user_id)
* `date`
* `stock_location_id`
* timestamps

10. `StockMovement`

* `id`
* `company_id`
* `item_id`
* `stock_location_id`
* `quantity` (positive/negative)
* `movement_type` (enum: `in`, `out`, `transfer`, `adjustment`)
* `reference_type` (string, e.g. `po`, `issue`, `transfer`)
* `reference_id` (nullable)
* `notes` (nullable)
* timestamps

Workflows (Livewire):

* `/procurement/requests` – create/submit/approve PRs.
* `/procurement/orders` – create POs from approved PRs.
* `/inventory/receipts` – record GRNs, auto stock-in.
* `/inventory/stock` – view stock per location, low stock alerts.

---

## 10. FLEET MODULE (FULL FLEET MANAGEMENT)

### 10.1 Module: Fleet

**Path:** `Modules/Fleet`

Entities:

1. `Vehicle`

* `id`
* `company_id`
* `registration_number`
* `type` (string, e.g. `truck`, `pickup`, `car`)
* `make_model`
* `year` (nullable)
* `capacity` (string, nullable)
* `status` (enum: `available`, `in_use`, `maintenance`, `inactive`)
* timestamps

2. `Driver`

* `id`
* `company_id`
* `employee_id` (nullable, from HR)
* `name`
* `phone`
* `license_number`
* timestamps

3. `Trip`

* `id`
* `vehicle_id`
* `driver_id`
* `company_id`
* `date`
* `origin`
* `destination`
* `distance_km` (decimal)
* `purpose` (text)
* `status` (enum: `planned`, `in_progress`, `completed`, `cancelled`)
* timestamps

4. `FuelLog`

* `id`
* `vehicle_id`
* `company_id`
* `date`
* `litres` (decimal)
* `cost` (decimal)
* `odometer` (decimal)
* timestamps

5. `MaintenanceRecord`

* `id`
* `vehicle_id`
* `company_id`
* `date`
* `type` (string)
* `description` (text, nullable)
* `cost` (decimal)
* timestamps

Workflows:

* `/fleet/vehicles` – manage vehicles & status.
* `/fleet/trips` – assign trips, track completion.
* `/fleet/fuel` – log fuel and compute efficiency.
* `/fleet/maintenance` – record maintenance.

---

## 11. FINANCE MODULE (ACCOUNTING BACKBONE)

### 11.1 Module: Finance

**Path:** `Modules/Finance`

Entities:

1. `Account`

* `id`
* `company_id`
* `code`
* `name`
* `type` (enum: `asset`, `liability`, `equity`, `income`, `expense`)
* `parent_id` (nullable)
* timestamps

2. `JournalEntry`

* `id`
* `company_id`
* `date`
* `reference` (string)
* `description` (nullable)
* timestamps

3. `JournalLine`

* `id`
* `journal_entry_id`
* `account_id`
* `debit` (decimal)
* `credit` (decimal)
* timestamps

4. `Invoice`

* `id`
* `company_id`
* `customer_name` (nullable, or link to Tenant/Farm customer/etc.)
* `customer_type` (string: `tenant`, `external`, etc.)
* `customer_id` (nullable)
* `invoice_number`
* `invoice_date`
* `due_date` (nullable)
* `status` (enum: `draft`, `sent`, `paid`, `overdue`, `cancelled`)
* `sub_total` (decimal)
* `tax_total` (decimal)
* `total` (decimal)
* `hostel_id` (nullable)
* `farm_id` (nullable)
* `construction_project_id` (nullable)
* `plant_id` (nullable)
* timestamps

5. `InvoiceLine`

* `id`
* `invoice_id`
* `description`
* `quantity`
* `unit_price`
* `line_total`
* timestamps

6. `Payment`

* `id`
* `company_id`
* `invoice_id` (nullable)
* `amount` (decimal)
* `payment_date`
* `payment_method` (string: `cash`, `bank`, `momo`, etc.)
* `reference` (nullable)
* timestamps

Workflows:

* `/finance/invoices` – list and manage invoices (including those coming from Hostels, Farms, Construction, Manufacturing).
* `/finance/payments` – record payments and update invoice status.
* Journal posting services called from modules when needed.

---

## 12. MODULE TOGGLING & INTEGRATION

* Each module has `module.json` with `"enabled": true/false`.
* Each module’s service provider checks `module()->isEnabled()` before registering routes, Livewire components, Filament resources.
* Menus in Filament and public navigation should **only show modules that are enabled**.

Example: If `ManufacturingPaper` is disabled, there should be:

* No `/manufacturing/paper` routes.
* No Paper plant menus.
* No Paper-specific models loaded.

---

Use this document as the **single project rule** for Trae / Junie.
Whenever code is generated, it must respect:

* Nwidart modules per domain (toggleable).
* Livewire front-ends inside each module.
* Filament admin resources inside each module.
* Soft multi-tenancy with `company_id`.
* All entities, fields, and workflows described above.
