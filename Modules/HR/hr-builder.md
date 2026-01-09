Here’s a **standalone, professional HR module spec** you can drop into `Modules/HR/project_rules.md` for Trae/Junie.

---

# `Modules/HR/project_rules.md`

## Module Name

**HR**

## Module Summary

The **HR** module is a **full Human Resources system** for Kharis ERP, shared by all companies under Kharis (Hostels, Farms, Construction, Water, Paper, etc.).

It must support:

* Multi-company HR (each employee belongs to a company, may work with others).
* Org structure: departments, positions.
* Employee lifecycle: hire → active → transfer → exit.
* Attendance & leave management.
* Basic payroll scaffolding (salary structures, payslips later).
* Document management.
* Simple performance appraisals (MVP).

It is built as a **toggleable Nwidart module** with Filament for admin and Livewire for internal HR dashboards.

---

## 1. Folder Structure

```text
Modules/HR/
  Config/
  Database/
    migrations/
  Providers/
    HRServiceProvider.php
    EventServiceProvider.php
  Http/
    Livewire/
      Employees/
        Index.php
        Show.php
        Create.php
        Edit.php
      Attendance/
        Index.php
      Leaves/
        Index.php
        Requests.php
      OrgChart/
        Index.php
  Models/
    Department.php
    JobPosition.php
    Employee.php
    EmploymentContract.php
    AttendanceRecord.php
    LeaveType.php
    LeaveRequest.php
    EmployeeDocument.php
    PerformanceCycle.php
    PerformanceReview.php
    SalaryScale.php
    EmployeeSalary.php
  Resources/
    views/
  Services/
    HRService.php
  Traits/
    BelongsToCompany.php   // optional helper
  Filament/
    Resources/
      DepartmentResource.php
      JobPositionResource.php
      EmployeeResource.php
      LeaveTypeResource.php
      LeaveRequestResource.php
      AttendanceRecordResource.php
      PerformanceCycleResource.php
      PerformanceReviewResource.php
      SalaryScaleResource.php
      EmployeeSalaryResource.php
  module.json
  Routes/
    web.php
```

---

## 2. Cross-Cutting Rules

* Use `company_id` on all HR models (Department, JobPosition, Employee, etc.).
* Respect the global `Company` model from Core (multi-company).
* Use Spatie Roles/Permissions/Shield for access control.
* HR module is mostly **internal** (staff admin) but should be ready for employee self-service later.

---

## 3. Data Model & Migrations

### 3.1 `Department`

**Table:** `hr_departments`

Fields:

* `id`
* `company_id`
* `name`
* `code` (nullable, e.g. “HR”, “OPS”)
* `description` (nullable)
* `parent_id` (nullable, self-join for hierarchy)
* `is_active` (bool)
* timestamps

---

### 3.2 `JobPosition`

**Table:** `hr_job_positions`

Fields:

* `id`
* `company_id`
* `department_id`
* `title`
* `code` (nullable, e.g. “HRM-01”)
* `description` (nullable)
* `grade` (nullable, e.g. “Senior”, “Junior”)
* `is_active` (bool)
* timestamps

---

### 3.3 `Employee`

**Table:** `hr_employees`

Fields:

* `id`
* `company_id`
* `user_id` (nullable FK → users.id for login access)
* `employee_code` (unique per company)
* `first_name`
* `last_name`
* `other_names` (nullable)
* `full_name` (cached)
* `gender` (nullable)
* `dob` (nullable)
* `phone`
* `alt_phone` (nullable)
* `email` (nullable)
* `national_id_number` (nullable)
* `marital_status` (nullable)
* `address` (nullable)
* `emergency_contact_name` (nullable)
* `emergency_contact_phone` (nullable)
* `department_id` (nullable)
* `job_position_id` (nullable)
* `hire_date`
* `employment_type` (enum: `full_time`, `part_time`, `contract`, `intern`)
* `employment_status` (enum: `active`, `probation`, `suspended`, `terminated`, `resigned`)
* `reporting_to_employee_id` (nullable, self-join)
* `photo_path` (nullable)
* timestamps

Relations:

* `employee->department`, `jobPosition`, `manager`, `contracts`, `attendanceRecords`, `leaveRequests`, `salary`, `performanceReviews`.

---

### 3.4 `EmploymentContract`

**Table:** `hr_employment_contracts`

Fields:

* `id`
* `employee_id`
* `company_id`
* `contract_number` (nullable)
* `start_date`
* `end_date` (nullable)
* `contract_type` (enum: `permanent`, `fixed_term`, `casual`)
* `probation_end_date` (nullable)
* `is_current` (bool)
* `basic_salary` (decimal)
* `currency` (string, default `GHS`)
* `working_hours_per_week` (nullable)
* `notes` (nullable)
* timestamps

---

### 3.5 `EmployeeDocument`

**Table:** `hr_employee_documents`

Fields:

