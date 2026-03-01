
# Finance Module — Audit Findings
**Date:** 2026-03-01
**Module Status:** 85–90% complete (UI/UX gaps + cross-module integration gaps)

---

## 1. What's Working Well

- Solid database schema (7 tables, proper unique constraints)
- All 7 models correctly defined with relationships
- All 7 Filament resources registered using Filament v4 Schema API
- All 7 policies implemented with Spatie permission integration
- Receipt model with full status lifecycle (draft → sent → viewed → downloaded)
- Event-driven integration architecture (Hostels, Payments, Farms, Construction, etc.)
- Double-entry bookkeeping logic in IntegrationService
- EnhancedIntegrationService covering all module integration stubs
- Booking → Invoice → Payment flow fully working for Hostels
- Refund path (negative Payment + reversal journals) implemented

---

## 2. UI / Filament Gaps

### 2.1 — Missing View Pages (ALL 7 resources)
No `ViewXxx.php` pages exist. Users cannot view record detail without entering edit mode.

| Resource | Missing Page |
|---|---|
| AccountResource | ViewAccount |
| InvoiceResource | ViewInvoice |
| InvoiceLineResource | ViewInvoiceLine |
| JournalEntryResource | ViewJournalEntry |
| JournalLineResource | ViewJournalLine |
| PaymentResource | ViewPayment |
| ReceiptResource | ViewReceipt |

### 2.2 — Missing Relation Managers
| Host Resource | Missing RelationManager |
|---|---|
| InvoiceResource | InvoiceLinesRelationManager (add/view line items inline) |
| InvoiceResource | PaymentsRelationManager (track payments inline) |
| JournalEntryResource | JournalLinesRelationManager (debit/credit lines inline) |

### 2.3 — Navigation Icons (All Generic)
| Resource | Current | Recommended |
|---|---|---|
| AccountResource | OutlinedRectangleStack | `OutlinedCalculator` |
| InvoiceResource | OutlinedRectangleStack | `OutlinedDocumentText` |
| InvoiceLineResource | OutlinedRectangleStack | `OutlinedListBullet` |
| JournalEntryResource | OutlinedRectangleStack | `OutlinedBookOpen` |
| JournalLineResource | OutlinedRectangleStack | `OutlinedQueueList` |
| PaymentResource | OutlinedRectangleStack | `OutlinedCreditCard` |
| ReceiptResource | OutlinedDocument | `OutlinedCheckBadge` |

### 2.4 — Navigation Groups
All 7 resources are in a single `'Finance'` group. Recommended split:
- **Billing:** Invoice, Payment, Receipt
- **General Ledger:** Account, JournalEntry, JournalLine
- InvoiceLine: hide from nav (access only via Invoice relation manager)

### 2.5 — No Form Sectioning
All forms are flat. Recommended sections:
- **InvoiceResource:** `Customer Info` | `Invoice Details` | `Module Reference` | `Amounts`
- **ReceiptResource:** `Customer Info` | `Receipt Details` | `Status Tracking`
- **AccountResource:** `Account Details` | `Chart Hierarchy`
- **JournalEntryResource:** `Entry Details` (simple — relation manager handles lines)
- **PaymentResource:** `Payment Details` | `Reference`

### 2.6 — Plugin Has No Panel Split
`FinanceFilamentPlugin` registers all resources for all panels with no admin vs company-admin distinction. No custom pages or dashboard registered.

---

## 3. Finance Feature Gaps (Internal)

### 3.1 — Receipt Email Integration Broken
`ReceiptController::sendEmail()` calls `CommunicationService::sendToContact()` with template `'payment_receipt'`.
- Template does **not exist** in CommunicationCentre database — call will fail
- No validation that `customer_email` is non-null before sending
- No success/failure logging or exception handling

### 3.2 — No Invoice Sending Feature
Invoices are created but there is no "Send Invoice" action to email them to the customer.

### 3.3 — PDF Export Not Implemented
`ReceiptController::download()` returns raw HTML with comment: "For future: use DomPDF or similar". No real PDF.

### 3.4 — No Overdue Status Logic
Invoice status options include `overdue` but no automated detection or transition exists. No scheduled command or observer to detect `due_date < today` and update status.

### 3.5 — No Vendor / Accounts Payable Invoice
Only AR (Customer Invoices) exist. There is no "Vendor Invoice" or "Bill" model for AP management. ProcurementInventory, Fleet, and HR payroll all need AP-side Finance records.

