# HR Module — User Training Guide

**KharisERP | Human Resources**

---

## Overview

The HR module covers the full employee lifecycle — from recruitment and onboarding through daily operations (leave, attendance, payroll, performance) to separation. It has two interfaces:

- **Admin Panel** (`/company-admin`) — HR Managers, HR Staff, and Admins manage all data
- **Employee Portal** (`/hr`) — Employees view their own records, submit requests, and check leave balances

---

## Who Uses What

| Role | Panel | What they can do |
|---|---|---|
| HR Manager | Admin panel | Full CRUD on all HR records, approve leaves, process payroll, manage vacancies |
| HR Staff | Admin panel | Add/edit employees, record attendance, manage training nominations |
| Department Head | Admin panel | View own department employees, approve leaves for their team |
| Employee | Employee portal `/hr` | View own profile, salaries, contracts, submit leave, view performance |

---

## 1. Employee Management

### Creating an Employee

Go to **HR Records > Employees > New Employee**.

Fill in the sections in order:

**Basic Information**
- **Employee Code** — Leave blank to auto-generate (`#EMP000001`). Enter a custom code if your company uses a specific format (e.g. `GH-ADM-001`).
- **Photo** — Upload a portrait photo (max 10 MB).
- First Name, Last Name, Gender, Date of Birth are required.
- **National ID** — Select the ID type first, then the number and front/back photos appear.
- **Marital Status** — Once selected, the Next of Kin section appears. Add at least one next of kin (name, relationship, phone).

**Contact Information**
- Residential Address, GhanaPost GPS, Phone numbers, WhatsApp, Email.
- Emergency Contact — a separate emergency name and phone.

**Bank Account Information**
- All bank fields are optional. Fill them in when the employee has confirmed their bank details. Bank Branch and Sort Code are needed for payroll direct transfers.

**Employment Information**
- **Company** — Defaults to the current tenant company. Can assign to a subsidiary if your account is HQ.
- **Hire Date** — The official start date.
- **Employment Type** — Full Time / Part Time / Contract / Intern.
- **Employment Status** — Active / Probation / Suspended / Terminated / Resigned.
- **Department** — Filtered by the selected company.
- **Job Position** — Filtered by the selected company.
- **Reporting To** — The employee's direct manager. Filtered by company.

Click **Save** — the employee code is auto-generated if left blank.

---

### Employee Tabs (after creation)

Open any employee record to see these tabs:

| Tab | What it shows |
|---|---|
| Company Assignments | All companies this employee is deployed to |
| Employment Contracts | All contracts with dates, salary, probation |
| Salaries | Salary history, current basic, scale |
| Leave Requests | All leave requests submitted by this employee |
| Attendance Records | Daily clock-in/out records |
| Documents | Uploaded documents (offer letter, certificates, etc.) |
| Performance Reviews | Reviews given and received |
| Direct Reports | Employees who report to this person |
| Hostel Assignments | If deployed as hostel staff |

---

### Direct Reports Tab

This tab shows employees who report to the current employee (their manager).

To add a direct report:
1. Click **Add Direct Report**
2. A search box appears — type a name or employee code
3. Select the employee from the list
4. The selected employee's `Reporting To` field is automatically updated

To remove a direct report:
- Click **Remove** on the row — this unlinks the reporting relationship only. The employee record is not deleted.

> Note: The same link can be made from the other direction — on the subordinate's form, set their "Reporting To" field to this manager.

---

### Company Assignment Logic

**Primary company** (`company_id` on the employee record) is where the employee is primarily managed and where their permissions/roles are scoped.

**Additional assignments** (Company Assignments tab) track deployments to other companies or subsidiaries.

How it works:
- An employee is created under their **home company** (e.g. subsidiary company A).
- If they need to be shared with or temporarily deployed to another company (e.g. company B), use the **Company Assignments tab** to add that assignment with a start date, role, and optional end date.
- HQ/Group companies can see all subsidiary employees by default.
- You do NOT need to create employees in HQ first. Each employee belongs to one primary company and can be assigned to others as needed.

