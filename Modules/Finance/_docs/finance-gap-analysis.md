
# KharisERP Finance Module — Gap Analysis vs. World-Class Financial Systems
> Generated: 2026-03-28 | Based on: Xero, QuickBooks, SAP Finance, Oracle Financials, NetSuite, Sage Intacct, Microsoft Dynamics 365 Finance

---

## What You Already Have (Strong Foundation)

| Area | Status |
|---|---|
| Multi-level Chart of Accounts (with hierarchy) | ✅ Complete |
| Double-entry journaling + period locking | ✅ Complete |
| Accounting periods (open/closing/closed) | ✅ Complete |
| Cost centres (hierarchical) | ✅ Complete |
| Customer & Vendor invoicing (AR/AP) | ✅ Complete |
| 3-way match (PO ↔ GRN ↔ Invoice) with variance tolerances | ✅ Complete |
| Recurring invoice templates (daily/weekly/monthly/quarterly/annual) | ✅ Complete |
| Receipt lifecycle tracking (draft→sent→viewed→downloaded) | ✅ Complete |
| Multi-method payments (cash/bank/MoMo/card/cheque) | ✅ Complete |
| Bank account register (GL-linked) | ✅ Complete |
| Bank reconciliation workflow | ✅ Partial |
| Tax rate configuration (VAT, NHIL, GETFund, withholding) | ✅ Complete |
| Fixed asset lifecycle (acquisition → depreciation → disposal) | ✅ Complete |
| Asset custodian tracking (HR integration) | ✅ Complete |
| Asset warranty & insurance management | ✅ Complete |
| Asset geospatial mapping (lat/lng/GeoJSON) | ✅ Complete |
| Asset maintenance records | ✅ Complete |
| Trial Balance | ✅ Complete |
| Income Statement (P&L) | ✅ Complete |
| Balance Sheet | ✅ Complete |
| AR Aging Report (4 buckets) | ✅ Complete |
| AP Aging Report (4 buckets) | ✅ Complete |
| General Ledger (account drill-down) | ✅ Complete |
| Cash Flow Statement (indirect method) | ✅ Complete |
| Finance Dashboard (6 KPIs) | ✅ Partial |
| Module integration (HR payroll, Farms, Hostels, Construction, Fleet, Manufacturing) | ✅ Complete |
| Payment gateway bridge (PaymentsChannel) | ✅ Complete |

---

## The Gaps — Prioritised

### 🔴 HIGH PRIORITY — Core gaps blocking real-world finance operations

✅ **1. Customer Management (dedicated AR)**
- `customer_name` is only a free-text string on invoices — no Customer record
- No credit limit enforcement
- No customer payment terms (net7/net14/net30)
- No customer account balance (open AR by customer)
- No customer statement generation
- No customer portal / self-service invoice view
- Tables needed: `fin_customers`, with link to existing vendor/company models

✅ **2. Credit Notes & Debit Notes**
- No way to reverse or partially refund an invoice
- No credit note application (offset against open invoices)
- No debit note for vendor disputes
- No CN/DN number sequence
- Tables needed: `fin_credit_notes`, `fin_credit_note_lines`, `fin_debit_notes`, `fin_debit_note_lines`

