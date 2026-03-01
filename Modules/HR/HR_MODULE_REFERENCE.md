# HR Module Reference

## Overview

The HR module provides end-to-end human resource management for KharisERP2025. It is built on top of `nwidart/laravel-modules` and integrates with Filament v4 admin panels.

## Feature Areas

### 1. Payroll

**Tables:** `hr_payroll_runs`, `hr_payroll_lines`, `hr_allowance_types`, `hr_deduction_types`

**Navigation Group:** Payroll (sorts 50–52)

| Resource | Sort | Description |
|---|---|---|
| PayrollRunResource | 50 | Full payroll cycle management |
| AllowanceTypeResource | 51 | Define allowance types (fixed / %) |
| DeductionTypeResource | 52 | Define deduction types (tax, loan, etc.) |

**PayrollRun lifecycle:** `draft` → `processing` → `finalized` → `paid`

**Service:** `PayrollService`
- `generatePayrollRun($companyId, $year, $month)` — creates run + lines for all active employees
- `calculateEmployeePayroll($run, $employee)` — builds one PayrollLine
- `calculatePAYE($taxableIncome)` — Ghana PAYE bands (monthly)
- `calculateSSNIT($basicSalary)` — 5.5% employee / 13% employer
- `finalizePayrollRun($run)` — locks the run
- `postToFinance($run)` — (stub) creates Journal Entry

---

### 2. Workforce (Shifts & Holidays)

**Tables:** `hr_shifts`, `hr_shift_assignments`, `hr_public_holidays`

**Navigation Group:** Workforce (sorts 53–54)

| Resource | Sort | Description |
|---|---|---|
| ShiftResource | 53 | Define shifts with days/times, assign employees |
| PublicHolidayResource | 54 | Manage public holidays per company |

**Shift model:** `days_of_week` cast to array (0=Sunday … 6=Saturday). `day_names` accessor returns human-readable string.

---

### 3. Recruitment / ATS

**Tables:** `hr_job_vacancies`, `hr_applicants`, `hr_interviews`

**Navigation Group:** Recruitment (sort 55)

| Resource | Sort | Description |
|---|---|---|
| JobVacancyResource | 55 | Job postings with applicant tracking |

**JobVacancy lifecycle:** `draft` → `open` → `closed` / `filled`

**Applicant statuses:** `applied`, `screening`, `shortlisted`, `interview`, `offer`, `hired`, `rejected`

---

### 4. Learning & Development

**Tables:** `hr_training_programs`, `hr_training_nominations`, `hr_certifications`

**Navigation Group:** Learning & Development (sorts 56–57)

| Resource | Sort | Description |
|---|---|---|
| TrainingProgramResource | 56 | Training programs with nominations |
| CertificationResource | 57 | Employee certifications + expiry tracking |

**TrainingProgram lifecycle:** `planned` → `active` → `completed` / `cancelled`

**Certification:** `is_expired` accessor (past expiry_date), `is_expiring_soon` accessor (within 30 days).

---

### 5. Employee Relations

**Tables:** `hr_disciplinary_cases`, `hr_grievance_cases`

**Navigation Group:** Employee Relations (sorts 60–61)

| Resource | Sort | Description |
|---|---|---|
| DisciplinaryCaseResource | 60 | Disciplinary proceedings |
| GrievanceCaseResource | 61 | Grievance cases (supports anonymous) |

**DisciplinaryCase lifecycle:** `open` → `under_investigation` → `resolved` / `closed`

**GrievanceCase:** `is_anonymous` bool — when true the table shows "Anonymous" instead of employee name.

---

### 6. Performance Management

**Tables:** `hr_employee_goals`, `hr_kpi_definitions`

**Navigation Group:** Performance (sorts 62–63)

| Resource | Sort | Description |
|---|---|---|
| EmployeeGoalResource | 62 | SMART goals with progress tracking |
| KpiDefinitionResource | 63 | KPI definitions for appraisals |

**EmployeeGoal:** `completion_percentage` accessor = `round((actual_value / target_value) * 100, 1)`. Lifecycle: `draft` → `in_progress` → `completed` / `cancelled`.