* `id`
* `employee_id`
* `company_id`
* `document_type` (string: `CV`, `ID`, `CERTIFICATE`, `CONTRACT`, etc.)
* `file_path`
* `uploaded_by_user_id`
* `description` (nullable)
* timestamps

---

### 3.6 `AttendanceRecord`

**Table:** `hr_attendance_records`

Fields:

* `id`
* `employee_id`
* `company_id`
* `date`
* `status` (enum: `present`, `absent`, `leave`, `off`, `remote`)
* `check_in_time` (nullable)
* `check_out_time` (nullable)
* `notes` (nullable)
* timestamps

---

### 3.7 `LeaveType`

**Table:** `hr_leave_types`

Fields:

* `id`
* `company_id`
* `name` (e.g. “Annual Leave”, “Sick Leave”, “Maternity”)
* `code` (nullable)
* `description` (nullable)
* `max_days_per_year` (int, nullable)
* `requires_approval` (bool)
* `is_paid` (bool)
* `is_active` (bool)
* timestamps

---

### 3.8 `LeaveRequest`

**Table:** `hr_leave_requests`

Fields:

* `id`
* `employee_id`
* `company_id`
* `leave_type_id`
* `start_date`
* `end_date`
* `total_days` (decimal)
* `status` (enum: `draft`, `pending`, `approved`, `rejected`, `cancelled`)
* `reason` (text, nullable)
* `approved_by_employee_id` (nullable)
* `approved_at` (nullable)
* `rejected_reason` (nullable)
* timestamps

---

### 3.9 Salary Structures (MVP Payroll)

#### `SalaryScale`

**Table:** `hr_salary_scales`

* `id`
* `company_id`
* `name` (e.g. “Grade A”, “Management Band 1”)
* `code` (nullable)
* `min_basic` (decimal)
* `max_basic` (decimal)
* `currency` (string)
* `description` (nullable)
* timestamps

#### `EmployeeSalary`

**Table:** `hr_employee_salaries`

* `id`
* `employee_id`
* `company_id`
* `salary_scale_id` (nullable)
* `basic_salary` (decimal)
* `currency` (string)
* `effective_from`
* `effective_to` (nullable)
* `is_current` (bool)
* timestamps

This gives you HR + payroll linkage without full payslip calculations yet.

---

### 3.10 Performance Management (Lightweight)

#### `PerformanceCycle`

**Table:** `hr_performance_cycles`

* `id`
* `company_id`
* `name` (e.g. “2025 Mid-Year Review”)
* `start_date`
* `end_date`
* `status` (enum: `planned`, `open`, `closed`)
* `description` (nullable)
* timestamps

#### `PerformanceReview`

**Table:** `hr_performance_reviews`

* `id`
* `company_id`
* `performance_cycle_id`
* `employee_id`
* `reviewer_employee_id` (nullable)
* `rating` (decimal, e.g. 1–5)
* `comments` (text, nullable)
* `created_at`, `updated_at`

---

## 4. Traits / Helpers

### 4.1 `BelongsToCompany` (optional helper)

To DRY `company_id`:

```php
trait BelongsToCompany
{
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function company()
    {
        return $this->belongsTo(\Modules\Core\Models\Company::class);
    }
}
```

Apply on HR models.

---

## 5. Livewire Frontend – HR Internal UI

All HR front screens live under `Modules/HR/Http/Livewire`.

### 5.1 Routes

`Modules/HR/Routes/web.php`:

```php
Route::middleware(['web', 'auth', 'set-company']) // set-company uses user's company
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {
        Route::get('/employees', Employees\Index::class)->name('employees.index');
        Route::get('/employees/{employee}', Employees\Show::class)->name('employees.show');

        Route::get('/attendance', Attendance\Index::class)->name('attendance.index');

        Route::get('/leaves', Leaves\Index::class)->name('leaves.index');
        Route::get('/leave-requests', Leaves\Requests::class)->name('leaves.requests');

        Route::get('/org-chart', OrgChart\Index::class)->name('org-chart.index');
    });
```

---

### 5.2 HR Dashboard (Livewire)

You may add `HR\Dashboard`:

* High-level stats:

    * Number of employees per company.
    * Headcount by department.
    * Active vs probation vs terminated.
    * Upcoming contract expiries.
    * Employees on leave today.

---

### 5.3 Employees – Index & Profile

**Component:** `Employees\Index`

* Filters: department, employment_status, employment_type, search by name/code/phone.
* Columns: employee code, name, department, position, status, hire date.
* Actions: view profile, edit, deactivate/terminate.

**Component:** `Employees\Show`

* Tabs:

    * Profile (bio, contact details, reporting line)
    * Employment (department, position, contracts, salary history)
    * Attendance (recent attendance)
    * Leave (history of leave requests)
    * Documents (upload/download)
    * Performance (reviews summary)

---

### 5.4 Attendance

**Component:** `Attendance\Index`

Features:

