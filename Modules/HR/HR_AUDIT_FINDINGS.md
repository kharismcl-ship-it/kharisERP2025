# HR Module Audit — Findings & Improvement Plan

> Status: **PENDING APPROVAL**
> Date: 2026-03-01
> Scope: All 31 Filament resources, 43 models, 10 services, 39 policies, HRFilamentPlugin, HRServiceProvider

---

## Executive Summary

The HR module is **~90% production-ready**. Forms, tables, models, migrations, and the new feature areas are solid. The main gaps are:

1. **16 resources have no View page** (no infolist — users must edit to view details)
2. **Navigation icons** are generic `OutlinedRectangleStack` across ~20 resources
3. **Company-admin panel** is missing several configuration resources
4. **Services** (6 of 10) are not bound in the service provider
5. **Two form method naming bugs** in existing RelationManagers
6. **One model relationship name mismatch** in ViewEmployee

These are all fixable. Below is the full breakdown.

---

## 1. Navigation & Icons

### Current State

| Resource | Group | Sort | Current Icon | Correct Icon |
|---|---|---|---|---|
| EmployeeResource | Employees | — | `OutlinedRectangleStack` | `OutlinedUsers` |
| DepartmentResource | Structure | — | `OutlinedRectangleStack` | `OutlinedBuildingOffice2` |
| JobPositionResource | Structure | — | `OutlinedRectangleStack` | `OutlinedBriefcase` |
| LeaveRequestResource | Leave | — | `OutlinedRectangleStack` | `OutlinedCalendarDays` |
| LeaveTypeResource | Leave | — | `OutlinedRectangleStack` | `OutlinedClipboardDocument` |
| LeaveBalanceResource | Leave | — | `OutlinedRectangleStack` | `OutlinedChartBar` |
| LeaveApprovalWorkflowResource | Leave | — | `OutlinedRectangleStack` | `OutlinedArrowPath` |
| AttendanceRecordResource | Workforce | — | `OutlinedRectangleStack` | `OutlinedFingerPrint` |
| PerformanceReviewResource | Performance | — | `OutlinedRectangleStack` | `OutlinedStar` |
| PerformanceCycleResource | Performance | — | `OutlinedRectangleStack` | `OutlinedArrowPathRoundedSquare` |
| EmployeeSalaryResource | Payroll | — | `OutlinedRectangleStack` | `OutlinedBanknotes` |
| SalaryScaleResource | Payroll | — | `OutlinedRectangleStack` | `OutlinedScale` |
| DeductionTypeResource | Payroll | — | `OutlinedRectangleStack` | `OutlinedMinusCircle` |
| EmploymentContractResource | Documents | — | `OutlinedRectangleStack` | `OutlinedDocumentText` |
| EmployeeDocumentResource | Documents | — | `OutlinedRectangleStack` | `OutlinedDocument` |
| EmployeeCompanyAssignmentResource | Structure | — | `OutlinedRectangleStack` | `OutlinedArrowsRightLeft` |
| HostelStaffAssignmentResource | Structure | — | `OutlinedRectangleStack` | `OutlinedHome` |
| TrainingProgramResource | Learning & Development | 56 | `OutlinedRectangleStack` | `OutlinedAcademicCap` |
| CertificationResource | Learning & Development | 57 | `OutlinedRectangleStack` | `OutlinedBadgeCheck` |
| EmployeeGoalResource | Performance | 62 | `OutlinedRectangleStack` | `OutlinedFlag` |
| GrievanceCaseResource | Employee Relations | 61 | `OutlinedRectangleStack` | `OutlinedChatBubbleLeftRight` |

**Resources with correct icons (keep as-is):**
- PayrollRunResource — `OutlinedBanknotes` ✓
- ShiftResource — `OutlinedClock` ✓
- JobVacancyResource — `OutlinedBriefcase` ✓
- AnnouncementResource — `OutlinedMegaphone` ✓
- DisciplinaryCaseResource — `OutlinedExclamationTriangle` ✓
- AllowanceTypeResource — `OutlinedPlusCircle` ✓
- BenefitTypeResource — `OutlinedGift` ✓
- EmployeeLoanResource — `OutlinedCreditCard` ✓
- KpiDefinitionResource — `OutlinedPresentationChartLine` ✓
- PublicHolidayResource — `OutlinedCalendar` ✓