Example:
- John is hired by "Accra Branch" → create him there (company_id = Accra Branch).
- John is temporarily deployed to "Kumasi Branch" for 3 months → add a Company Assignment from John's Company Assignments tab pointing to Kumasi Branch with start/end dates.

---

## 2. Departments & Job Positions

### Departments

Go to **HR Setup > Departments**.

- Create a department per company (the company selector filters the list).
- Departments can be nested — set a **Parent Department** to create a hierarchy (e.g. Finance > Accounts Payable).
- Each department can have a code (e.g. `FIN`, `OPS`).

### Job Positions

Go to **HR Setup > Job Positions**.

- A Job Position belongs to a Department and a Company.
- Set a Grade (e.g. Grade 5, Level 3) to link with Salary Scales.
- Mark inactive positions with `is_active = false` so they no longer appear in the employee form dropdowns.

---

## 3. Leave Management

### Leave Types

Go to **HR Setup > Leave Types** to configure what types of leave are available.

Key settings per leave type:
- **Max Days Per Year** — Maximum days an employee can take.
- **Requires Approval** — If on, the leave goes through an approval workflow.
- **Is Paid** — Whether salary is paid during this leave.
- **Has Accrual** — If on, leave balance builds up over time (e.g. 1.5 days per month).
- **Carry Over Limit** — How many unused days can roll over to the next year.

### Submitting a Leave Request (Admin)

Go to **HR Records > Leave Requests > New Leave Request**.

- Select the employee, leave type, dates.
- System calculates the number of days automatically (weekends/public holidays may be excluded based on configuration).
- If the leave type requires approval, the request status starts as `pending`.

### Approving Leave Requests

For leave types with approval configured:
1. The approver sees the pending leave in their Leave Requests list.
2. Click the leave → click **Approve** or **Reject** (with a reason).
3. On approval, the employee's leave balance is automatically deducted.
4. If rejected, the balance is not deducted.

### Leave Approval Workflows

Go to **HR Setup > Leave Approval Workflows** to set multi-level approval chains.

