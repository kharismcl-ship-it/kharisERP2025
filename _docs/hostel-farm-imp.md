Implementation Plan: Hostels Frontend Gaps + Farms Operations Portal

_Ref: tools/hostel-farms-frontend-analysis.md

Context

The Hostels module has a fully built backend (34 models, 60 migrations, 28 Filament resources) and a largely complete Livewire frontend, but 9 operational gaps remain — most critically: no admin
check-in/check-out flow, no occupant booking cancellation, no deposit visibility, and no fee breakdown. The Farms module has a production-ready backend (30 models, FarmService with 25+ methods, 22
Filament resources) but almost no frontend — only a stub FarmIndex listing page. This plan closes all Hostels gaps and builds the complete Farm Operations Portal.

 ---
Key Patterns (from codebase exploration)

- Livewire namespace: Modules\X\Http\Livewire\..., view x::livewire.dot.path, layout x::layouts.app
- Auth: auth() for admin, auth('hostel_occupant') for occupant portal
- Routes: ['web', 'auth', 'set-company:hostels'] for admin; ['web', 'auth:hostel_occupant'] for occupant
- Farms routes: ['web', 'auth', 'set-company:farms'] already working — just add more routes
- Stats: White cards + SVG icons + Tailwind grid — NO external chart library
- Tabs: Pure Livewire $set('activeTab', 'x') + blade @if — no JS needed
- Notifications: Always dispatch browser event + session flash
- Component registration: Manual in ServiceProvider Livewire::component('alias', Class::class)
- Service injection: FarmService is a singleton; inject via constructor or app(FarmService::class)

 ---
Phase 1 — Hostels: Critical Frontend Gaps

1A. Admin Check-In Page

File: Modules/Hostels/app/Http/Livewire/Admin/CheckIn.php
View: Modules/Hostels/resources/views/livewire/admin/check-in.blade.php
Route: GET /hostels/admin/{hostel:slug}/check-in → hostels.check-in

Logic:
- Search bookings by reference/name (only status confirmed or awaiting_payment)
- Show booking detail card on match
- Signature capture field (base64 textarea or JS-based pad → store in guest_check_in_signature)
- Submit → calls $booking->checkIn() (existing method on Booking model)
- Dispatches browser event, flashes success, bed becomes occupied

Register in HostelsServiceProvider boot().

 ---
1B. Admin Check-Out Page

File: Modules/Hostels/app/Http/Livewire/Admin/CheckOut.php
View: Modules/Hostels/resources/views/livewire/admin/check-out.blade.php
Route: GET /hostels/admin/{hostel:slug}/check-out → hostels.check-out

Logic:
- Search bookings by reference (status = checked_in)
- Show summary: total charged, amount paid, balance, deposit held
- Call $booking->getApplicableCancellationPolicy() → show deductions if any
- Confirm → update status to checked_out, set actual_check_out_at = now(), release bed (available)
- If deposit refund due → call $deposit->processRefund(amount) (existing Deposit method)

 ---
1C. Occupant Booking Cancellation

File: Modules/Hostels/app/Http/Livewire/HostelOccupant/Bookings/Cancel.php
View: Modules/Hostels/resources/views/livewire/hostel-occupant/bookings/cancel.blade.php
Route: GET /hostel-occupant/bookings/{booking}/cancel middleware auth:hostel_occupant

Logic:
- Auth check: booking must belong to current occupant (abort 403 otherwise)
- Call $booking->getApplicableCancellationPolicy() → calculateRefundAmount() → show refund estimate
- isCancellationAllowed($checkInDate) check → show warning if no refund window
- Confirm button → $booking->cancelBooking($policy) (existing method returns ['status', 'refund_amount'])
- Redirect to bookings index with flash

Add link from HostelOccupant/Bookings/Show view.

 ---
1D. Deposit Visibility + Admin Collection

Occupant portal — add Deposit card to HostelOccupant/Bookings/Show.php:
- Load $booking->deposit relation in mount()
- Blade section: status badge, amount, collected_at, refund amount if refunded
- No new component needed — extend existing Show