### Navigation Group Reorganisation Proposal

Current groups are inconsistent across old and new resources. Proposed unified structure:

| Group Name | Resources | Sort Range |
|---|---|---|
| **Core HR** | Employee, Department, JobPosition, SalaryScale, EmployeeCompanyAssignment | 10–19 |
| **Leave** | LeaveType, LeaveBalance, LeaveApprovalWorkflow, LeaveRequest | 20–29 |
| **Payroll** | PayrollRun, AllowanceType, DeductionType, EmployeeSalary | 50–53 |
| **Workforce** | Shift, PublicHoliday, AttendanceRecord | 54–56 |
| **Recruitment** | JobVacancy | 57 |
| **Learning & Development** | TrainingProgram, Certification | 58–59 |
| **Performance** | PerformanceCycle, PerformanceReview, EmployeeGoal, KpiDefinition | 60–63 |
| **Employee Relations** | DisciplinaryCase, GrievanceCase | 64–65 |
| **Benefits & Loans** | BenefitType, EmployeeLoan | 66–67 |
| **Documents** | EmploymentContract, EmployeeDocument | 68–69 |
| **HR Comms** | Announcement, HrAnalyticsDashboard | 70–71 |

---

## 2. Missing View Pages (Infolists)

16 resources have no dedicated View page. Users cannot read a record's details without entering edit mode.

### Resources Requiring View Pages + Infolist

#### Tier 1 — High Usage (Build First)

**AttendanceRecordResource**
- Sections: Record Overview (employee, date, clock-in/out, hours), Status & Notes
- Header actions: Edit, Mark Present, Mark Absent (if applicable)

**PerformanceReviewResource**
- Sections: Review Overview (employee, cycle, reviewer, period), Ratings & Scores, Comments & Feedback, Approval
- Header actions: Edit, Submit, Approve, Reject

**LeaveApprovalWorkflowResource**
- Sections: Workflow Settings, Approval Levels (relation manager table inline), Notes
- Header actions: Edit, Activate/Deactivate

**EmployeeSalaryResource**
- Sections: Salary Details (employee, basic, effective date), Components (allowances, deductions)
- Header actions: Edit

**LeaveBalanceResource**
- Sections: Balance Overview (employee, leave type, total, used, remaining), Accrual Info
- Header actions: Edit, Adjust Balance

#### Tier 2 — Configuration Resources

**DepartmentResource**
- Sections: Department Info (name, company, manager, head count), Employees (relation manager)
- Header actions: Edit

**JobPositionResource**
- Sections: Position Info (title, department, grade, salary range), Employees (relation manager)
- Header actions: Edit

**LeaveBalanceResource**
- Sections: Balance, Accrual Settings
- Header actions: Edit

**SalaryScaleResource**
- Sections: Scale Info, Grade Bands (relation manager if applicable)
- Header actions: Edit

**EmploymentContractResource**
- Sections: Contract Details (employee, type, start/end, salary), Terms, Status
- Header actions: Edit, Terminate

**EmployeeDocumentResource**
- Sections: Document Info (employee, type, upload date, expiry), File Preview link
- Header actions: Edit

#### Tier 3 — New Feature Resources (missing view)

| Resource | Key Sections | Status Actions |
|---|---|---|
| PublicHolidayResource | Holiday info, date, recurring | Edit |
| KpiDefinitionResource | KPI details, target, metric type | Edit |
| CertificationResource | Employee, cert details, expiry status badge | Edit |
| EmployeeGoalResource | Goal overview, progress bar, milestones | Edit, Start, Complete |
| GrievanceCaseResource | Case info, parties, investigation, resolution | Edit, Investigate, Resolve |
| EmployeeCompanyAssignmentResource | Assignment details, dates, role | Edit |
| HostelStaffAssignmentResource | Staff, hostel, dates, role | Edit |

---

## 3. HRFilamentPlugin — Company-Admin Panel Gaps

### Current company-admin registrations (17 resources):
```
EmployeeResource, EmployeeSalaryResource, EmploymentContractResource,
EmployeeDocumentResource, LeaveRequestResource, PerformanceReviewResource,
AttendanceRecordResource, EmployeeCompanyAssignmentResource,
HostelStaffAssignmentResource, PayrollRunResource, ShiftResource,
JobVacancyResource, TrainingProgramResource, CertificationResource,
DisciplinaryCaseResource, GrievanceCaseResource, EmployeeGoalResource,
EmployeeLoanResource, AnnouncementResource
```