### 3.6 — No Expense Tracking
All expenses must be manually journaled. No expense categories, expense records, or automated expense posting from HR / Fleet / Procurement.

### 3.7 — No Tax Management
Invoice has `tax_total` column but:
- No tax rate tables
- No breakdown by tax type (VAT, NHIL, GETF, etc.)
- No Tax Payable GL account tracking
- No withholding tax handling (payroll deductions, supplier payments)

### 3.8 — No Cost Centre / Department Allocation
All expenses post to a single account. No departmental P&L, no project-level cost tracking, no allocation of payroll by department.

### 3.9 — No Payment Method → GL Account Mapping
`Payment.payment_method` is a free-text string (e.g., `'momo'`, `'bank'`). No FK to `PayMethod` and no mapping to which GL cash/bank account to debit on payment received.

### 3.10 — Chart of Accounts Seeder Empty
`FinanceDatabaseSeeder` is empty. No default COA per company. Manual account creation required before any journals can post.

### 3.11 — No Period / Month-End Closing
No closing checklist, no accruals/deferrals handling, no period lock to prevent edits to past periods.

### 3.12 — No Financial Reporting
6 Livewire report components exist (`FinancialReports`, `ChartOfAccounts`, etc.) but are unimplemented stubs. No Trial Balance, P&L, Balance Sheet, Cash Flow, AR Aging, or AP Aging.

### 3.13 — No Audit Trail on Financial Records
No change logs, no who/when history, no approval workflow for manual journal entries.

---

## 4. Cross-Module Integration Analysis

### 4.1 — Hostels — 80% Integrated

**Working:**
- `BookingPaymentCompleted` → `CreateInvoiceForBooking` listener creates Invoice + InvoiceLine + Journal entries (DR AR, CR Revenue)
- `PaymentSucceeded` → `RecordPaymentOnSuccess` creates Payment + Journal entries (DR Bank, CR AR)
- Refund path: `Booking::processRefund()` → `processBookingRefund()` → negative Payment + reversal journals
- `Deposit.invoice_id` FK exists and is used

**Missing:**
- `BookingConfirmed` event not listened — Finance cannot update invoice status when booking is confirmed
- `HostelCharge` (utility/maintenance charges) has `invoice_id` FK but is never populated — charges don't appear on invoices
- Billing rules have `gl_account_code` column that Finance never reads or maps to accounts
- When booking is cancelled, invoice is not automatically set to `cancelled` status
- No credit note mechanism for partial refunds

**Events to Add Finance Listener For:**
| Event | Required Action |
|---|---|
| `BookingConfirmed` | Update Invoice status to `sent` / `confirmed` |
| `BookingCancelled` | Cancel Invoice, create credit note |

---

### 4.2 — PaymentsChannel — 75% Integrated

**Working:**
- `PaymentSucceeded` → `RecordPaymentOnSuccess` listener creates Payment + journals
- Metadata `invoice_id` set by Hostels listener so payment can find the invoice

**Missing:**
- `PaymentFailed` event exists but Finance doesn't listen — cannot mark invoice as `payment_failed`
- No `PaymentRefunded` event — gateway refunds processed at provider level but Finance gets no notification
- `Payment.payment_method` is a string, not FK to `PayMethod` — cannot trace which provider/gateway processed it
- No daily reconciliation: no comparison of gateway payout totals vs Finance Payment records
- No suspense account for payments awaiting settlement (e.g., mobile money T+1 settlement)

**Events to Add Finance Listener For:**
| Event | Required Action |
|---|---|
| `PaymentFailed` | Update Invoice to note failed payment attempt |
| `[PaymentRefunded]` (new event needed) | Create reversal Payment record in Finance |

**New Column Needed:**
- `payments.pay_method_id` FK → `pay_methods.id` (to track gateway used)

---

### 4.3 — HR Module — 0% Integrated

**Current State:**
- `PayrollRun` model: has `total_gross`, `total_deductions`, `total_net`, `status`
- `PayrollLine` model: per-employee breakdown with `gross_salary`, `deductions`, `net_salary`
- `Employee` model: has `bank_account_no`, `bank_name`, `bank_sort_code`
- `RecordPayrollExpense` listener exists in Finance but is commented out
- No `PayrollFinalized` or `PayrollProcessed` event defined in HR

**Required Integrations:**
1. **Payroll → General Ledger Posting**
   - On `PayrollRun` finalized: create JournalEntry with:
     - DR Salary Expense (total_gross)
     - CR Bank / Cash (total_net)
     - CR Tax Payable (income tax deductions)
     - CR Pension Payable (pension deductions)
   - Link JournalEntry back to PayrollRun for audit

