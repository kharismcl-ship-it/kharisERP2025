Here’s a clean, **standalone Hostel Module spec** you can hand to Trae/Junie to build something that feels like a serious, commercial hostel management system.

I’ll focus only on **Hostels**, but design it so it plugs into your Core/Finance/HR/etc later.

---

## 1. Hostel Module – Purpose & Scope

**Module name:** `Hostels` (Nwidart: `Modules/Hostels`)
**Front-end:** Livewire v3
**Admin:** Filament resources inside the module

The Hostel module should handle:

* Multiple hostels under one company (Kharis Hostels).
* Long-stay (academic/yearly) and short-stay (daily) booking.
* Rooms & beds management.
* Hostel occupants (mostly students), guarantors, documents.
* Billing, invoices (via Finance), and payments.
* Check-in, check-out, room changes.
* Maintenance requests and incidents.
* Professional reports & analytics.
* Notifications (email/SMS/WhatsApp later).

---

## 2. Core Data Model (Hostel Domain)

### 2.1 `Hostel`

Represents each physical hostel.

**Table:** `hostels`

Fields:

* `id`
* `company_id` (FK → companies.id, from Core)
* `name` (e.g. “Kharis Hostel – East Legon”)
* `slug` (for URLs)
* `code` (short ref, e.g. KH-EAST)
* `location` (string)
* `latitude` (decimal, nullable)
* `longitude` (decimal, nullable)
* `country` (string)
* `contact_phone` (nullable)
* `contact_email` (nullable)
* `contact_name` (nullable)
* `photo` (nullable)
* `description` (nullable)
* `city` (string)
* `region` (string, nullable)
* `capacity` (int, cached total beds)
* `gender_policy` (enum: `male`, `female`, `mixed`)
* `check_in_time_default` (time, nullable)
* `check_out_time_default` (time, nullable)
* `status` (enum: `active`, `inactive`)
* `notes` (text, nullable)
* timestamps

Relations:

* `hostel->blocks()`, `hostel->rooms()`, `hostel->beds()`, `hostel->hostelOccupants()`, `hostel->bookings()`

---

### 2.2 `Block` / `Floor` (optional but professional)

If your hostels are large, model blocks/floors.

**Table:** `hostel_blocks`

* `id`
* `hostel_id`
* `name` (e.g. “Block A”)
* `description` (nullable)
* timestamps

**Table:** `hostel_floors`

* `id`
* `hostel_id`
* `block_id` (nullable)
* `name` (e.g. “Ground Floor”, “1st Floor”)
* `level` (int, nullable)
* timestamps

---

### 2.3 `Room`

**Table:** `hostel_rooms`

Fields:

* `id`
* `hostel_id`
* `block_id` (nullable)
* `floor_id` (nullable)
* `room_number` (string)
* `room_type` (enum: `single`, `double`, `triple`, `quad`, `dorm`)
* `gender_policy` (enum: `male`, `female`, `mixed`, `inherit_hostel`)
* `base_rate` (decimal)
* `billing_cycle` (enum: `per_night`, `per_semester`, `per_year`)
* `max_occupancy` (int)
* `current_occupancy` (int, for quick stats)
* `status` (enum: `available`, `partially_occupied`, `full`, `maintenance`, `closed`)
* `notes` (nullable)
* timestamps

---

### 2.4 `Bed`

**Table:** `hostel_beds`

Fields:

* `id`
* `room_id`
* `bed_number` (string, e.g. “A”, “B”, “1”, “2”)
* `status` (enum: `available`, `reserved`, `occupied`, `maintenance`, `blocked`)
* `is_upper_bunk` (bool, nullable)
* `notes` (nullable)
* timestamps

--- 

### 2.5 `HostelOccupant` (Student/Resident)

**Table:** `hostel_occupants`

Fields:

* `id`
* `hostel_id`
* `first_name`
* `last_name`
* `other_names` (nullable)
* `full_name` (cached)
* `gender`
* `dob` (nullable)
* `phone`
* `alt_phone` (nullable)
* `email` (nullable)
* `national_id_number` (nullable)
* `student_id` (nullable)
* `institution` (nullable)
* `guardian_name` (nullable)
* `guardian_phone` (nullable)
* `guardian_email` (nullable)
* `address` (nullable)
* `emergency_contact_name` (nullable)
* `emergency_contact_phone` (nullable)
* `status` (enum: `prospect`, `active`, `inactive`, `blacklisted`)
* timestamps

Optional relation to global `users` later if you add portal:

* `user_id` (nullable, FK → users.id)

---

### 2.6 `HostelOccupantDocument`

**Table:** `hostel_occupant_documents`

Fields:

* `id`
* `hostel_occupant_id`
* `document_type` (e.g. `id_card`, `admission_letter`, `guarantor_form`)
* `file_path`
* `uploaded_by` (user_id)
* timestamps

---

### 2.7 `Booking`

This is the heart.

**Table:** `hostel_bookings`

Fields:

* `id`
* `hostel_id`
* `room_id`
* `bed_id` (nullable if room-only booking)
* `hostel_occupant_id`
* `booking_reference` (string, unique)
* `booking_type` (enum: `academic`, `short_stay`)
* `academic_year` (nullable, string)
* `semester` (nullable, string)
* `check_in_date` (date)
* `check_out_date` (date)
* `actual_check_in_at` (datetime, nullable)
* `actual_check_out_at` (datetime, nullable)
* `status` (enum: `pending`, `awaiting_payment`, `confirmed`, `checked_in`, `checked_out`, `no_show`, `cancelled`)
* `total_amount` (decimal)
* `amount_paid` (decimal, default 0)
* `balance_amount` (decimal)
* `payment_status` (enum: `unpaid`, `partially_paid`, `paid`, `overpaid`)
* `channel` (enum: `walk_in`, `online`, `agent`)
* `notes` (text, nullable)
* timestamps

---

### 2.8 `ChargeTemplate` / `FeeType`

Template of fees that can be attached to bookings.

**Table:** `hostel_fee_types`

* `id`
* `hostel_id`
* `name` (e.g. “Rent – Semester”, “Utility Fee”, “Damage Penalty”)
* `code` (short code)
* `default_amount` (decimal)
* `billing_cycle` (enum: `one_time`, `per_semester`, `per_year`, `per_night`)
* `is_mandatory` (boolean)
* `is_active` (boolean)
* timestamps

**Table:** `hostel_booking_charges`

* `id`
* `booking_id`
* `fee_type_id` (nullable, for tracking origin)
* `description`
* `quantity` (decimal)
* `unit_price` (decimal)
* `amount` (decimal)
* timestamps

---

### 2.9 `MaintenanceRequest`

**Table:** `hostel_maintenance_requests`

Fields:

* `id`
* `hostel_id`
* `room_id` (nullable)
* `bed_id` (nullable)
* `reported_by_hostel_occupant_id` (nullable)
* `reported_by_user_id` (nullable)
* `title`
* `description`
* `priority` (enum: `low`, `medium`, `high`, `urgent`)
* `status` (enum: `open`, `in_progress`, `completed`, `cancelled`)
* `assigned_to_user_id` (nullable)
* `reported_at` (datetime)
* `completed_at` (nullable)
* timestamps

---

### 2.10 `Incident` (discipline / security issues)

**Table:** `hostel_incidents`

* `id`
* `hostel_id`
* `hostel_occupant_id` (nullable)
* `room_id` (nullable)
* `title`
* `description`
* `severity` (enum: `minor`, `major`, `critical`)
* `reported_by_user_id`
* `action_taken` (nullable)
* `status` (enum: `open`, `resolved`, `escalated`)
* `reported_at`
* `resolved_at` (nullable)
* timestamps

---

### 2.11 `VisitorLog` (optional but professional)

**Table:** `hostel_visitor_logs`

* `id`
* `hostel_id`
* `hostel_occupant_id` (nullable)
* `visitor_name`
* `visitor_phone` (nullable)
* `purpose` (text, nullable)
* `check_in_at`
* `check_out_at` (nullable)
* `recorded_by_user_id`
* timestamps

---

## 3. Roles & Permissions (Hostel Module)

Recommended roles (inside Kharis Hostels):

* **Hostel Owner / Super Admin** – full access.
* **Hostel Manager** – manage hostels, rooms, beds, hostel occupants, bookings, billing.
* **Front Desk / Reception** – create/edit bookings, check-in/out, basic hostel occupant/visitor management.
* **Finance Officer (Hostel)** – view and manage hostel invoices/payments (through Finance module).
* **Maintenance Officer** – manage maintenance requests.
* **Security** – manage visitor logs, incidents (limited).
* **Student/Tenant** (later via portal) – view own bookings, pay fees, submit maintenance, view rules.

Use Spatie/Shield permissions like:

* `hostels.view`, `hostels.manage`
* `hostels.rooms.manage`
* `hostels.beds.manage`
* `hostels.hostel_occupants.manage`
* `hostels.bookings.manage`
* `hostels.maintenance.manage`
* `hostels.incidents.manage`
* `hostels.reports.view`

---

## 4. Livewire Frontend – Pages & Workflows

All of these Livewire components live in `Modules/Hostels/Http/Livewire`.

### 4.1 Routes

