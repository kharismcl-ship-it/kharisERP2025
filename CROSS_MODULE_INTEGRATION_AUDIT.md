# Cross-Module Integration Audit
**Date:** 2026-03-01
**Rules enforced:**
1. Communications → `CommunicationCentre` (`CommunicationService::sendFromTemplate()`)
2. Finance GL → `Finance` (JournalEntry / Invoice via Finance services)
3. Payments → `PaymentsChannel` (`HasPayments` trait + gateway)
4. Procurement materials → `ProcurementInventory` (nullable `item_id` FK → `items` table)
5. Building/site work → `Construction` module records

---

## Status Matrix

| Module | Comms | Finance | Payments | Procurement | Construction | Overall |
|---|---|---|---|---|---|---|
| Hostels | DONE | DONE | DONE | SILOED | N/A | 95% |
| HR | DONE | DONE | N/A | MISSING | N/A | 90% |
| PaymentsChannel | Missing (acceptable) | DONE | CORE | N/A | N/A | 90% |
| ProcurementInventory | DONE | DONE | N/A | CORE | PARTIAL | 80% |
| Finance | **MISSING** | CORE | DONE | DONE | STUB | 85% |
| Farms | DONE | PARTIAL | DONE | DONE | N/A | 85% |
| Fleet | DONE | PARTIAL | N/A | **MISSING** | N/A | 75% |
| Core | PARTIAL | PARTIAL | N/A | N/A | N/A | 70% |
| Construction | **MISSING** | **STUB** | **MISSING** | **MISSING** | CORE | 20% |
| ManufacturingPaper | **MISSING** | **STUB** | **MISSING** | **MISSING** | N/A | 5% |
| ManufacturingWater | **MISSING** | **MISSING** | **MISSING** | **MISSING** | N/A | 5% |

---

## HIGH PRIORITY GAPS

### GAP-1: Finance — No Outbound Communication
**Risk:** Invoices created but never emailed/SMSd to customer. No overdue reminders. No payment confirmation to payer.
**Scope:**
- Invoice created → notify customer (email/SMS)
- Invoice overdue → reminder to customer
- Payment recorded → receipt to payer
**Fix:** CommTemplates + listeners on `InvoiceCreated`, `InvoiceOverdue`, `PaymentSucceeded`; scheduled command for overdue scan.
**Status:** ✅ IMPLEMENTED (2026-03-01) — see below

---

### GAP-2: Construction — All 5 Rules Broken
**Risk:** Construction is a financial black hole. No GL posting, no payment collection, no procurement link, no comms.
**Scope:**
- `MaterialUsage`: add `item_id` nullable FK → `ProcurementInventory\Models\Item`
- `ConstructionProject`: add `HasPayments` trait + payment_status field
- Finance listener `CreateInvoiceForProject`: implement (currently stubbed)
- Add `ProjectStatusListener` → CommunicationService alerts on milestone/overrun/completion
- CommTemplates for Construction events
**Status:** ✅ IMPLEMENTED (2026-03-01) — see below

---

### GAP-3: Fleet — Maintenance Parts Not in Procurement
**Risk:** Spare parts consumed during maintenance never deducted from stock.
**Scope:**
- `MaintenanceRecord`: add `item_id` nullable FK → `ProcurementInventory\Models\Item`
- Finance GL posting for maintenance is partially stubbed
**Status:** PENDING

---

### GAP-4: ManufacturingPaper — Fully Isolated
**Risk:** No revenue invoices, no raw material tracking, no QA alerts.
**Scope:**
- Batch completion → Finance Invoice (sales revenue)
- Raw material input → link to `item_id` (ProcurementInventory)
- QA failure / equipment fault → CommunicationCentre alert
- Finance listener `CreateInvoiceForBatch`: implement
**Status:** PENDING

---

### GAP-5: ManufacturingWater — Fully Isolated
**Risk:** Water distribution revenue not in GL. Chemical costs not tracked. No alerts.
**Scope:**
- Distribution sale → Finance Invoice
- Chemical consumption → `item_id` FK (ProcurementInventory)
- Tank level / QA alerts → CommunicationCentre
**Status:** PENDING

---

## MEDIUM PRIORITY GAPS

| Gap | Module | Description | Status |
|---|---|---|---|
| Hostel inventory siloed | Hostels | `HostelInventoryItem` not linked to `ProcurementInventory\Item` | PENDING |
| Construction comms | Construction | No milestone / budget-overrun alerts | Fixed in GAP-2 |

---

## Implementation Log

### 2026-03-01 — Finance Comms (GAP-1)
- Added CommTemplates: `finance_invoice_issued`, `finance_invoice_overdue`, `finance_payment_receipt`
- Created `SendInvoiceNotification` listener on `InvoiceCreated` event
- Created `SendPaymentReceiptNotification` listener on `PaymentSucceeded` (Finance scope)
- Created `FinanceOverdueInvoiceAlertCommand` scheduled daily at 09:00
- Registered in Finance EventServiceProvider + FinanceServiceProvider

### 2026-03-01 — Construction Full Integration (GAP-2)
- Migration: `item_id` nullable FK on `construction_material_usages` → `items`
- Migration: `payment_status`, `amount_paid` on `construction_projects`
- Model: `MaterialUsage` — added `item()` relationship
- Model: `ConstructionProject` — added `HasPayments` trait
- Implemented `Finance\Listeners\Construction\CreateInvoiceForProject`
- Created `ProjectStatusListener` → CommunicationService for milestone/overrun/completion
- CommTemplates: `construction_project_milestone`, `construction_budget_overrun`, `construction_project_completed`
- Registered all in Construction + Finance providers

---

## Notes for Implementation
- **Filament v4**: all resources use `Schema $schema` not `Form $form`; actions import from `Filament\Actions\*`
- All notifications via `CommunicationService::sendFromTemplate($slug, $notifiable, $data)`
- All expenses use double-entry: DR Expense account / CR Cash or AP account
- All `item_id` FKs are nullable — only add FK constraint if table exists (cross-module safety)