2. **Employee Loan Disbursement → AP Invoice**
   - When loan disbursed: create Invoice (AP-side) for employee
   - Monthly repayment deductions → create Payment records against loan invoice

3. **Department Cost Allocation**
   - Payroll expense split by `employee.department_id` → separate GL entries per department

**New Events Needed in HR:**
| Event | Trigger | Finance Action |
|---|---|---|
| `PayrollFinalized` | PayrollRun status → `paid` | Create salary expense journal entries |
| `LoanDisbursed` | EmployeeLoan created + approved | Create liability AP record |
| `LoanRepaymentProcessed` | Monthly payroll deduction | Create Payment against loan |

---

### 4.4 — ProcurementInventory — 5% Integrated

**Current State:**
- `PurchaseOrder` model has `finance_invoice_id` FK column → never populated
- `GoodsReceipt` model exists with `status` (partial/full)
- `RecordPurchaseOrderExpense` listener exists in Finance but commented out
- No events defined in ProcurementInventory

**Required Integrations:**
1. **PO Approval → AP Invoice**
   - On PO status → `approved`: create Finance "Vendor Invoice" (DR Inventory/Expense, CR Accounts Payable)
   - Store invoice ID in `purchase_orders.finance_invoice_id`

2. **Goods Receipt → Inventory GL**
   - On GoodsReceipt created: post to inventory account (DR Inventory Asset, CR GR/IR suspense)
   - On Vendor Invoice matched: clear GR/IR suspense (DR GR/IR, CR Accounts Payable)

3. **3-Way Match**
   - Match PO quantity/price → GoodsReceipt quantity → Vendor Invoice amount
   - Only release AP payment after 3-way match

**New Events Needed in Procurement:**
| Event | Trigger | Finance Action |
|---|---|---|
| `PurchaseOrderApproved` | PO status → `approved` | Create AP invoice, populate `finance_invoice_id` |
| `GoodsReceived` | GoodsReceipt created | Post to inventory GL, update AP match status |
| `VendorInvoiceReceived` | Manual entry or OCR | Create liability, trigger 3-way match |

**New Finance Model Needed:**
- `VendorInvoice` (or extend `Invoice` with `type` enum: `customer` / `vendor`)
  - Fields: company_id, vendor_id, po_id, amount, status, due_date, gl_account_id

---

### 4.5 — Core Module — 10% Integrated

**Working:**
- `PaymentCompleted` event → `ProcessUnifiedPayment` listener handles generic payment via module/entity lookup

**Missing:**
- `PaymentFailed` event exists but Finance doesn't listen
- Core `AutomationService` doesn't trigger any Finance automations (recurring invoices, payment reminders, GL closures)
- No Finance dashboard page in Core's admin panel

**Potential Automation Triggers:**
| Automation | Trigger | Finance Action |
|---|---|---|
| Payment Reminder | Invoice due_date - 3 days | Send reminder via CommunicationCentre |
| Overdue Detection | Daily: invoice due_date < today, status != paid | Update status to `overdue` |
| Recurring Invoice | Monthly/semester cycle | Clone invoice template, send to customer |

---

### 4.6 — CommunicationCentre — 40% Integrated

**Working:**
- `ReceiptController::sendEmail()` calls `CommunicationService::sendToContact()` — code path exists

**Missing:**
- `payment_receipt` template does **not exist** in the CommunicationCentre templates table — all receipt emails currently fail
- No `invoice_sent` template for sending invoices to customers
- No `payment_reminder` template for overdue invoices
- No SMS/WhatsApp payment notifications (channel exists, not used by Finance)

**Templates to Seed in CommunicationCentre:**
| Template Code | Channel | Variables | Used By |
|---|---|---|---|
| `payment_receipt` | email | `name`, `booking_reference`, `amount`, `date`, `receipt_number` | ReceiptController::sendEmail() |
| `invoice_sent` | email | `name`, `invoice_number`, `due_date`, `amount`, `company_name` | InvoiceResource "Send" action |
| `payment_reminder` | email + sms | `name`, `invoice_number`, `due_date`, `amount`, `days_overdue` | Overdue automation |
| `payment_confirmed` | sms | `name`, `amount`, `reference` | On PaymentSucceeded |

---

### 4.7 — Construction / Farms / Fleet / Manufacturing — 0% Integrated