* Day view: mark attendance for today (employees list with Present/Absent/Leave).
* Date filter: view past days.
* Quick filters: department, shift (if you add later).
* Bulk mark: mark whole department as present, then adjust.
* HR view can correct records.

Later you can integrate biometric/QR data.

---

### 5.5 Leave Management

**Component:** `Leaves\Index`

* For HR and managers:

    * Overview of all leave requests.
    * Filters: status, leave type, date range, department.
    * Approve / reject / cancel actions.

**Component:** `Leaves\Requests` (Employee self-service later)

* For logged-in employee:

    * List of own leave requests.
    * Form to create new leave request: choose type, start/end date, reason.
    * Show available balance (if you add leave balance calc).

Workflow:

1. Employee submits leave → `status = pending`.
2. Manager/HR reviews & approves/rejects.
3. If approved:

    * Set `approved_by_employee_id`.
    * Mark corresponding attendance days as `leave` (optional).

---

### 5.6 Org Chart

**Component:** `OrgChart\Index`

* Simple listing by Department → Employees.
* Show reporting lines (manager-subordinate).
* Not a graphical tree at MVP, but structured view.
* Optional: JSON API for future chart.

---

## 6. Filament Admin (HR)

Filament resources under `Modules/HR/Filament/Resources`.

### 6.1 `DepartmentResource`

* Manage departments, parent-child relations.
* Fields: name, code, parent, description, active.

### 6.2 `JobPositionResource`

* Manage job titles/grades.
* Fields: department, title, code, grade, active.

### 6.3 `EmployeeResource`

* Full CRUD for employees.

* Forms:

    * Personal info.
    * Contact & emergency info.
    * Employment info: department, position, hire date, employment type, status, manager.
    * Link to user account (optional).

* Tables with filters (department, status, type).

### 6.4 `LeaveTypeResource`

* Manage different leave types and their rules.

### 6.5 `LeaveRequestResource`

* Admin view of leave requests:

    * Approve / reject.
    * See history.

### 6.6 `AttendanceRecordResource`

* Admin access to all attendance records for auditing.
* Filters: by employee, date range.

### 6.7 `SalaryScaleResource` & `EmployeeSalaryResource`

* Manage salary scales, and assign salary structures to employees.

### 6.8 `PerformanceCycleResource` & `PerformanceReviewResource`

* Manage review cycles.
* View and record employee performance reviews.

All Filament resources:

* Must scope queries by `company_id` for non-super-admin users.
* Super admins can see all companies.

---

## 7. Events & Integration with Other Modules

HR must emit events and also listen to some.

### 7.1 Events Emitted

* `EmployeeCreated`
* `EmployeeUpdated`
* `EmployeeStatusChanged` (e.g. from active → terminated)
* `LeaveRequestCreated`
* `LeaveRequestApproved`
* `LeaveRequestRejected`
* `AttendanceMarked`

Other modules can use these for:

* CommunicationCentre: send welcome email, leave approval emails, etc.
* Finance: subscribe to EmployeeSalary changes (for payroll expense).
* Core: roles assignment when certain positions are created.

### 7.2 Events Listened To (from outside)

* `UserCreated` (from Core) → auto create Employee stub if needed.
* `CompanyCreated` → optionally scaffold default departments/positions.

---

## 8. Permissions & Roles

Use Spatie/Shield permission names like:

* `hr.view_dashboard`
* `hr.departments.manage`
* `hr.positions.manage`
* `hr.employees.view`
* `hr.employees.manage`
* `hr.attendance.manage`
* `hr.leaves.view_all`
* `hr.leaves.approve`
* `hr.performance.manage`
* `hr.salaries.manage`

Suggested HR roles:

* **HR Admin** – full HR module access.
* **HR Officer** – manage employees, attendance, leaves.
* **Manager** – view team, approve leave, read-only employee info.
* **Employee** – manage own profile & leave (later portal).

---

## 9. Module Toggling Behavior

* When `"enabled": true` in `module.json`:

    * Register HR models, migrations, routes, Livewire components, Filament resources, events.

* When `"enabled": false`:

    * No HR routes or resources.
    * Services from HR (if any) should either no-op or clearly error out.
    * Other modules must not hard-depend on HR (guard any HR calls with checks).

---

## 10. High-Level Summary for Trae / Junie

> Build a complete, toggleable Nwidart **HR** module for Kharis ERP that supports multi-company HR: departments, job positions, employee records, employment contracts, attendance, leave management, basic salary structures, and lightweight performance reviews. Use Filament for admin (department/position/employee/leave/attendance/salary/performance resources) and Livewire for the HR internal UI (employee list & profile, attendance page, leave requests, org chart). Every HR entity must be scoped by `company_id`. The module must emit events (e.g. EmployeeCreated, LeaveRequestApproved) that can be consumed by CommunicationCentre and Finance, and it must integrate cleanly with the shared `Company` and `User` models in the Core module.

If you want, next we can do an **“HR + CommunicationCentre” spec** so HR auto-sends emails/SMS on hire, leave approval, etc.