---

### 7. Benefits & Loans

**Tables:** `hr_benefit_types`, `hr_employee_benefits`, `hr_employee_loans`, `hr_loan_repayments`

**Navigation Group:** Benefits & Loans (sorts 64–65)

| Resource | Sort | Description |
|---|---|---|
| BenefitTypeResource | 64 | Benefit types with enrollment management |
| EmployeeLoanResource | 65 | Loan applications + repayment tracking |

**EmployeeLoan lifecycle:** `pending` → `approved` → `active` → `cleared` / `rejected`

On approval: `outstanding_balance` is set to `principal_amount`. Each `LoanRepayment` reduces it.

---

### 8. Announcements

**Tables:** `hr_announcements`, `hr_announcement_reads`

**Navigation Group:** HR Comms (sort 66)

| Resource | Sort | Description |
|---|---|---|
| AnnouncementResource | 66 | Company-wide or targeted announcements |

**Target audiences:** `all`, `department`, `job_position`

**Service:** `AnnouncementService`
- `publish($announcement)` — sets `is_published=true`, `published_at=now()`
- `unpublish($announcement)`
- `markAsRead($announcement, $employee)` — idempotent via `firstOrCreate`
- `getForEmployee($employee)` — audience-filtered active announcements
- `sendViaCommunicationCentre($announcement)` — stub for email/SMS

---

### 9. HR Analytics Dashboard

**Page:** `HrAnalyticsDashboard` — navigation group `HR Comms`, sort 70

**Blade view:** `Modules/HR/resources/views/filament/pages/hr-analytics-dashboard.blade.php`

**Stats shown:**
- Total / Active employees
- On leave today
- Pending leave requests
- Open vacancies
- Active loans
- Active training programs
- Last paid payroll period

---

## Migrations (new — prefix `2026_03_01_6xxxxx`)

| File | Table |
|---|---|
| 600001 | hr_allowance_types |
| 600002 | hr_deduction_types |
| 600003 | hr_payroll_runs |
| 600004 | hr_payroll_lines |
| 600005 | hr_shifts |
| 600006 | hr_shift_assignments |
| 600007 | hr_public_holidays |
| 600008 | hr_job_vacancies |
| 600009 | hr_applicants |
| 600010 | hr_interviews |
| 600011 | hr_training_programs |
| 600012 | hr_training_nominations |
| 600013 | hr_certifications |
| 600014 | hr_disciplinary_cases |
| 600015 | hr_grievance_cases |
| 600016 | hr_employee_goals |
| 600017 | hr_kpi_definitions |
| 600018 | hr_benefit_types |
| 600019 | hr_employee_benefits |
| 600020 | hr_employee_loans |
| 600021 | hr_loan_repayments |
| 600022 | hr_announcements |
| 600023 | hr_announcement_reads |

## Policy Files (all permissive — Spatie Shield enforces actual permissions)

`Modules/HR/app/Policies/`: AllowanceTypePolicy, AnnouncementPolicy, BenefitTypePolicy, CertificationPolicy, DeductionTypePolicy, DisciplinaryCasePolicy, EmployeeBenefitPolicy, EmployeeGoalPolicy, EmployeeLoanPolicy, GrievanceCasePolicy, InterviewPolicy, JobVacancyPolicy, KpiDefinitionPolicy, LoanRepaymentPolicy, PublicHolidayPolicy, ShiftAssignmentPolicy, ShiftPolicy, TrainingNominationPolicy, TrainingProgramPolicy, and 5 others.

## Filament v4 Patterns Used

- `Schema->components([Section->schema([...])->columns(n)])` — columns on Section, NOT Schema root
- `TextColumn->badge()->color(fn(string $state): string => match($state){...})`
- All actions from `Filament\Actions\*`
- `infolist(Schema $schema)` in ViewRecord pages
- `$this->refreshFormData([...])` in ViewRecord header actions
- RelationManagers use `headerActions([CreateAction::make()])` + `actions([EditAction, DeleteAction])`
- List pages: `CreateAction::make()->slideOver()` in header
- Edit pages: `DeleteAction::make()` in header