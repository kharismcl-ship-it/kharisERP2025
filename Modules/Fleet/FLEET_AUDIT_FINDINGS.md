# Fleet Module — Audit Findings & Implementation Plan

**Date:** 2026-03-01
**Current Completeness:** ~70% (functional core, critical bugs, gaps in integration and automation)
**Filament:** v4 Schema API

---

## 1. What Exists

| Component | Files | Completeness |
|-----------|-------|-------------|
| Models | Vehicle, VehicleDocument, MaintenanceRecord, FuelLog, DriverAssignment, TripLog | 95% |
| Migrations | 7 migrations (vehicles, documents, maintenance, fuel, drivers, trips) | 100% |
| Filament Resources | VehicleResource (full), MaintenanceRecordResource, FuelLogResource, TripLogResource, DriverAssignmentResource | 75–95% |
| Services | FleetService (fuel, maintenance, analytics) | 60% |
| Policies | 5 policies (all models covered) | 90% |
| Events | MaintenancePartsRequested (defined, never dispatched) | 10% |
| Finance Integration | GL posting for fuel + maintenance costs | 50% (bugs) |

---

## 2. Critical Bugs (Must Fix First)

### BUG-001: FuelLog column name mismatch
- **Where:** `FleetService::recordFuelExpenseInFinance()` and `EnhancedIntegrationService::recordFleetFuelExpense()`
- **Problem:** Finance service accesses `$fuelLog->date` — the column is `fill_date`
- **Impact:** Every fuel log saves to Finance silently fails (caught and logged, no crash, but expense never posted)

### BUG-002: MaintenanceRecord column name mismatch
- **Where:** `FleetService::recordMaintenanceExpenseInFinance()`
- **Problem:** Finance service accesses `$maintenanceRecord->date` — the column is `service_date`
- **Impact:** Every maintenance record Finance post silently fails

### BUG-003: MaintenancePartsRequested event never dispatched
- **Where:** `app/Events/MaintenancePartsRequested.php` exists; `ProcurementInventory\Listeners\CreateMaintenancePartsPO` exists
- **Problem:** The event is never fired anywhere in the Fleet code
- **Impact:** Zero procurement integration from Fleet

---

## 3. Missing Features by Resource

### VehicleResource
- Missing: KPI cards on ViewVehicle (YTD fuel cost, YTD maintenance cost, total distance driven, next service due)
- Missing: Bulk status change (active → retired)
- Missing: Vehicle health score indicator

### MaintenanceRecordResource
- Missing: **ViewRecord page** (only List/Create/Edit exist)
- Missing: Status workflow actions (Scheduled → In Progress → Completed)
- Missing: "Request Parts" action to trigger ProcurementInventory PO creation
- Missing: Parts line-item form (per-part description, qty, unit price)
- Missing: Finance expense receipt attachment

### FuelLogResource
- Missing: **ViewRecord page**
- Missing: Fuel economy display (L/100km vs previous fill)

### TripLogResource
- Missing: **ViewRecord page**
- Missing: Status workflow actions (Planned → In Progress → Completed → Cancelled)
- Missing: Trip cost per km calculation

### DriverAssignmentResource
- Missing: **ViewRecord page**
- Missing: `employee_id` column exists in DB but unused — should link HR\Models\Employee
- Missing: Active assignment validation (cannot assign the same driver to 2 vehicles simultaneously)
- Missing: Driver license/qualification checks

---

## 4. Cross-Module Integration Gaps

### Finance (50% — bugs present)
| Feature | Status |
|---------|--------|
| Fuel cost → GL journal entry (debit FleetFuel, credit Cash/AP) | Broken (BUG-001) |
| Maintenance cost → GL journal entry (debit Maintenance, credit Cash/AP) | Broken (BUG-002) |
| Fleet cost centre tagging | Missing |
| Vehicle as fixed asset (linked to FixedAsset Finance model) | Missing |

### ProcurementInventory (5% — event wired but never fired)
| Feature | Status |
|---------|--------|
| MaintenancePartsRequested event wired to CreateMaintenancePartsPO | Defined, never dispatched |
| "Request Parts" UI action on MaintenanceRecord | Missing |
| Parts line-item form on maintenance records | Missing |
| PO reference linkback on MaintenanceRecord | Missing |

### HR (0%)
| Feature | Status |
|---------|--------|
| DriverAssignment.employee_id link to HR\Models\Employee | Column exists, unused |
| Driver leave validation (cannot assign driver who is on approved leave) | Missing |
| Driver license expiry cross-check against employee documents | Missing |
| Employee→Driver sync (create DriverAssignment on HR hire) | Missing |

### CommunicationCentre (0%)
| Feature | Status |
|---------|--------|
| Fleet notification templates | None |
| Notifications | None |

### Core Automation (0%)
| Feature | Status |
|---------|--------|
| Scheduled commands | None registered |
| Document expiry alerts | None |
| Service due reminders | None |

---

## 5. Database Changes Needed

### New Migration: add_procurement_ref_to_maintenance_records
- `purchase_order_id` NULLABLE UNSIGNED BIGINT on `maintenance_records`
- Index on `purchase_order_id`

### New Migration: add_employee_id_to_driver_assignments
- `employee_id` already exists in `driver_assignments` but no FK — add proper FK to `employees`

No new tables needed for MVP phases.

---

## 6. Proposed Implementation Plan (6 Phases)

