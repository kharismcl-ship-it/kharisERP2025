# Hostel & Farms Frontend Analysis
_Generated: 2026-03-08_

---

## Part 1 — Hostels Module: Frontend Analysis

### What Is Fully Implemented

**Public Booking (unauthenticated):**
- `Public/Index` — hostel listing
- `Public/Show` — hostel detail with rooms/beds
- `Public/BookingWizard` (942 lines, 5-step): type selection → guest OTP verification → room/bed selection with dynamic pricing → terms review → payment gateway
- `Public/BookingConfirmation`, `BookingPayment`, `BookingPaymentReturn`, `BookingPaymentFailed`
- `Public/BookingChangeRequest`

**Hostel Occupant Portal (authenticated):**
- Auth: Login, Register
- Dashboard with active bookings + maintenance summary
- Bookings: Index, Create, Show
- Maintenance: Index, Create
- Profile: Edit

**Admin Livewire (staff):**
- Dashboard (statistics: beds, rooms, occupancy, incidents)
- HostelList, RoomList, BedList, HostelOccupantList, BookingList
- HostelChargeList, WhatsApp groups + message viewer
- Incidents/Index, Maintenance/Index, Visitors/Index (with signature capture)
- Reports/Index, BookingChangeRequests, Admin/BookingApproval

---

### Identified Gaps

#### GAP 1 — Admin Check-In / Check-Out Operational Flow (CRITICAL)
The `Booking` model has `checkIn()`, `actual_check_in_at`, `guest_check_in_signature`, but there is no Livewire admin page for processing a physical check-in. `CheckInOutCalendar` is a Filament page, not an operational front-desk page.

**Must build:**
- `Admin/CheckIn.php` — search booking by reference, capture signature, update bed to `occupied`, send welcome notification
- `Admin/CheckOut.php` — calculate final balance, apply policy deductions, initiate refund via PaymentsChannel, release bed

#### GAP 2 — No Booking Cancellation Flow for Occupants (CRITICAL)
`BookingCancellationPolicy` model is complete but occupant portal has no "Cancel my booking" page.

**Must build:**
- `HostelOccupant/Bookings/Cancel.php` — show refund estimate from policy, confirm, cancel + initiate refund

#### GAP 3 — No Deposit Visibility or Collection (CRITICAL)
`Deposit` model is complete (markAsCollected, processRefund, GL integration) but not surfaced anywhere in the frontend.

**Must build:**
- Deposit status card in `HostelOccupant/Bookings/Show`
- `Admin/DepositCollection.php` — operational page to mark deposit collected, issue receipt

#### GAP 4 — No Fee/Charge Breakdown for Occupants (IMPORTANT)
`FeeType`, `BookingCharge`, `HostelUtilityCharge` exist in backend. Wizard calculates mandatory fees but does not show itemized breakdown. Occupants can't see applied charges post-booking.

**Must build:**
- Itemized fee table in BookingWizard Step 3
- "Charges" tab in `HostelOccupant/Bookings/Show`

#### GAP 5 — No Payment History / Receipt Download (IMPORTANT)
Occupants see booking status but not individual payment transactions or downloadable receipts.

**Must build:**
- "Payments" tab in `HostelOccupant/Bookings/Show`
- Print-friendly / PDF receipt page

#### GAP 6 — No Occupant Incident Reporting (IMPORTANT)
`Incident` model has `occupant_id` FK but occupant portal has no incident report form.

**Must build:**
- `HostelOccupant/Incidents/Create.php` — hostel/room, title, description, severity

#### GAP 7 — Visitor Pre-Registration by Occupants (NICE TO HAVE)
`VisitorLog` exists with signature capture and occupant association. Occupants cannot pre-register visitors.

**Must build:**
- `HostelOccupant/Visitors/Create.php` — pre-register visitor (name, phone, expected time)

#### GAP 8 — PricingPolicy Not Applied in Booking Wizard (IMPORTANT)
`PricingPolicy` supports seasonal/occupancy-based adjustments but the wizard reads raw room rate fields without applying dynamic pricing rules.

**Must build:**
- Pricing service call in BookingWizard step 3 to apply applicable `PricingPolicy` rules before quoting

#### GAP 9 — Admin Reports Are Shallow (IMPORTANT)
`Reports/Index.php` exists but only basic counters. Backend has full data for rich reporting.

**Must build:**
- Occupancy rate over time (chart)
- Revenue vs deposits collected
- Maintenance request SLA report
- Booking source/type breakdown

---

## Part 2 — Hostels: Backend vs Frontend Comparison

| Backend Feature | Filament Admin | Livewire Frontend | Gap |
|---|---|---|---|
| Hostel CRUD | Yes | Yes (list/show) | None |
| Room/Bed management | Yes | Yes (admin list) | None |
| Booking lifecycle | Yes | Yes (wizard + approval) | Check-in/out operational flow missing |
| Deposit management | Yes | No occupant view, no admin collect page | CRITICAL |
| BookingCancellationPolicy | Yes | No occupant cancel flow | CRITICAL |
| FeeType / BookingCharge | Yes | No itemized view | IMPORTANT |
| UtilityCharge | Yes | No frontend | Nice to have |
| BillingCycle / BillingRule | Yes | No occupant view | Nice to have |
| PricingPolicy | Yes | Not applied in wizard | IMPORTANT |
| HostelOccupant documents | Yes | No upload UI in portal | Nice to have |
| Incident reporting | Yes | Admin only; no occupant form | IMPORTANT |
| VisitorLog | Yes (with signature) | Admin only; no occupant pre-register | Nice to have |
| Maintenance tracking | Yes | Both admin + occupant | None |
| WhatsApp groups | Yes | Admin view/messages | Compose/send missing |
| HousekeepingSchedule | Yes | No frontend | Nice to have |
| Inventory (room assignments) | Yes | No frontend | Nice to have |
| Staff shifts/attendance | Yes | No self-service portal | Nice to have |
| Payroll | Yes | No frontend | Nice to have |
| Reports/analytics | Yes (Filament) | Shallow Livewire page | IMPORTANT |
| Payment history/receipts | Yes (via PaymentsChannel) | Not surfaced in portal | IMPORTANT |