### Missing from company-admin (should be visible to company managers):

| Resource | Reason to Include |
|---|---|
| DepartmentResource | Company managers need to view/manage their departments |
| JobPositionResource | Need to manage positions when hiring |
| LeaveTypeResource | Companies may customise leave types |
| LeaveBalanceResource | HR managers need to view/adjust balances |
| LeaveApprovalWorkflowResource | Companies configure their approval chains |
| AllowanceTypeResource | Payroll setup |
| DeductionTypeResource | Payroll setup |
| BenefitTypeResource | Benefits setup |
| KpiDefinitionResource | Companies define their own KPIs |
| PublicHolidayResource | Companies set their public holidays |
| SalaryScaleResource | Compensation setup |

---

## 4. Service Provider — Missing Singletons

**File:** `Modules/HR/app/Providers/HRServiceProvider.php`

### Currently registered:
- `CompanyAssignmentService` ✓
- `PayrollService` ✓
- `AnnouncementService` ✓

### Not registered (exist but rely on `new` or auto-resolution):
```php
LeaveAccrualService::class
LeaveApprovalService::class
LeaveAttachmentService::class
LeaveBalanceService::class
LeaveNotificationService::class
LeaveReportingService::class
HRService::class
```

**Impact:** These services still work via auto-resolution but won't be injected as singletons. Any state they accumulate won't persist across the request. Low risk but should be consistent.

---

## 5. Bug Fixes Required

### Bug 1 — DepartmentResource EmployeesRelationManager wrong method

**File:** `Modules/HR/app/Filament/Resources/DepartmentResource/RelationManagers/EmployeesRelationManager.php`

```php
// CURRENT (wrong)
public function schema(Schema $schema): Schema {
    return $schema->schema([...]);
}

// CORRECT
public function form(Schema $schema): Schema {
    return $schema->components([...]);
}
```

### Bug 2 — ViewEmployee wrong relationship name

**File:** `Modules/HR/app/Filament/Resources/EmployeeResource/Pages/ViewEmployee.php` (~line 198)

```php
// CURRENT (wrong — relationship doesn't exist)
TextEntry::make('reportingTo.full_name')

// CORRECT (relationship is defined as 'manager' in Employee model)
TextEntry::make('manager.full_name')
```

### Bug 3 — DepartmentResource & JobPositionResource missing 'view' route

```php
// Both resources have getPages() without 'view' key
// After View pages are created, add:
'view' => Pages\ViewDepartment::route('/{record}'),
```

---

## 6. Service Correctness Audit

### PayrollService
| Method | Status | Notes |
|---|---|---|
| `generatePayrollRun()` | WORKS | Creates run + all lines correctly |
| `calculateEmployeePayroll()` | WORKS | Uses correct salary lookup |
| `calculatePAYE()` | WORKS | Ghana 2024/25 monthly bands correct |
| `calculateSSNIT()` | WORKS | 5.5% employee / 13% employer |
| `computeAllowances()` | WORKS | Fixed + percentage both handled |
| `computeDeductions()` | WORKS | Same |
| `finalizePayrollRun()` | WORKS | Sets finalized_at + finalized_by |
| `postToFinance()` | STUB | TODO — Finance Journal Entry not yet wired |

### AnnouncementService
| Method | Status | Notes |
|---|---|---|
| `publish()` | WORKS | Correct |
| `unpublish()` | WORKS | Correct |
| `markAsRead()` | WORKS | firstOrCreate — idempotent |
| `getForEmployee()` | WORKS | Audience filtering correct |
| `getReadCount()` | WORKS | Correct |
| `sendViaCommunicationCentre()` | STUB | TODO — not wired to CommunicationCentre |

### LeaveApprovalService
| Method | Status | Notes |
|---|---|---|
| `initializeApprovalProcess()` | WORKS | DB transaction, creates approval request |
| `processApproval()` | WORKS | Handles approve/reject, escalation |
| `resolveApproverForLevel()` | WORKS | Delegation-aware |
| `getWorkflowForLeaveRequest()` | WORKS | Company + leave type matching |