Admin collection page:
File: Modules/Hostels/app/Http/Livewire/Admin/DepositCollection.php
View: Modules/Hostels/resources/views/livewire/admin/deposit-collection.blade.php
Route: GET /hostels/admin/{hostel:slug}/deposit-collection → hostels.deposit-collection

Logic:
- List deposits with status pending, filter by hostel
- "Mark Collected" action → $deposit->markAsCollected() (existing method)
- "Process Refund" action → input refund amount + deduction reason → $deposit->processRefund()
- Shows Finance GL posting confirmation

 ---
Phase 2 — Hostels: Important Frontend Gaps

2A. Fee/Charge Breakdown

BookingWizard Step 3 (Public/BookingWizard.php):
- After bed selection, load mandatory FeeTypes: FeeType::where('hostel_id', $hostelId)->where('is_mandatory', true)->where('is_active', true)->get()
- Add to price breakdown table: room rate + mandatory fees = total
- No new component — modify existing BookingWizard.php

Occupant booking view (HostelOccupant/Bookings/Show.php):
- Add "Charges" tab: load $booking->bookingCharges()->with('feeType')->get()
- Table: fee name, quantity, unit price, amount

 ---
2B. Payment History + Receipt

Occupant Bookings/Show.php — add "Payments" tab:
- Load payment history via $booking->payments() (HasPayments trait)
- Table: date, amount, gateway, reference, status
- "Print Receipt" link → new route

Receipt page:
File: Modules/Hostels/app/Http/Livewire/HostelOccupant/Bookings/Receipt.php
View: resources/views/livewire/hostel-occupant/bookings/receipt.blade.php
Route: GET /hostel-occupant/bookings/{booking}/receipt middleware auth:hostel_occupant

Renders a print-friendly page (no layout nav, just booking details + payment summary).

 ---
2C. Occupant Incident Reporting

File: Modules/Hostels/app/Http/Livewire/HostelOccupant/Incidents/Create.php
View: resources/views/livewire/hostel-occupant/incidents/create.blade.php
Route: GET /hostel-occupant/incidents/create middleware auth:hostel_occupant