All stub listeners exist in Finance (`CreateInvoiceForProject`, `CreateInvoiceForFarmSale`, `RecordFleetExpenses`, `CreateInvoiceForBatch`) but:
- No models exist in these modules yet (stub stage)
- No events defined or fired
- Listeners commented out in EventServiceProvider

**When modules are built, Finance needs:**
| Module | Event | Finance Action |
|---|---|---|
| Construction | `ProjectMilestoneCompleted` | Create AR invoice for milestone billing |
| Construction | `MaterialPurchased` | Create AP invoice for materials |
| Farms | `HarvestSaleRecorded` | Create AR invoice for farm sale |
| Farms | `InputPurchased` | Create AP expense for farm input |
| Fleet | `FuelLogCreated` | DR Fuel Expense, CR Bank |
| Fleet | `MaintenanceCompleted` | DR Maintenance Expense, CR AP |
| Manufacturing | `BatchCompleted` | Post finished goods to inventory GL |
| Manufacturing | `RawMaterialConsumed` | DR WIP, CR Inventory |

---

## 5. All Events Across Codebase — Finance Listener Status

| Event | Source Module | Finance Listens? | Action If Listening |
|---|---|---|---|
| `BookingPaymentCompleted` | Hostels | ✓ Yes | Create invoice + payment + journals |
| `BookingConfirmed` | Hostels | ✗ No | Update invoice status |
| `BookingCancelled` | Hostels | ✗ No | Cancel invoice, create credit note |
| `PaymentSucceeded` | PaymentsChannel | ✓ Yes | Create payment record + journals |
| `PaymentFailed` | PaymentsChannel | ✗ No | Flag invoice payment attempt as failed |
| `PaymentCompleted` | Core | ✓ Yes | Generic payment processor |
| `[PaymentRefunded]` | PaymentsChannel | ✗ No event | Create reversal payment record |
| `[PayrollFinalized]` | HR | ✗ No event | Create salary expense journals |
| `[LoanDisbursed]` | HR | ✗ No event | Create AP/liability record |
| `[PurchaseOrderApproved]` | Procurement | ✗ No event | Create AP vendor invoice |
| `[GoodsReceived]` | Procurement | ✗ No event | Post to inventory GL |
| `[ProjectMilestoneCompleted]` | Construction | ✗ No event | Create AR invoice |
| `[FuelLogCreated]` | Fleet | ✗ No event | Post fuel expense |
| `[MaintenanceCompleted]` | Fleet | ✗ No event | Post maintenance expense |
| `[HarvestSaleRecorded]` | Farms | ✗ No event | Create AR invoice |
| `[BatchCompleted]` | Manufacturing | ✗ No event | Post inventory GL |

_(Events in brackets `[ ]` do not yet exist and must be created in their source module)_

---

## 6. Missing Finance Features for Full ERP

### 6.1 — Critical Missing (Blockers)
| Feature | Why Needed | Effort |
|---|---|---|
| Vendor Invoice / AP model | Procurement, HR loans, Fleet expenses all need AP-side invoices | Medium |
| Chart of Accounts seeder | No journals can post without default accounts | Small |
| `payment_receipt` template seed | Receipt emails fail without it | Small |
| `pay_method_id` FK on Payment | Cannot trace gateway used | Small |
| PDF export for receipts | Professional receipt delivery | Medium |

### 6.2 — High Priority
| Feature | Why Needed | Effort |
|---|---|---|
| Tax rate table + Tax Payable GL | VAT, NHIL, GETF, withholding tax compliance | Medium |
| Overdue invoice detection command | Scheduled status transition | Small |
| `PayrollFinalized` event + listener | HR payroll posts to GL | Medium |
| Invoice sending (email + PDF) | Core AR workflow | Medium |
| InvoiceLine & Payment RelationManagers | Usability — see everything on one page | Small |
| JournalLine RelationManager | Usability — enter debits/credits inline | Small |

### 6.3 — Medium Priority
| Feature | Why Needed | Effort |
|---|---|---|
| Cost centre / department allocation | Departmental P&L reporting | Medium |
| Period / month-end close | Accounting controls | Medium |
| Trial Balance report | Minimum viable financial statement | Medium |
| Income Statement (P&L) | Core business report | Large |
| Balance Sheet | Core business report | Large |
| AR Aging report | Credit control | Medium |
| AP Aging report | Supplier management | Medium |
| Bank reconciliation | Cash management | Large |