---

## Part 3 — Farms: Recommended Frontend Flows

The Farms module has 22 Filament admin resources, 38 database tables, FarmService with 25+ methods, but almost no frontend — only a stub `FarmIndex` Livewire component.

Recommended: **Farm Operations Portal** — a simplified interface for farm managers and workers.

### Flow A — Farm Dashboard
**Route:** `GET /farms/{farm:slug}`
- Active crop cycles with growth stage progress bars
- Today's weather (inline quick-entry)
- Livestock batch summary (count by type, recent mortality alerts)
- Today's open tasks
- Recent daily reports (submitted/pending review)
- Budget vs actual summary (`FarmService::budgetVsActual()`)
- Farm map with plot status overlay

### Flow B — Task Board
**Route:** `GET /farms/{farm:slug}/tasks`
- Grouped by status (pending / in_progress / completed / overdue)
- Filter by crop cycle, plot, livestock batch, task type, worker, priority
- Inline mark complete + completion note
- Create task modal
- Overdue tasks highlighted (`FarmTask::is_overdue`)

### Flow C — Daily Report Submission
**Route:** `GET /farms/{farm:slug}/daily-report`
- Worker: activities done, issues, recommendations, weather, photos → Submit → `FarmDailyReportSubmitted` event
- Manager view: list by worker/date, review/approve/reject

### Flow D — Crop Monitoring
**Route:** `GET /farms/{farm:slug}/crops/{cropCycle}`
- Cycle summary (planting date, expected harvest, status)
- Plot boundary on map
- Quick-log: scouting record, input application, activity
- Timeline of all activities/scouting/inputs

### Flow E — Livestock Quick Actions
**Route:** `GET /farms/{farm:slug}/livestock/{batch}`
- Batch summary (type, current count, status)
- Quick-log: feed today, weight record, mortality (auto-decrements count), health event
- Next health due dates widget
- Growth rate chart (`FarmService::livestockGrowthRate()`)

### Flow F — Harvest Recording
**Route:** `GET /farms/{farm:slug}/crops/{cropCycle}/harvest`
- Form: harvest date, quantity, unit price, buyer, storage, photo
- Calls `FarmService::recordHarvest()` → HarvestRecord, marks cycle `harvested`, updates `FarmProduceInventory`, creates Finance Invoice
- Running harvest total vs expected yield

### Flow G — Farm Requests
**Route:** `GET /farms/{farm:slug}/requests`
- Worker: create request (type, urgency, line items with ProcurementInventory linking) → auto-ref `FR-YYYYMM-00001` → `FarmRequestStatusChanged` event
- Manager: pending queue, approve/reject with reason
- Fulfilled state when items received

### Flow H — Worker Attendance
**Route:** `GET /farms/{farm:slug}/attendance`
- List active workers with status selector per row (present/absent/half_day/leave)
- Hours worked + overtime fields
- Bulk submit for day
- View past attendance by week/month

### Flow I — Farm Map
**Route:** `GET /farms/{farm:slug}/map`
- Full-screen FilamentLeaflet map
- Plot boundaries from `FarmPlot::geometry` (GeoJSON), color-coded by status
- Click plot → active crop cycle info
- Livestock batch markers

### Flow J — Reports & Analytics
**Route:** `GET /farms/{farm:slug}/reports`
- Revenue vs Expenses chart (`FarmService::netProfit()`)
- Budget vs Actual by category (`FarmService::budgetVsActual()`)
- Crop cycle P&L table (`FarmService::cropCyclePnL()`)
- Feed conversion ratio (`FarmService::feedConversionRatio()`)
- Date range filter

---

## Implementation Priority Order

### Hostels (implement in this order)
1. Admin CheckIn page with signature capture
2. Admin CheckOut page with balance + refund
3. Occupant: Cancel booking with policy refund estimate
4. Deposit status in Occupant booking view + Admin DepositCollection page
5. Fee/charge breakdown in BookingWizard step 3 + Occupant booking view
6. Payment history + receipt in occupant portal
7. PricingPolicy applied in wizard
8. Occupant incident reporting
9. Visitor pre-registration by occupants
10. Richer admin reports

### Farms (implement in this order)
1. Farm Dashboard (Flow A)
2. Task Board (Flow B)
3. Daily Report Create + Manager Review (Flow C)
4. Crop Monitoring (Flow D)
5. Livestock Quick Actions (Flow E)
6. Harvest Recording (Flow F)
7. Farm Requests with approval (Flow G)
8. Worker Attendance (Flow H)
9. Farm Map (Flow I)
10. Reports & Analytics (Flow J)