✅ **3. Budget Management**
- No budget creation (annual, department, project, cost centre)
- No budget vs. actual variance report
- No budget approval workflow
- No budget revision tracking (version history)
- No commitment accounting (PO commitments don't reduce budget)
- Tables needed: `fin_budgets`, `fin_budget_lines`, `fin_budget_revisions`

✅ **4. Expense Claims & Employee Reimbursement**
- No expense claim submission by employees (via staff portal)
- No expense categories / per diem rates
- No expense approval workflow
- No mileage / travel expense tracking
- No receipt photo attachment on expense items
- No export to payroll for reimbursement
- Tables needed: `fin_expense_claims`, `fin_expense_claim_lines`, `fin_expense_categories`

⚠️ **5. Partial Payments & Payment Allocation**
- Payments are 1:1 with invoices (no partial payment model)
- No "amount_paid" / "amount_outstanding" tracking per invoice
- No payment allocation across multiple invoices from one payment
- No overpayment handling (credit back or advance)
- Tables needed: `fin_payment_allocations` pivot
- *Status: `fin_payment_allocations` table + PaymentAllocationResource implemented (2026-03-28); invoice amountPaid/amountOutstanding helpers added*

✅ **6. Petty Cash Management**
- No petty cash fund setup per department/location
- No petty cash requisition workflow
- No petty cash replenishment cycle
- No petty cash book report
- Tables needed: `fin_petty_cash_funds`, `fin_petty_cash_transactions`

⚠️ **7. Withholding Tax Certificates & GRA Compliance**
- `tax_rates` has `withholding` type but no certificate generation
- No WHT certificate numbering or printing
- No WHT filing summary (monthly/annual GRA report)
- No VAT return report (Output VAT − Input VAT)
- No NHIL/GETFund levy computation report
- No GRA e-Invoice (EFRIS) integration hook
- *Status: WhtCertificatePage implemented (2026-03-28) — filter + print view; certificate PDF pending*

⚠️ **8. Advance Payments & Deposits**
- No advance payment recording against a customer/vendor
- No advance allocation to future invoices
- No customer deposit tracking (liability account management)
- Tables needed: `fin_advance_payments`
- *Status: `fin_advance_payments` table + AdvancePaymentResource implemented (2026-03-28)*

⚠️ **9. Automated Depreciation Scheduling**
- Depreciation runs are manually triggered ("Generate Now")
- No scheduled artisan command to auto-run monthly depreciation
- No batch run for all assets in one operation
- No depreciation schedule report (projected future depreciation)
- Console command needed: `finance:run-depreciation`
- *Status: RunDepreciationCommand exists — verify it is registered in scheduler*

✅ **10. Multi-Currency Support**
- No exchange rate management (GHS is assumed throughout)
- No FCY invoice with base-currency equivalent
- No realised/unrealised FX gain/loss
- No period-end revaluation of FCY balances
- Tables needed: `fin_currencies`, `fin_fx_rates`

---

### 🟡 MEDIUM PRIORITY

⚠️ **11. Automated Bank Statement Import & Auto-Reconciliation**
- Reconciliation is fully manual (type balances in a form)
- No CSV/OFX/MT940 statement import
- No auto-matching of bank transactions to GL entries
- No unmatched items list (outstanding cheques, deposits in transit)
- Tables needed: `fin_bank_statement_lines` (imported rows) with match status
- *Status: Manual reconciliation exists; auto-match pending*

✅ **12. VAT / Tax Return Reports**
- No VAT return report (input tax from AP vs. output tax from AR)
- No tax period summary with filing deadline tracking
- Cannot produce GRA VAT Standard Rate Return or Flat Rate return

⚠️ **13. Financial Ratios & Advanced Dashboard**
- Dashboard has 6 KPIs — no financial ratio analysis
- No current ratio, quick ratio, DSO (Days Sales Outstanding), DPO (Days Payable Outstanding)
- No working capital trend
- No revenue/expense trend charts (month-over-month)
- No cash runway / burn rate (for project companies)
- *Status: FinancialRatiosPage implemented (2026-03-28) — 7 ratios with benchmark cards*

✅ **14. Customer Statements & Vendor Statements**
- No customer account statement (period activity: invoices, payments, credits, balance)
- No vendor statement
- No bulk statement send (email all customers their statements)

✅ **15. Cheque Management**
- Cheque is a payment method but no cheque book management
- No cheque sequence tracking (cheque book, leaf numbers)
- No cheque status: issued / presented / cleared / returned / void
- No post-dated cheque management
- Tables needed: `fin_cheque_books`, `fin_cheques`

✅ **16. Document Attachments on Invoices & Payments**
- Fixed assets have a full document library; invoices & payments have none
- Can't attach vendor invoice PDF, remittance advice, or payment confirmation
- Tables needed: `fin_invoice_documents`, `fin_payment_documents`

✅ **17. Segment / Profit Centre Reporting**
- Cost centres exist but no P&L breakout by cost centre
- No department-level income statement
- No project-level profitability report

✅ **18. Consolidated Reporting (Multi-Company / Group)**
- Each company is an isolated tenant — no group consolidation
- No consolidated Balance Sheet, P&L, or Cash Flow across companies
- No intercompany elimination entries

✅ **19. Automated Invoice Reminders**
- `InvoiceMarkedOverdue` event exists but reminder schedule is unclear
- No configurable reminder rules (send at -7 days, -1 day, +1 day, +7 days)
- No reminder template management
- Tables needed: `fin_invoice_reminder_rules`

⚠️ **20. Audit Trail on Financial Records**
- No detailed change log on journal entries (who changed which line, when)
- No edit history before period close
- No approver/reviewer sign-off workflow on journal entries
- No segregation of duties enforcement (creator ≠ approver)
- *Status: `fin_journal_entry_logs` table + JournalEntryLog model + JournalEntryLogResource implemented (2026-03-28); JournalEntry updating hook added*

❌ **21. Inter-Company Transactions**
- No intercompany journal entries
- No due-to / due-from account auto-posting
- No intercompany invoice matching

✅ **22. Payment Batch Processing**
- No bulk AP payment run (select multiple vendor invoices, create one bank transfer batch)
- No NACHA/BACS payment file export
- No payment approval step before bank submission

⚠️ **23. Notes & Comments on Financial Records**
- No comment thread on invoices, payments, or journal entries
- Commentions package is available in the project but not wired to Finance models
- *Status: Commentions package installed; Finance models not yet wired*

---

### 🟢 LOWER PRIORITY — Enterprise differentiators

❌ **24. IFRS 16 Lease Accounting**
- No right-of-use (ROU) asset management
- No lease liability amortization schedule
- No lease classification (finance vs. operating)
- Tables needed: `fin_leases`, `fin_lease_schedules`

❌ **25. Deferred Revenue & Revenue Recognition**
- Recurring invoices exist but no deferred revenue schedule
- No straight-line recognition of advance billing
- No IFRS 15 / ASC 606 revenue recognition rules

❌ **26. Asset Impairment Recording**
- No impairment test documentation
- No impairment loss journal posting
- No impairment reversal tracking

❌ **27. Donor / Restricted Fund Accounting**
- No fund-based accounting (critical for NGOs / grant-funded projects)
- No fund balance reporting
- No grant expenditure tracking vs. budget

❌ **28. Financial Forecasting**
- No cash flow forecast (projections from recurring items)
- No revenue forecast against budget
- No rolling 12-month forecast

❌ **29. Mobile Expense Capture**
- No receipt photo OCR for expense items
- No mobile-first expense submission

❌ **30. GRA / e-Invoice Integration**
- No EFRIS (Electronic Fiscal Receipting and Invoicing Solution) hook
- No GRA invoice verification QR code generation

---

## Summary Scorecard

| Category | Current | Target | Gap |
|---|---|---|---|
| Chart of Accounts | 95% | 100% | Minor |
| Journal Entry / GL | 85% | 100% | Medium |
| Invoicing (AR/AP) | 80% | 100% | Medium |
| Credit Notes / Debit Notes | 100% | 100% | ✅ Done |
| Customer Management | 100% | 100% | ✅ Done |
| Partial Payments & Allocation | 70% | 100% | Partial |
| Budget Management | 100% | 100% | ✅ Done |
| Expense Claims | 100% | 100% | ✅ Done |
| Petty Cash | 100% | 100% | ✅ Done |
| WHT Certificates & GRA Reports | 60% | 100% | Partial |
| Multi-Currency | 100% | 100% | ✅ Done |
| Bank Reconciliation (auto-match) | 30% | 100% | Large |
| Tax Return Reports (VAT/NHIL) | 100% | 100% | ✅ Done |
| Fixed Asset Depreciation (auto-schedule) | 60% | 100% | Medium |
| Financial Ratios & Dashboard | 70% | 100% | Partial |
| Customer Statements | 100% | 100% | ✅ Done |
| Cheque Management | 100% | 100% | ✅ Done |
| Segment / Cost Centre Reporting | 100% | 100% | ✅ Done |
| Consolidated Multi-Company Reports | 100% | 100% | ✅ Done |
| Audit Trail on GL | 70% | 100% | Partial |
| Document Attachments (Invoices) | 100% | 100% | ✅ Done |
| Payment Batch Processing | 100% | 100% | ✅ Done |
| Invoice Reminders (automated) | 100% | 100% | ✅ Done |
| Advance Payments / Deposits | 80% | 100% | Partial |
| IFRS 16 Lease Accounting | 0% | 100% | Lower priority |
| Deferred Revenue / IFRS 15 | 5% | 100% | Lower priority |
| Donor / Fund Accounting | 0% | 100% | Lower priority |
| GRA e-Invoice (EFRIS) | 0% | 100% | Lower priority |

---

## Implementation Roadmap

### Phase 1 — Operational must-haves (unblock daily finance work) ✅ COMPLETE
1. **Credit Notes & Debit Notes** — ✅ Done
2. **Partial Payments & Payment Allocation** — ✅ PaymentAllocationResource + fin_payment_allocations
3. **Customer Management** — ✅ CustomerResource + fin_customers
4. **Expense Claims** — ✅ ExpenseClaimResource + staff portal
5. **Petty Cash Management** — ✅ PettyCashResource

### Phase 2 — Compliance & controls ⚠️ PARTIAL
1. Budget Management — ✅ Done (BudgetResource + BudgetVsActualReport)
2. WHT Certificates + VAT Return Report — ⚠️ WhtCertificatePage done; EFRIS pending
3. Automated Depreciation Scheduling — ⚠️ RunDepreciationCommand exists; verify scheduler
4. Bank Statement Import + Auto-Reconciliation — ⚠️ Manual reconciliation only
5. Audit Trail on Journal Entries — ✅ JournalEntryLogResource + hook added

### Phase 3 — Reporting depth & automation ⚠️ PARTIAL
1. Financial Ratios Dashboard — ✅ FinancialRatiosPage with 7 ratios
2. Customer & Vendor Statements — ✅ CustomerStatementReport done
3. Segment / Cost Centre P&L Reports — ✅ CostCentreReport done
4. Payment Batch Processing — ✅ PaymentBatchResource done
5. Automated Invoice Reminders — ✅ InvoiceReminderRuleResource done

### Phase 4 — Enterprise & compliance ❌ PENDING
1. Multi-Currency — ✅ Done (CurrencyResource + FxRateResource)
2. Cheque Management — ✅ Done (ChequeResource)
3. Consolidated Group Reporting — ✅ Done (ConsolidatedReport page)
4. IFRS 16 Lease Accounting — ❌ Not yet
5. GRA e-Invoice / EFRIS Integration — ❌ Not yet