### Phase 1 — Bug Fixes & View Pages
**Scope:** Fix critical bugs; add 4 missing ViewRecord pages
1. Fix `EnhancedIntegrationService` (or add model accessors) to use correct column names
2. Create `ViewMaintenanceRecord` — infolist with sections: Details, Service Info, Finance, Audit
3. Create `ViewFuelLog` — infolist with sections: Fuel Details, Cost, Audit
4. Create `ViewTripLog` — infolist with sections: Trip Details, Mileage, Audit
5. Create `ViewDriverAssignment` — infolist with sections: Assignment Details, Audit
6. Add `ViewAction` to all 4 resource tables; register `'view'` routes
7. Update `VehicleResource` nav group to `Fleet Management`; move all fleet resources under it

### Phase 2 — Dashboard & Vehicle KPIs
**Scope:** Fleet Dashboard page + enhanced ViewVehicle with KPIs
1. Create `FleetDashboard` Filament page (nav group: Fleet Management, sort: 1)
   - KPI cards: total vehicles, active/under maintenance/retired, open maintenance records, fleet fuel cost MTD, fleet maintenance cost MTD, documents expiring this month
   - Top 5 vehicles by fuel cost YTD
   - Upcoming maintenance (next 30 days)
2. Enhance `ViewVehicle` infolist: add Vehicle KPIs section
   - YTD fuel cost, YTD maintenance cost, total distance YTD, next service due, last fuel fill

### Phase 3 — Maintenance Workflow + Parts Request
**Scope:** Status machine on MaintenanceRecord + procurement integration
1. Add status transition actions to `ViewMaintenanceRecord`:
   - "Start Service" (Scheduled → In Progress)
   - "Mark Completed" (In Progress → Completed) — fires Finance GL post
2. Add "Request Parts" action on `ViewMaintenanceRecord`:
   - Opens modal with repeater of parts (description, qty, unit_price)
   - Dispatches `MaintenancePartsRequested` event → PO auto-created by Procurement listener
   - Stores `purchase_order_id` back on maintenance record
3. Add migration: `purchase_order_id` on `maintenance_records`
4. Update `PurchaseOrder.hostel_id → vehicle_id` column or use `module_tag='fleet'`

### Phase 4 — Automation & Notifications
**Scope:** Scheduled reminders + CommunicationCentre templates
1. Create `FleetDocumentExpiryAlertCommand` (`fleet:document-expiry-alert`) — daily at 08:00
   - Find documents expiring within 30 days or already expired
   - Send email/SMS via CommunicationCentre template `fleet_document_expiry`
2. Create `FleetServiceDueAlertCommand` (`fleet:service-due-alert`) — daily at 08:30
   - Find vehicles with `next_service_date <= today + 14 days`
   - Send email/SMS via `fleet_service_due` template
3. Create `ProcurementCommTemplateSeeder` equivalent for Fleet:
   - `fleet_document_expiry` — email + SMS templates
   - `fleet_service_due` — email + SMS templates
   - `fleet_maintenance_completed` — email template
   - `fleet_vehicle_assigned` — email + SMS templates
4. Register commands + schedules in `FleetServiceProvider`
5. Add trip status workflow actions to `ViewTripLog`:
   - "Start Trip" (Planned → In Progress), "Complete Trip" (In Progress → Completed), "Cancel" (Planned/In Progress → Cancelled)

### Phase 5 — HR Integration
**Scope:** Link drivers to HR employees
1. Add FK migration: `driver_assignments.employee_id → employees.id`
2. Update `DriverAssignment` model: add `employee()` BelongsTo relationship
3. Update `DriverAssignmentResource` form: add Employee select alongside User select
4. Add validation: cannot assign a driver who has active approved leave (query LeaveRequest)
5. Update `ViewDriverAssignment` infolist: show employee name, department, license details
6. Create `VehicleDocumentResource` as standalone resource (in addition to RelationManager) for company-wide document expiry management
7. Add `fleet:check-driver-leave` awareness in `FleetService::recordDriverAssignment()`

### Phase 6 — Analytics & Cost Reporting
**Scope:** Fleet analytics and fuel/cost reports
1. Add fuel efficiency calculation to `FleetService` and `FuelLogResource` table
   - L/100km calculated from consecutive fill-up mileage gaps
2. Add trip cost column to `TripLog` (fuel cost / distance × trip distance)
3. Add `FleetCostReport` Filament page (net cost per vehicle: fuel + maintenance)
4. Add `FuelEfficiencyReport` Filament page (consumption trends per vehicle)
5. Add vehicle health score to `ViewVehicle` (% of scheduled maintenance actually completed)
6. Add odometer validation: prevent mileage lower than vehicle's `current_mileage`
7. Add vehicle depreciation calculation (link to Finance FixedAsset if available)

---

## 7. File Summary of Changes

| Phase | New Files | Modified Files |
|-------|-----------|----------------|
| 1 | 4 ViewRecord pages (4) | VehicleResource, 4 Resources (getPages + actions), EnhancedIntegrationService |
| 2 | FleetDashboard.php, fleet-dashboard.blade.php | VehicleResource/Pages/ViewVehicle.php |
| 3 | 1 migration, "Request Parts" action logic | ViewMaintenanceRecord.php, FleetService.php |
| 4 | 2 commands, FleetCommTemplateSeeder.php | FleetServiceProvider.php, CommunicationCentreDatabaseSeeder.php |
| 5 | 1 migration, VehicleDocumentResource.php | DriverAssignment.php, DriverAssignmentResource.php, FleetFilamentPlugin.php |
| 6 | 2 report pages, 2 blade views | FleetService.php, FuelLogResource.php |

**Total estimate:** ~65 new/modified files, 6 phases