Each workflow has levels (Level 1 = first approver, Level 2 = second approver, etc.). Each level specifies:
- **Approver Type**: Manager (auto-detects the employee's manager), Specific Employee, Department Head, or HR Role.
- **Is Required**: If unchecked, the level can be skipped.

### Leave Balances

Go to **HR Records > Leave Balances**.

- Each employee gets a balance record per leave type per year.
- Balances auto-update when leaves are approved or cancelled.
- Year-end carry-over runs automatically via a scheduled job every January 1 at 00:30.

### Leave Calendar

The **Team Leave Calendar** (Employee Portal: `/hr/leave-calendar`) shows a calendar of all approved leave in the team. HR Managers can see all employees; regular employees see their department.

---

## 4. Attendance

### Recording Attendance

Go to **HR Records > Attendance Records > New**.

- Select the employee, date, status (present/absent/late/on_leave).
- Optionally record check-in and check-out times with notes.

### Viewing Attendance (Employee Portal)

Employees go to `/hr/attendance` to see their own attendance history.

---

## 5. Payroll

### Setup Required Before Payroll

Before running payroll you need:
1. **Allowance Types** (HR Setup > Allowance Types) — e.g. Transport Allowance (fixed GHS 300), Housing (10% of basic).
2. **Deduction Types** (HR Setup > Deduction Types) — e.g. Loan deduction, Voluntary pension.
3. **Salary Scales** (HR Records > Salary Scales) — salary bands linked to job grades.
4. **Employee Salaries** — each active employee must have a current salary record (see the Salaries tab on the employee record or HR Records > Employee Salaries).
5. **Finance GL accounts** with codes `5210`, `5220`, `2120`, `2140`, `2150` must exist in the Finance module for payroll journal posting.

### Running Payroll

1. Go to **HR Records > Payroll Runs > New**.
2. Select the company, year, month.
3. Click **Process** — the system calculates payroll lines for all active employees using their current salary, allowances, and deductions. PAYE (Ghana tax brackets) and SSNIT (13.5% employer + 5.5% employee) are calculated automatically.
4. Review the generated lines in the **Payroll Lines** tab. You can see each employee's gross, deductions, PAYE, SSNIT, and net pay.
5. Click **Finalize** — locks the payroll. No further edits.
6. Click **Mark as Paid** — records the payment date.
7. After finalization, use **Post to Finance** to create the journal entry in the Finance module.

### Employee Loans

Go to **HR Records > Employee Loans** to record salary advances or personal loans.

- Set the principal, monthly deduction, number of repayment months.
- Approve the loan via the **Approve** action.
- Once approved and active, the monthly deduction is automatically included in payroll calculations.

---

## 6. Performance Management

### Setting Up Performance Cycles

Go to **HR Performance > Performance Cycles**.

- Create a cycle (e.g. "Annual Review 2025") with a start and end date.
- Once the cycle is `active`, reviews can be added.

### Performance Reviews

Go to **HR Performance > Performance Reviews**.

- Each review links an employee being reviewed, a reviewer (another employee or their manager), and a cycle.
- Add a rating (1-5) and comments.

### Employee Goals

Go to **HR Performance > Employee Goals**.

- Goals are linked to an employee and a performance cycle.
- Set title, description, target value, actual value, unit, due date, priority, and status.
- The completion percentage is computed automatically from target vs. actual.

### KPI Definitions

Go to **HR Performance > KPI Definitions**.

- Define measurable KPIs per department, job position, or company-wide.
- Set frequency (daily, weekly, monthly, quarterly, annually) and target values.
- These are used as reference when doing performance reviews.

---

## 7. Recruitment

### Creating a Job Vacancy

Go to **HR Recruitment > Job Vacancies > New**.

- Fill in department, job position, employment type, salary range, description, and requirements.
- Set status to **Open** and optionally a closing date.
- The posted date defaults to today.

### Managing Applicants

In the **Applicants** tab of a vacancy (or HR Recruitment > Applicants):

- Each applicant has a status pipeline: Applied → Shortlisted → Interview Scheduled → Interviewed → Offered → Hired / Rejected.
- Upload resume and cover letter per applicant.
- Create interview records from the applicant's Interviews tab.

### Applicant Kanban Board

Go to **HR Recruitment > Applicant Pipeline** for a Kanban view of all applicants across vacancies, sorted by status column. Drag cards to move applicants through stages.

---

## 8. Training & Development

### Training Programs

Go to **HR Learning > Training Programs**.

- Create a program with type (Internal/External/Online/Conference), provider, dates, cost, and max participants.
- Statuses: Planned → Ongoing → Completed / Cancelled.

### Nominations

In the **Nominations** tab of a training program (or HR Learning > Training Nominations):

- Add employees to the training.
- Update their status from Nominated → Confirmed → Attended → Completed.
- Record their score and completion date.

### Certifications

Go to **HR Learning > Certifications**.

- Record professional certificates per employee with issue date, expiry, and the certificate file.
- The system flags certificates that are expired or expiring soon.

---

## 9. Workplace Relations

### Disciplinary Cases

Go to **HR Relations > Disciplinary Cases**.

- Record the incident date, type (Verbal Warning, Written Warning, Final Warning, Suspension, Termination), action taken, and status.
- Handled By = the HR employee managing the case.
- Resolution workflow: Open → Under Review → Resolved/Appealed → Closed.

### Grievance Cases

Go to **HR Relations > Grievance Cases**.

- An employee can file a grievance (anonymously if needed).
- Types cover workplace misconduct, pay disputes, etc.
- Assign a case handler (HR employee) and track through: Filed → Under Investigation → Hearing Scheduled → Resolved → Closed / Escalated.

---

## 10. Announcements

Go to **HR Setup > Announcements** (or Announcements in the navigation).

- Create an announcement with priority level (Low / Normal / High / Urgent).
- Target all employees, a specific department, or a specific job position.
- Set published/expiry dates.
- The **Send Email** and **Send SMS** toggles (when CommunicationCentre integration is active) will dispatch the announcement via email/SMS.

---

## 11. Shifts

### Creating Shifts

Go to **HR Setup > Shifts**.

- Define a shift name (e.g. "Morning Shift"), start/end times, days of the week it applies, and break duration.

### Shift Assignments

In the **Shift Assignments** tab (or HR Records > Shift Assignments):

- Assign an employee to a shift with an effective date range.
- An employee can have only one active shift assignment at a time.

---

## 12. Public Holidays

Go to **HR Setup > Public Holidays**.

- Add the official public holidays for the year.
- Mark recurring holidays (e.g. Christmas, New Year) to avoid re-entering each year.
- Leave calculations use this list to exclude holidays from counted days.

---

## 13. Employee Portal (`/hr`)

Employees log in at `/hr` with their user account credentials.

| Section | What employee sees |
|---|---|
| Dashboard | Summary: leave balance, upcoming reviews, recent announcements |
| My Profile | Personal details, bank info, emergency contact |
| My Contracts | Employment contract dates, salary |
| My Salaries | Current and historical salary records |
| My Leave | Current balances, submitted requests, submit new leave |
| My Attendance | Clock-in/out history |
| My Performance | Reviews where they are the subject or reviewer |
| Salary Scales | The scale attached to their position |
| Leave Calendar | Team leave calendar |
| Leave Reports | Downloadable leave summary |
| Org Chart | Company hierarchy |

Employees can view but **cannot edit** most data — all updates go through an HR admin.

---

## Incomplete / Partially Implemented Features

The following are identified for completion. Approval needed before work begins.

| # | Feature | Current State | What's needed |
|---|---|---|---|
| 1 | **Announcement Email/SMS dispatch** | `AnnouncementService::sendAnnouncement()` is a stub — CommunicationCentre is not wired | Wire `CommunicationService::sendFromTemplate()` for email and SMS per announcement target audience |
| 2 | **Payroll Process action** | Clicking "Process" only sets status to `processing` — it does NOT call `PayrollService::generatePayrollRun()` to actually create payroll lines | The action should call `app(PayrollService::class)->generatePayrollRun($companyId, $year, $month)` and redirect to the payroll lines tab |
| 3 | **Leave portal — submit leave (employee)** | `/hr/leave-requests` view exists but the **Submit Leave Request** form from the portal has not been confirmed functional end-to-end | Test and wire portal leave submission to `LeaveApprovalService::initializeApprovalProcess()` |
| 4 | **Goal progress tracking** | `EmployeeGoal` has `actual_value` but there is no portal page for employees to update their own goal progress | Add a portal page `/hr/goals` for employees to see and update their goal progress |
| 5 | **Benefits (EmployeeBenefit)** | `EmployeeBenefit` model and `BenefitType` resource exist but there is no employee-facing portal page and no payroll deduction hook for employee contribution | Wire employee contribution deduction into `PayrollService::calculateEmployeePayroll()` |
| 6 | **KPI tracking vs. reviews** | `KpiDefinition` exists but there's no UI to record actual KPI values against targets per cycle | Add KPI score entry in PerformanceReview flow or a separate KPI tracking resource |
| 7 | **Org Chart** | Portal route `/hr/org-chart` is registered and the Livewire component likely renders a list, but a visual tree/chart view is not confirmed | Verify or implement a visual org chart using D3.js or a pure HTML/CSS tree |

To proceed with any of the above, confirm which items to prioritise.

---

## Quick Reference: Navigation Clusters

| Cluster | Contains |
|---|---|
| HR Records | Employees, Leave Requests, Leave Balances, Attendance, Payroll Runs, Employee Loans, Employee Salaries, Employment Contracts, Employee Documents, Shift Assignments, Company Assignments |
| HR Recruitment | Job Vacancies, Applicants, Applicant Pipeline (Kanban), Interviews |
| HR Setup | Departments, Job Positions, Leave Types, Approval Workflows, Shifts, Public Holidays, Allowance Types, Deduction Types, Salary Scales, Announcements, Benefit Types |
| HR Learning | Training Programs, Training Nominations, Certifications |
| HR Performance | Performance Cycles, Performance Reviews, Employee Goals, KPI Definitions |
| HR Relations | Disciplinary Cases, Grievance Cases |