```php
Route::middleware(['web', 'auth', 'set-company:hostels'])
    ->prefix('hostels')
    ->name('hostels.')
    ->group(function () {
        Route::get('/', HostelList::class)->name('index');
        Route::get('{hostel:slug}', Dashboard::class)->name('dashboard');

        Route::get('{hostel:slug}/rooms', Rooms\Index::class)->name('rooms.index');
        Route::get('{hostel:slug}/beds', Beds\Index::class)->name('beds.index');

        Route::get('{hostel:slug}/hostel-occupants', HostelOccupants\Index::class)->name('hostel-occupants.index');
        Route::get('{hostel:slug}/hostel-occupants/{hostelOccupant}', HostelOccupants\Show::class)->name('hostel-occupants.show');

        Route::get('{hostel:slug}/bookings', Bookings\Index::class)->name('bookings.index');
        Route::get('{hostel:slug}/bookings/create', Bookings\Create::class)->name('bookings.create');
        Route::get('{hostel:slug}/bookings/{booking}', Bookings\Show::class)->name('bookings.show');

        Route::get('{hostel:slug}/maintenance', Maintenance\Index::class)->name('maintenance.index');
        Route::get('{hostel:slug}/incidents', Incidents\Index::class)->name('incidents.index');
        Route::get('{hostel:slug}/visitors', Visitors\Index::class)->name('visitors.index');

        Route::get('{hostel:slug}/reports', Reports\Index::class)->name('reports.index');
    });
```

---

### 4.2 Page: Hostel Selector

**Component:** `HostelList`

* Cards for each hostel user can access.
* Quick stats per hostel:

    * Occupancy %, beds free, active bookings, outstanding balances.
* Clicking card → `Dashboard`.

---

### 4.3 Page: Hostel Dashboard

**Component:** `Dashboard`

Shows:

* KPIs (for selected hostel):

    * Total beds, occupied beds, free beds.
    * Active bookings, pending bookings.
    * Total revenue (period filter: today/this month/semester).
    * Top debtors (hostel occupants with highest balance).

* Widgets:

    * Occupancy by room type.
    * Today’s check-ins/check-outs.
    * Open maintenance tickets.
    * Recent incidents.

---

### 4.4 Page: Rooms & Bed Map

**Component:** `Rooms\Index`, `Beds\Index`

Features:

* Filter by block, floor, status, room type.

* Grid view:

    * Each room card shows number, type, occupancy (e.g. “3/4 occupied”), status.
    * Click room → modal with beds list.

* Bed map in room modal:

    * Shows each bed with color-coded status: available/occupied/maintenance.
    * Click available bed → start booking wizard straight away.

Operations:

* Create/edit room.
* Set base rate & billing cycle.
* Add beds to room.
* Bulk edit: mark multiple rooms as maintenance.

---

### 4.5 Page: Tenants

**Component:** `Tenants\Index`, `Tenants\Show`

Index:

* Search by name, phone, student ID, room.
* Filters: hostel, status, blacklisted.
* Table columns: Tenant, phone, current room, balance, status.

Show:

* Profile details.
* Booking history.
* Financial summary (invoices, payments from Finance module).
* Documents list (download).
* Incidents associated with hostel occupant.

---

### 4.6 Booking Workflows

#### 4.6.1 Booking List

**Component:** `Bookings\Index`

* Table of bookings with filters: status, date range, room, hostel occupant.
* Quick actions: view, edit, check-in, check-out, cancel.
* Create booking button.

#### 4.6.2 Booking Creation Wizard

**Component:** `Bookings\Create`

Steps:

1. **Select hostel occupant:**

    * Search existing hostel occupant by name/phone/student ID.
    * Or create new hostel occupant inline.

2. **Select booking type & period:**

    * Booking type: `academic` or `short_stay`.
    * Academic: select academic year + semester + date range preset.
    * Short-stay: enter check-in date & expected check-out date.

3. **Select hostel, room, bed:**

    * Filter by gender policy, room type, price range.
    * Show bed map to select bed.
    * Prevent selecting bed already reserved/occupied within date range.

4. **Pricing & charges:**

    * Auto-calc base rent from room & period.
    * Add mandatory hostel fee templates.
    * Allow manual additional charges (e.g. key deposit, penalty).
    * Show breakdown + total.

5. **Review & confirm:**

    * Summary of hostel occupant + room/bed + dates + total.
    * On submit: create booking, create booking charges, set status to `pending` or `awaiting_payment`.

Optionally: trigger Finance invoice creation via a service (later).

---

### 4.7 Check-in / Check-out

From `Bookings\Show`:

* **Check-in:**

    * Allowed when `status` is `confirmed` or `awaiting_payment` (depending on your business rule).
    * Set `actual_check_in_at = now()`.
    * Change `status` to `checked_in`.
    * Set `bed.status = occupied`.
    * Update `room.current_occupancy`.

* **Check-out:**

    * From `checked_in` state.
    * On click: confirm any outstanding balance.
    * Optionally add final charges (e.g. damages).
    * Create/update invoice if necessary.
    * Set `actual_check_out_at = now()`.
    * Set `status = checked_out`.
    * Set `bed.status = available`; adjust occupancy.

---

### 4.8 Room Change / Bed Transfer

Allow moving a hostel occupant from one bed/room to another mid-stay.

* UI: action “Change Room/Bed” on booking.
* Steps:

    * Select new room/bed, date of change.
    * Close previous bed occupancy (for reporting).
    * Adjust booking charges if difference in rate (pro-rated).
    * Log change in a `hostel_booking_events` table (optional) for audit:

        * `booking_id`, `event_type` (`room_change`), `old_room_id`, `new_room_id`, etc.

---

### 4.9 Maintenance & Incidents

**Maintenance:**

* `Maintenance\Index` page:

    * Filter by status, priority, room.
    * Create new ticket: select room/bed, description, priority.
    * Assign to maintenance officer (user).
    * Update status (`open` → `in_progress` → `completed`).
    * On completion: optionally link to cost (future integration with Procurement/Finance).

**Incidents:**

* `Incidents\Index` page:

    * Create incident: select hostel, hostel occupant (optional), room (optional).
    * Record details, severity and action.
    * Track resolution state.

---

### 4.10 Visitors

**Visitors\Index**

* Simple CRUD of visitor records.
* Use at front desk/security.
* Quick action to close visits (set `check_out_at`).

---

### 4.11 Reports

**Reports\Index**

Typical reports:

* Occupancy report:

    * By hostel, by block, by room type, by gender.
* Revenue report:

    * Total rent & fees per hostel, per period (via Finance).
* Debtors report:

    * Tenants with outstanding balances.
* Booking trends:

    * Bookings by source/channel, month, academic year.
* Maintenance:

    * Open vs closed tickets, average resolution time.

(Implementation: query booking/hostel_occupant/maintenance tables, and Finance invoices via relationships.)

---

## 5. Filament Admin for Hostels

Inside `Modules/Hostels/Filament/Resources`:

Create resources for:

* `HostelResource` – manage hostels, blocks, floors.
* `RoomResource` – manage rooms, default pricing.
* `BedResource` – manage beds.
* `HostelOccupantResource` – admin-level hostel occupant view.
* `FeeTypeResource` – hostel fee templates.
* (Optionally) `MaintenanceRequestResource`, `IncidentResource` for admin view.

Each resource:

* Must scope queries by `company_id` and optionally current hostel (if needed).
* Should use relations to pick hostel, block, floor, etc.

---

## 6. Professional “Extra” Features to Aim For

You asked: **what other features make it professional?**

Here are extras that make this module feel SaaS-grade:

1. **Calendar views**

    * Calendar of check-ins/check-outs per hostel.
    * Room/bed availability calendar (like hotel PMS).

2. **Bulk operations**

    * Bulk assign beds for a group booking (e.g. 10 students from same institution).
    * Bulk generate invoices for all active academic bookings at semester start.

3. **Student/Tenant portal (later)**

    * Tenants login to see their room, invoices, payment history.
    * Raise maintenance requests.
    * Download hostel rules & documents.

4. **Notifications**

    * Email/SMS/WhatsApp reminders:

        * Upcoming check-in.
        * Payment due / overdue.
        * Check-out reminders.
    * Maintenance ticket updates.

5. **Audit logs**

    * Log who created/edited bookings, changed rooms, changed statuses.
    * Store in `hostel_audit_logs` or use activity log package.

6. **Attachments**

    * Attach photos of room condition at check-in/out.
    * Attach repair receipts to maintenance tickets.

7. **Configurable hostel policies**

    * Per-hostel settings:

        * Allowed number of visitors.
        * Curfew time.
        * Default billing models.

   **Table:** `hostel_settings` with `key/value` (JSON) per hostel.

8. **Exports**

    * Export hostel occupants, bookings, occupancy, debtors to Excel/CSV.

9. **Integration hooks**

    * Events for other modules:

        * `BookingCreated`, `BookingCheckedIn`, `BookingCheckedOut`, `MaintenanceCompleted`.
    * So Finance/HR or external systems can react.

---

If you want, next I can turn **this Hostel module spec alone** into a Trae/Junie **module-level project_rules.md** (only for `Modules/Hostels`) that tells the agent exactly how to scaffold migrations, models, Livewire components, and Filament resources.