### LeaveBalanceService
| Method | Status | Notes |
|---|---|---|
| `getBalance()` | WORKS | Correct DB lookup |
| `deductBalance()` | LIKELY OK | Needs integration test |
| `restoreBalance()` | LIKELY OK | Needs integration test |

### LeaveAccrualService
- Accrual logic exists — tied to `ProcessLeaveCarryOverCommand`
- Needs manual test run: `php artisan hr:process-leave-carryover`

### LeaveNotificationService
- Methods exist but integration with CommunicationCentre needs verification
- Template keys assumed to match what CommunicationCentre has seeded

---

## 7. Form Quality Audit

### Forms with no Section wrapping (flat components — should be sectioned)

| Resource | Fix Needed |
|---|---|
| AttendanceRecordResource | Wrap in sections: Record Info, Timing, Notes |
| PerformanceCycleResource | Wrap in sections: Cycle Info, Period, Status |
| HostelStaffAssignmentResource | Wrap in sections: Assignment Info, Dates |
| EmployeeCompanyAssignmentResource | Wrap in sections: Assignment Details, Role |
| SalaryScaleResource | Wrap in sections: Scale Info, Grade Bands |

### Forms that are solid (no change needed):
- EmployeeResource (7 sections — excellent)
- PayrollRunResource (3 sections)
- JobVacancyResource (4 sections with RichEditor)
- LeaveRequestResource (3 sections)
- DisciplinaryCaseResource (3 sections)
- EmployeeLoanResource (2 sections)
- AnnouncementResource (2 sections)
- ShiftResource (2 sections with CheckboxList)
- TrainingProgramResource (3 sections)

---

## 8. Table Quality Audit

### Issues found:

**PerformanceReviewResource table:**
- Missing `searchable()` on employee column
- No filter for status or cycle

**AttendanceRecordResource table:**
- No date range filter
- `hours_worked` not shown

**EmployeeSalaryResource table:**
- No `sortable()` on `effective_date`
- No filter by company

**LeaveBalanceResource table:**
- Missing `leave_type` filter
- No color coding on balance (e.g., red when 0)

### Tables that are solid:
- EmployeeResource (15+ columns, 4 filters, badges, image) ✓✓
- PayrollRunResource (money formatting, status badge, sort) ✓✓
- LeaveRequestResource (status badge, date columns, filters) ✓✓
- EmployeeLoanResource (outstanding balance color-coded) ✓✓
- JobVacancyResource (applicant count badge, status lifecycle) ✓✓
- DisciplinaryCaseResource (severity badge, status, dates) ✓✓

---

## 9. Proposed Fix Sequence (for approval)

### Phase A — Bugs & Quick Wins (< 1 hour)
1. Fix `EmployeesRelationManager` method name (`schema` → `form`, `.schema([])` → `.components([])`)
2. Fix `ViewEmployee` relationship (`reportingTo` → `manager`)
3. Add 7 missing services to HRServiceProvider singletons
4. Fix all 21 navigation icons to correct Heroicon values
5. Reorganise navigation groups + sort numbers across all resources

### Phase B — Company-Admin Panel (30 min)
6. Add 11 missing resources to company-admin list in HRFilamentPlugin
7. Add `'view'` route to DepartmentResource and JobPositionResource getPages()

### Phase C — View Pages (main build, ~6 hours)
8. Create View pages for all 16 missing resources (templated)
9. Each page: infolist with 2–4 collapsible sections, header EditAction + any status actions

### Phase D — Form Polish (1 hour)
10. Wrap flat forms in AttendanceRecordResource, PerformanceCycleResource, HostelStaffAssignmentResource, EmployeeCompanyAssignmentResource, SalaryScaleResource into Sections

### Phase E — Service Integration (2 hours, lower priority)
11. Wire `AnnouncementService::sendViaCommunicationCentre()` to CommunicationCentre
12. Wire `PayrollService::postToFinance()` to Finance module Journal Entry

---

## Approval Checklist

- [ ] **Phase A** — Approve bug fixes + icon/navigation changes
- [ ] **Phase B** — Approve plugin registration additions
- [ ] **Phase C** — Approve View page build for all 16 resources (or specify subset)
- [ ] **Phase D** — Approve form sectioning polish
- [ ] **Phase E** — Approve service integration work (CommunicationCentre + Finance)

> Reply with which phases to proceed with, or any adjustments to the plan.
