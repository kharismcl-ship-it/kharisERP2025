# Farms Module — Integration Findings & Fix Plan

**Date:** 2026-03-01
**Status:** Findings documented, high-priority fixes implemented in same session.

---

## 1. Cross-Module Integration Map — Actual vs Planned

| Integration | Feature | Implemented | Gap |
|---|---|---|---|
| **HR** | `farm_workers.employee_id` FK | DONE | — |
| **HR** | Worker live leave status (ViewFarmWorker) | DONE | — |
| **HR** | Labour cost via CropActivity.cost | DONE | — |
| **Finance** | `FarmSale.invoice_id` FK | DONE | — |
| **Finance** | `createSaleInvoice()` | DONE | — |
| **Finance** | `createExpenseJournalEntry()` | **FIXED** (this session) | Was never built — farm expenses never posted to GL |
| **PaymentsChannel** | `HasPayments` on FarmSale | **FIXED** (this session) | Sale payment status was manual-only |
| **PaymentsChannel** | `PaymentSucceeded` listener | **FIXED** (this session) | No auto-update of `payment_status` on payment |
| **CommunicationCentre** | Harvest due alert command | DONE | — |
| **CommunicationCentre** | Livestock health reminder command | DONE | — |
| **CommunicationCentre** | Task overdue command | **FIXED** (this session) | Template seeded, command missing |
| **CommunicationCentre** | SMS sending | Templates only | Commands send email only — SMS a future enhancement |
| **ProcurementInventory** | `CropInputApplication.item_id` FK | NOT DONE | Optional — planned for future |
| **Fleet** | `FarmTask.vehicle_id` FK | NOT DONE | Optional — planned for future |

---

## 2. High-Priority Fixes (This Session)

### Fix 1 — `FarmService::createExpenseJournalEntry()`
**Problem:** Farm expenses (FarmExpense model) are stored but never reflected in Finance General Ledger.
**Solution:** Added `createExpenseJournalEntry(FarmExpense)` to FarmService following the same pattern as `Finance\Services\IntegrationService::getAccountId()`. Creates a double-entry:
- DR: Expense account (code 'EXP' / type 'expense')
- CR: Cash/Bank account (code 'CASH' or 'BANK' / type 'asset')

Also added `postAllExpensesToFinance(Farm, string $from, string $to)` to batch-post unposted expenses.

### Fix 2 — `FarmsTaskOverdueAlertCommand`
**Problem:** `farms_task_overdue_email/sms` CommTemplates seeded but no command to execute alerts.
**Solution:** Created `FarmsTaskOverdueAlertCommand` (`farms:task-overdue-alerts {--days=0}`) that:
- Finds FarmTasks with `due_date < now()` and `completed_at = null`
- Sends email via `CommunicationService::sendFromTemplate('farms_task_overdue_email', ...)`
- Scheduled daily at 08:00 in FarmsServiceProvider

### Fix 3 — `HasPayments` on FarmSale + `FarmSalePaymentListener`
**Problem:** FarmSale has a buyer, total_amount, and payment_status, but no actual payment flow. Status was updated manually only.
**Solution:**
- Added `HasPayments` trait to FarmSale model with overrides for description, amount, currency, and customer fields
- Created `FarmSalePaymentListener` that listens to `PaymentSucceeded`:
  - Checks if `payIntent->payable_type === FarmSale`
  - Sums all successful transaction amounts on the intent
  - Updates `payment_status` to 'paid' if fully covered, 'partial' if not
- Registered listener in `Modules\Farms\Providers\EventServiceProvider`

---

## 3. Remaining Medium-Priority Items (Future)

### 3a — ProcurementInventory link
Add migration: `ALTER TABLE crop_input_applications ADD COLUMN item_id BIGINT UNSIGNED NULL REFERENCES items(id)`.
Add nullable relationship to CropInputApplication model.
Block on: ProcurementInventory module being built out (currently ~20% stub).

### 3b — Fleet link
Add migration: `ALTER TABLE farm_tasks ADD COLUMN vehicle_id BIGINT UNSIGNED NULL REFERENCES vehicles(id)`.
Add nullable relationship to FarmTask model.
Block on: Fleet module vehicle management being functional.

### 3c — SMS alerts
Update `FarmsHarvestDueAlertCommand` and `FarmsLivestockHealthReminderCommand` to also send SMS via the `farms_*_sms` templates using the farm's contact phone.

---

## 4. Farmbrite Parity — Still Missing

| Farmbrite Feature | Status | Priority |
|---|---|---|
| Produce inventory (harvested stock before sale) | NOT BUILT | Medium |
| Animal events ledger (births, purchases, transfers) | NOT BUILT | Medium |
| Farm equipment / asset register | NOT BUILT | Low |
| Weather / rainfall logs per farm | NOT BUILT | Low |
| Soil test records per plot | NOT BUILT | Low |
| Crop variety library / seed catalogue | NOT BUILT | Low |

---

## 5. Module Interaction Map (As-Built)

```
Farms ──────────────────────────────────────────────────────
  FarmWorker ──── HR.Employee (employee_id FK, leave query)
  CropActivity ── (labour cost tracked, not posted to GL)
  FarmExpense ─── Finance.JournalEntry (DR Expense / CR Cash)  ← FIXED
  FarmSale ──────┬─ Finance.Invoice (invoice_id FK)
                 └─ PaymentsChannel.PayIntent (HasPayments)    ← FIXED
                    └─ PaymentSucceeded → update payment_status ← FIXED
  FarmTask ──────── CommunicationCentre (overdue alert email)  ← FIXED
  CropCycle ─────── CommunicationCentre (harvest due alert)
  LivestockBatch ── CommunicationCentre (health reminder)
```