### 6.4 — Low Priority / Post-MVP
| Feature | Why Needed | Effort |
|---|---|---|
| Multi-currency support | International clients | Large |
| Recurring invoices | Semester fees, subscriptions | Medium |
| Fixed asset register + depreciation | Asset management | Large |
| Budget vs Actual tracking | Financial planning | Large |
| Audit trail / change logs | Compliance | Medium |
| Cash Flow statement | Financial management | Large |

---

## 7. Full Implementation Plan

### Phase 1 — Filament UI (CRITICAL — implement first)
1. Create View pages for all 7 resources with sectioned infolists
2. Update `getPages()` and add `ViewAction` to all table action groups
3. Fix navigation icons and split into `Billing` vs `General Ledger` groups
4. Add form sections to all resources
5. Add `InvoiceLinesRelationManager` and `PaymentsRelationManager` to InvoiceResource
6. Add `JournalLinesRelationManager` to JournalEntryResource
7. Update FinanceFilamentPlugin for admin vs company-admin panel split + add pages

### Phase 2 — Quick Integration Fixes (HIGH — fixes existing broken paths)
1. Seed `payment_receipt` template in CommunicationCentre
2. Seed `invoice_sent`, `payment_reminder`, `payment_confirmed` templates
3. Fix `ReceiptController::sendEmail()` — validate template + email, add logging
4. Add PDF export to receipts (laravel-dompdf or barryvdh/laravel-snappy)
5. Add Chart of Accounts seeder (5 standard account types, ~20 default accounts)
6. Add `pay_method_id` FK migration to payments table
7. Add overdue detection: `php artisan finance:mark-overdue` scheduled command

### Phase 3 — Complete Hostels Integration
1. Listen to `BookingConfirmed` → update invoice status to `sent`
2. Listen to `BookingCancelled` → cancel invoice + create credit note journal
3. Populate `hostel_utility_charges.invoice_id` when charges are created
4. Map `billing_rule.gl_account_code` → Finance Account lookup

### Phase 4 — HR Payroll Integration
1. Add `PayrollFinalized` event to HR module (fired when PayrollRun status → `paid`)
2. Uncomment + implement `RecordPayrollExpense` listener in Finance
3. Create salary expense JournalEntry (DR Salary Expense, CR Bank, CR Tax Payable, CR Pension Payable)
4. Add `LoanDisbursed` event and Finance AP listener
5. Seed standard payroll GL accounts (Salary Expense, Tax Payable, Pension Payable)

### Phase 5 — Procurement AP Integration
1. Add `VendorInvoice` concept (extend Invoice with `type` enum or new model)
2. Add `PurchaseOrderApproved` event to ProcurementInventory
3. Implement `RecordPurchaseOrderExpense` listener — create AP invoice, update `finance_invoice_id`
4. Add `GoodsReceived` event and inventory GL posting listener
5. Implement 3-way match validation before AP payment release

### Phase 6 — Financial Reporting
1. Trial Balance (accounts with running debit/credit totals)
2. Income Statement (Revenue accounts vs Expense accounts for period)
3. Balance Sheet (Assets, Liabilities, Equity snapshot)
4. AR Aging (invoices grouped by days overdue: 0-30, 31-60, 61-90, 90+)
5. AP Aging (vendor invoices grouped similarly)
6. Cash Flow statement
7. Finance Dashboard Filament page (key KPIs: cash position, AR total, AP total, revenue MTD)

### Phase 7 — Advanced Features (Post-MVP)
1. Tax management (rate tables, tax return summaries)
2. Cost centre / department allocation
3. Period close + journal entry lock
4. Multi-currency with FX rates
5. Bank reconciliation module
6. Recurring invoice engine
7. Fixed asset register + depreciation

---

## 8. Resources — Current getPages()

All 7 resources currently have only: `List`, `Create`, `Edit` — none have `View`.

---

## 9. File Reference

| Path | Purpose |
|---|---|
| `app/Filament/FinanceFilamentPlugin.php` | Plugin registration |
| `app/Filament/Resources/` | 7 resources |
| `app/Models/` | Account, Invoice, InvoiceLine, Payment, Receipt, JournalEntry, JournalLine |
| `app/Services/IntegrationService.php` | Hostel/Payment integration |
| `app/Services/EnhancedIntegrationService.php` | All-module integration stubs |
| `app/Http/Controllers/ReceiptController.php` | Receipt show/download/email |
| `app/Listeners/` | 8 event listeners (6 commented out) |
| `database/migrations/` | 8 migrations |
| `app/Providers/EventServiceProvider.php` | Event → Listener mappings |
