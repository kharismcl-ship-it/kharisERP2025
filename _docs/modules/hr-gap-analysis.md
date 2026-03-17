# HR Module — Completeness & Integration Analysis

**Date:** 2026-03-13
**Overall completion estimate: ~60–65%**

The module has excellent skeleton coverage (models, migrations, Filament resources for nearly every domain) but several critical service layers, event wires, and portal flows are either stubs or unconnected.

---

## Critical Gaps — Tier 1 (Break production workflows)

### 1. Payroll → Finance GL posting not automatic
`PayrollRunResource.php` fires `PayrollFinalized` event when "Mark as Paid" is clicked. The Finance module's `EventServiceProvider` has a `RecordPayrollExpense` listener registered for this event — but the **HR module's `EventServiceProvider` does NOT list this listener**, so whether it fires depends on auto-discovery. The `postToFinance()` method on `PayrollService` exists but is only callable manually.

**Fix:** Register `RecordPayrollExpense` listener explicitly in HR `EventServiceProvider`, or verify auto-discovery covers it. Add a "Post to Finance" action button on `PayrollRunResource` as a manual fallback.

---

### 2. Announcement email/SMS is a stub
`AnnouncementService::sendAnnouncement()` has a `sendViaCommunicationCentre()` call that is a TODO stub — it does nothing. The "Send Email" and "Send SMS" toggles on announcements silently fail.

**Fix:** Wire `CommunicationService::sendFromTemplate()` with the target audience (all employees / department / job position) loop.

---

### 3. Leave portal bypass of approval workflow
The employee portal leave components do **direct `$leaveRequest->update(['status' => 'approved'])` calls** without going through `LeaveApprovalService::initializeApprovalProcess()`. This means:
- Multi-level approval workflows are silently skipped
- No approval notifications are dispatched
- Leave balance is NOT deducted on portal approval

**Fix:** Portal leave submission must call `LeaveApprovalService::initializeApprovalProcess($leaveRequest)`. Portal approval button must call `LeaveApprovalService::processApproval()`.

---

### 4. Employee loans not deducted in payroll
`EmployeeLoan` model and Filament resource exist. The `PayrollService::calculateEmployeePayroll()` method does NOT query active loans for monthly deductions. Loan repayments exist only as records — they never reduce net pay.

**Fix:** In `PayrollService`, after calculating deductions, query active loans and add `monthly_deduction` to the deductions total. Create `LoanRepayment` records per payroll cycle.

---

### 5. Leave accrual has no scheduled job
`LeaveType` has `has_accrual`, `accrual_rate`, and `accrual_frequency` fields. There is no scheduled command or job that runs monthly to add accrued days to `LeaveBalance` records. The carry-over job (January 1) exists, but monthly accrual does not.

**Fix:** Create `ProcessLeaveAccrual` artisan command; schedule it monthly.

---

### 6. Company assignment end does not revoke access
`CompanyAssignmentService::endAssignment()` sets `end_date = now()` on the assignment record but does NOT revoke the employee's Spatie role for that company. The employee retains permissions for a company they're no longer deployed to.

**Fix:** Call `$employee->user->removeRole($role, $companyId)` inside `endAssignment()`.

---

## Important Gaps — Tier 2 (Fix before user training)

### 7. Employee auto-user creation disabled
`EmployeeObserver::created()` has a `createUserFromEmployee()` method that is commented out / disabled. New employees don't get a user account automatically. They cannot log in to the employee portal (`/hr`) unless HR manually creates a user and links it.

**Fix:** Either re-enable auto-user creation in the observer, or add a "Create Portal Account" action to `EmployeeResource`.

---

### 8. Benefits not deducted in payroll
`EmployeeBenefit` and `BenefitType` Filament resources exist. Employee contribution amounts are not deducted in `PayrollService`. Employer contributions are also not journalled to Finance.

**Fix:** Add benefit contribution deduction to `PayrollService::calculateEmployeePayroll()`.

---

### 9. Performance — no KPI score entry UI
`KpiDefinition` model and resource exist. There is no UI to record actual KPI values against a definition per review cycle. `PerformanceReview` has a `rating` field but no linked KPI score breakdown.

**Fix:** Add `KpiScore` model (kpi_definition_id, performance_review_id, actual_value, score) + inline relation on the `PerformanceReview` form.

---

### 10. Org chart is list-only
The portal route `/hr/org-chart` is registered and renders. The Livewire component outputs a flat or nested list — no visual tree.

**Fix:** Implement a CSS/HTML recursive tree using nested `<ul>` with Tailwind left-border styling using `Employee::where('reporting_to', null)->with('subordinates.subordinates')->get()`.

---

### 11. Disciplinary/Grievance — no notifications or status workflow service
Both models and Filament resources exist. Status transitions have no service layer, no notifications, no event dispatches. The `handled_by` employee is never notified of assignment.

**Fix:** Add `DisciplinaryService::assign($case, $handledBy)` and `resolve($case, $resolution)` with `CommunicationService::sendFromTemplate()` calls.

---

### 12. Recruitment → onboarding gap
When an applicant's status is set to `hired`, there is no automatic creation of an `Employee` record or trigger of `NewEmployeeOnboarded` event. The two are completely disconnected.

**Fix:** Add a "Convert to Employee" action on `ApplicantResource` that pre-fills an employee form from applicant data.

---

## Cross-Module Integration Map

| Integration | Status | Notes |
|---|---|---|
| HR Payroll → Finance GL | Partial | `RecordPayrollExpense` listener exists in Finance. HR EventServiceProvider registration needs verification. |
| HR `NewEmployeeOnboarded` → ProcurementInventory `CreateOnboardingItemsPO` | Working | Listener registered, creates PO for onboarding items |
| Hostels `PayrollSyncService` → HR `AttendanceRecord` | Working | Bidirectional sync for hostel staff |
| HR → CommunicationCentre (announcements) | Broken | `AnnouncementService` is a stub |
| HR → CommunicationCentre (leave notifications) | Partial | Only fires on leave creation, not on approval/rejection |
| HR → Finance FixedAsset custodian | Working | `fixed_assets.employee_id` FK links to HR employee |
| CommunicationCentre FilamentDatabaseProvider → HR Employee | Working | Maps `Employee` to `User` for in-app notifications |
| HR EmployeeLoan → Payroll deduction | Broken | Loan records exist, deduction never applied |
| HR Benefits → Payroll deduction | Broken | Benefit records exist, deduction never applied |
| HR → Finance (per-employee payslip journal) | Missing | No per-employee payslip journal entries — only run-level aggregate |

---

## Prioritised Implementation Plan

**Tier 1 — Fix before going live (breaks money/compliance):**
1. Loan deduction in payroll
2. Payroll → Finance GL posting (verify listener + add manual action)
3. Leave portal approval workflow bypass
4. Leave accrual scheduled job
5. Company assignment end → revoke role

**Tier 2 — Fix before user training:**
6. Announcement CommunicationCentre wiring
7. Employee auto-user creation (portal login)

**Tier 3 — Complete after go-live:**
8. Benefits payroll deduction
9. KPI score entry UI
10. Recruitment → Employee conversion action
11. Disciplinary/Grievance notifications
12. Org chart visual tree