Fields: hostel (from occupant's active booking), room, title, description, severity (low/medium/high/critical)
On save: create Incident with hostel_occupant_id = auth('hostel_occupant')->user()->hostel_occupant_id

Index page:
File: HostelOccupant/Incidents/Index.php
Route: GET /hostel-occupant/incidents

 ---
2D. Visitor Pre-Registration by Occupants

File: Modules/Hostels/app/Http/Livewire/HostelOccupant/Visitors/Create.php
View: resources/views/livewire/hostel-occupant/visitors/create.blade.php
Route: GET /hostel-occupant/visitors/create middleware auth:hostel_occupant

Fields: visitor name, phone, purpose, expected arrival time
Creates a VisitorLog with check_out_at = null and hostel_occupant_id set — pre-registers for admin to confirm on arrival.

 ---
2E. PricingPolicy in BookingWizard

Modify Public/BookingWizard.php step 3 rate calculation:
- After room/bed selected, query: PricingPolicy::where('hostel_id', $hostelId)->where('is_active', true)->get()
- Apply matching policy (date range, occupancy threshold) to base rate
- Show "Seasonal rate applied" notice if policy adjusted the price

 ---
2F. Richer Admin Reports

Extend Reports/Index.php with new tabs (Livewire $set('activeTab', ...) pattern):

┌─────────────────┬──────────────────────────────────────────────────────┐
│       Tab       │                     Data source                      │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Occupancy       │ Bookings by date range, group by hostel — % occupied │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Revenue         │ Sum amount_paid by month/hostel                      │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Deposits        │ Collected vs pending vs refunded from Deposit model  │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Maintenance SLA │ Avg days open, % resolved within 48h                 │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Booking types   │ Count by booking_type (academic/semester/short_stay) │
└─────────────────┴──────────────────────────────────────────────────────┘

All computed as Livewire properties with date range filter props.

 ---
Phase 3 — Farms: Infrastructure

3A. Farms Layout

File: Modules/Farms/resources/views/layouts/app.blade.php

Minimal HTML5 wrapper with:
- Vite assets (farms::... or root app.css/app.js)
- @livewire('farms::navigation') component
- @livewireStyles / @livewireScripts
- Flash message slot

3B. Farms Navigation Component

File: Modules/Farms/app/Http/Livewire/Navigation.php
View: Modules/Farms/resources/views/livewire/navigation.blade.php

Nav links (with request()->routeIs() active states):
- My Farms (farms.index)
- Dashboard (farms.dashboard)
- Tasks (farms.tasks.index)
- Daily Reports (farms.daily-reports.index)
- Requests (farms.requests.index)
- Attendance (farms.attendance.index)
- Reports (farms.reports.index)

Mobile toggle via Alpine.js @click="open = !open".

Register in FarmsServiceProvider::boot().

3C. Expand Routes

Update Modules/Farms/routes/web.php — add all portal routes under existing middleware group:
GET /farms                           → FarmIndex (existing)
GET /farms/{farm:slug}               → FarmDashboard
GET /farms/{farm:slug}/tasks         → Tasks/Index
GET /farms/{farm:slug}/tasks/create  → Tasks/Create
GET /farms/{farm:slug}/daily-reports → DailyReports/Index
GET /farms/{farm:slug}/daily-reports/create → DailyReports/Create
GET /farms/{farm:slug}/daily-reports/{report} → DailyReports/Show
GET /farms/{farm:slug}/crops         → Crops/Index
GET /farms/{farm:slug}/crops/{cropCycle} → Crops/Show
GET /farms/{farm:slug}/crops/{cropCycle}/harvest → Crops/RecordHarvest
GET /farms/{farm:slug}/livestock     → Livestock/Index
GET /farms/{farm:slug}/livestock/{batch} → Livestock/Show
GET /farms/{farm:slug}/requests      → Requests/Index
GET /farms/{farm:slug}/requests/create → Requests/Create
GET /farms/{farm:slug}/requests/{request} → Requests/Show
GET /farms/{farm:slug}/attendance    → Attendance/Index
GET /farms/{farm:slug}/map           → FarmMap
GET /farms/{farm:slug}/reports       → Reports/Index

 ---
Phase 4 — Farms: Core Portal Pages

4A. Farm Dashboard

File: Modules/Farms/app/Http/Livewire/FarmDashboard.php
View: resources/views/livewire/farm-dashboard.blade.php
Route: GET /farms/{farm:slug} → farms.dashboard

Computed properties using FarmService (injected singleton):
- activeCropCycles — $farm->cropCycles()->where('status', 'growing')->get()
- openTasks — app(FarmService::class)->openTasksByFarm($farm)
- overdueTasks — app(FarmService::class)->overdueTasksByFarm($farm)
- livestockSummary — batches grouped by animal_type with count
- netProfit — FarmService::netProfit($farm, now()->startOfMonth(), now())
- budgetSummary — FarmService::budgetVsActual($farm)
- recentReports — $farm->dailyReports()->latest()->take(5)->get()

Layout: 4-column stat cards (total crops, open tasks, overdue, livestock count), then 3-column content (crop list, livestock summary, recent reports).

 ---
4B. Task Board

File: Modules/Farms/app/Http/Livewire/Tasks/Index.php
View: resources/views/livewire/tasks/index.blade.php
Route: GET /farms/{farm:slug}/tasks

Properties: $farm, $statusFilter = '', $priorityFilter = '', $workerFilter = ''

Computed $tasks: FarmTask::where('farm_id', $farm->id)->with(['assignedWorker', 'cropCycle', 'livestockBatch']) + filters + orderBy('due_date') + paginate(20)

Methods:
- markComplete(FarmTask $task) — sets completed_at = now(), flash
- filters passed from $farm->workers()->where('is_active', true)->get()

Overdue tasks shown with red border. is_overdue computed attr on model used in blade.

Create modal (inline, no separate page):
- showCreateModal, fields: title, task_type, priority, due_date, assigned_to_worker_id, linked to crop_cycle or livestock_batch
- saveTask() → FarmTask::create()

 ---
4C. Daily Report — Create

File: Modules/Farms/app/Http/Livewire/DailyReports/Create.php
View: resources/views/livewire/daily-reports/create.blade.php
Route: GET /farms/{farm:slug}/daily-reports/create

Properties: $farm, $reportDate = today, $workerId, $summary, $activitiesDone, $issuesNoted, $recommendations, $weatherObservation, $attachments = []
Trait: WithFileUploads

submitReport():
- Validate all required fields
- Create FarmDailyReport with status = 'submitted'
- Model's boot fires FarmDailyReportSubmitted event → listener notifies manager via CommunicationCentre
- Redirect to index with flash

Index + Manager Review:
File: DailyReports/Index.php
- Lists reports with status filter, date filter
- reviewReport(FarmDailyReport $report) → modal with review notes → sets status = 'reviewed', reviewed_by = Auth::id(), reviewed_at = now()

 ---
Phase 5 — Farms: Operations Pages

5A. Crop Monitoring

File: Modules/Farms/app/Http/Livewire/Crops/Show.php
View: resources/views/livewire/crops/show.blade.php

Properties: $farm, $cropCycle, $activeTab = 'overview'
Tabs: Overview | Scouting | Inputs | Activities | Financials

Each tab loads its relation lazily via computed property.

Quick-log modals per tab:
- Scouting: showScoutingModal → create CropScoutingRecord
- Input: showInputModal → create CropInputApplication (with optional item_id from ProcurementInventory)
- Activity: showActivityModal → create CropActivity

Overview shows PnL summary via FarmService::cropCyclePnL($cropCycle).

 ---
5B. Livestock Quick Actions

File: Modules/Farms/app/Http/Livewire/Livestock/Show.php
View: resources/views/livewire/livestock/show.blade.php

Properties: $farm, $batch, $activeTab = 'summary'

Four action modals:
- logFeed() → LivestockFeedRecord::create()
- logWeight() → LivestockWeightRecord::create()
- logMortality() → LivestockMortalityLog::create() — model boot() auto-decrements batch.current_count
- logHealthEvent() → LivestockHealthRecord::create()

Stats via FarmService:
- livestockGrowthRate($batch), feedConversionRatio($batch), livestockHealthSummary($batch)

 ---
5C. Harvest Recording

File: Modules/Farms/app/Http/Livewire/Crops/RecordHarvest.php
View: resources/views/livewire/crops/record-harvest.blade.php

Properties: $farm, $cropCycle, $harvestDate, $quantity, $unit, $unitPrice, $buyerName, $storageLocation
Trait: WithFileUploads (for photo)

recordHarvest():
- Validate
- Call app(FarmService::class)->recordHarvest($cropCycle, $data) — existing service method creates HarvestRecord, marks cycle harvested, updates ProduceInventory
- Optionally call FarmService::createSaleInvoice() if buyer info provided
- Redirect to crop show with flash

Shows running total vs expected_yield using FarmService::yieldVsTarget().

 ---
Phase 6 — Farms: Management Pages

6A. Farm Requests

Files:
- Requests/Index.php + blade
- Requests/Create.php + blade
- Requests/Show.php + blade

Create flow:
- $requestType, $title, $description, $urgency
- Dynamic line items array: $items = [['description' => '', 'quantity' => 1, 'unit' => '', 'unit_cost' => 0]]
- addItem() / removeItem($index) methods
- submitRequest() → create FarmRequest (auto-ref FR-YYYYMM-00001) + loop $items → create FarmRequestItem
- Status triggers FarmRequestStatusChanged event

Index (manager view): filter by status, approve/reject with reason modal.
Show: detail with line items table + status timeline.

 ---
6B. Worker Attendance

File: Modules/Farms/app/Http/Livewire/Attendance/Index.php
View: resources/views/livewire/attendance/index.blade.php

Properties: $farm, $attendanceDate = today, $entries = [] (array keyed by worker_id)

mount(): loads active workers, initialises $entries array with defaults.
markAttendance(): loops $entries, upserts FarmWorkerAttendance (updateOrCreate by farm_id + worker_id + date).
Past attendance: date picker + read-only summary table.

 ---
6C. Farm Map

File: Modules/Farms/app/Http/Livewire/FarmMap.php
View: resources/views/livewire/farm-map.blade.php

- Uses existing FilamentLeaflet package (x-filament-leaflet::map or plain Leaflet JS)
- Loads $farm->plots with geometry and status
- Passes GeoJSON plot boundaries as JSON to Alpine.js → Leaflet L.geoJSON()
- Color-codes by status (active=green, fallow=yellow, preparing=blue)
- Click plot → panel shows crop cycle info
- Livestock batch markers at farm lat/lng

 ---
6D. Reports & Analytics

File: Modules/Farms/app/Http/Livewire/Reports/Index.php
View: resources/views/livewire/reports/index.blade.php

Properties: $farm, $activeTab = 'financials', $fromDate, $toDate

Tabs (Livewire $set pattern):

┌────────────┬───────────────────────────────────────────────────────────┐
│    Tab     │                      Service Method                       │
├────────────┼───────────────────────────────────────────────────────────┤
│ Financials │ FarmService::netProfit(), totalRevenue(), totalExpenses() │
├────────────┼───────────────────────────────────────────────────────────┤
│ Budget     │ FarmService::budgetVsActual() — table by category         │
├────────────┼───────────────────────────────────────────────────────────┤
│ Crop P&L   │ FarmService::cropCyclePnL() per cycle                     │
├────────────┼───────────────────────────────────────────────────────────┤
│ Livestock  │ FarmService::feedConversionRatio(), livestockGrowthRate() │
├────────────┼───────────────────────────────────────────────────────────┤
│ Tasks      │ Count open/overdue/completed, breakdown by type           │
└────────────┴───────────────────────────────────────────────────────────┘

All stat cards with Tailwind grid, no chart library.

 ---
Files to Create (Total: ~60 new files)

Hostels — New Livewire PHP (10)

app/Http/Livewire/Admin/CheckIn.php
app/Http/Livewire/Admin/CheckOut.php
app/Http/Livewire/Admin/DepositCollection.php
app/Http/Livewire/HostelOccupant/Bookings/Cancel.php
app/Http/Livewire/HostelOccupant/Bookings/Receipt.php
app/Http/Livewire/HostelOccupant/Incidents/Index.php
app/Http/Livewire/HostelOccupant/Incidents/Create.php
app/Http/Livewire/HostelOccupant/Visitors/Create.php
Plus modifications to: HostelOccupant/Bookings/Show.php, Public/BookingWizard.php, Reports/Index.php

Hostels — New Blade Views (10)

Matching views for each new component above.

Farms — New Livewire PHP (17)

app/Http/Livewire/Navigation.php
app/Http/Livewire/FarmDashboard.php
app/Http/Livewire/Tasks/Index.php
app/Http/Livewire/DailyReports/Create.php
app/Http/Livewire/DailyReports/Index.php
app/Http/Livewire/DailyReports/Show.php
app/Http/Livewire/Crops/Index.php
app/Http/Livewire/Crops/Show.php
app/Http/Livewire/Crops/RecordHarvest.php
app/Http/Livewire/Livestock/Index.php
app/Http/Livewire/Livestock/Show.php
app/Http/Livewire/Requests/Index.php
app/Http/Livewire/Requests/Create.php
app/Http/Livewire/Requests/Show.php
app/Http/Livewire/Attendance/Index.php
app/Http/Livewire/FarmMap.php
app/Http/Livewire/Reports/Index.php

Farms — New Blade Views + Layout (18)

resources/views/layouts/app.blade.php
resources/views/livewire/navigation.blade.php
resources/views/livewire/farm-dashboard.blade.php
... (one view per component above)

Farms — Route File Update (1)

Modules/Farms/routes/web.php — add all portal routes

Service Provider Updates (2)

HostelsServiceProvider.php — register new components
FarmsServiceProvider.php — register all new components

 ---
Verification Plan

1. Serve app and visit /farms → verify list of farms renders
2. Click a farm → verify dashboard shows stat cards with live data
3. Visit task board → create a task, mark it complete
4. Submit a daily report → verify manager notification (check Log if CommunicationCentre not configured)
5. Visit /hostels/admin/{slug}/check-in → search a confirmed booking → check in
6. Visit /hostel-occupant/bookings/{id}/cancel → verify refund estimate shows correctly
7. Visit occupant portal → verify deposit section shows on booking detail
8. Visit /farms/{slug}/requests/create → add line items, submit, verify FR- reference generated
   ╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌
