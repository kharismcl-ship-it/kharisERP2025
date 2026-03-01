
# ProcurementInventory Module — Audit Findings
**Date:** 2026-03-01
**Module Status:** 70% — Solid Core, Ready for Completion & Cross-Module Integration

---

## 1. What's Working Well

- **8 models fully implemented** with relationships, scopes, state machines, and calculated attributes
- **2 services** (ProcurementService, StockService) with full transactional logic
- **Full PO lifecycle:** draft → submitted → approved → ordered → partially_received → received → closed / cancelled
- **GRN confirmation** updates stock levels atomically via StockService
- **Finance integration live:** PurchaseOrderApproved → AP invoice; GoodsReceived → inventory journal
- **6 Filament v4 resources** covering all models (VendorResource, ItemResource, ItemCategoryResource, PurchaseOrderResource, GoodsReceiptResource, StockLevelResource)
- **2 relation managers** on PurchaseOrderResource (lines inline + GRNs inline)
- **6 policies** registered and enforced via Spatie permissions
- **Event-driven architecture** already firing 2 events (PurchaseOrderApproved, GoodsReceived)

---

## 2. Filament UI Gaps

### 2.1 — Missing View Pages
| Resource | Missing |
|---|---|
| VendorResource | ViewVendor page |
| ItemResource | ViewItem page |
| ItemCategoryResource | ViewItemCategory page |
| StockLevelResource | No view/edit — read-only list only (correct for now) |

### 2.2 — Missing Navigation Icons / Groups
All resources use generic icons. Recommended:
| Resource | Recommended Icon | Nav Group |
|---|---|---|
| VendorResource | `OutlinedBuildingStorefront` | Procurement |
| ItemResource | `OutlinedCube` | Inventory |
| ItemCategoryResource | `OutlinedTag` | Inventory |
| PurchaseOrderResource | `OutlinedShoppingCart` | Procurement |
| GoodsReceiptResource | `OutlinedInboxArrowDown` | Procurement |
| StockLevelResource | `OutlinedChartBar` | Inventory |

Split into two nav groups: **Procurement** and **Inventory**.

### 2.3 — PurchaseOrder Workflow Actions Incomplete
- ViewPurchaseOrder has no "Submit", "Approve", "Mark Ordered", or "Receive Goods" actions wired up
- Receiving goods requires a custom form (which lines, how many units) — currently no UI for this
- No "Cancel PO" action with confirmation

### 2.4 — StockLevel Adjustments
- No stock adjustment form (manual count correction, write-off, shrinkage)
- No stock history / movement log
- No "Reorder Now" button on low-stock items

### 2.5 — GoodsReceipt Missing Bulk Receive
- Receiving requires specifying exact qty per line
- No way to click "Receive All" for a fully arrived delivery

### 2.6 — Vendor View Page Missing
- Vendor has no view page; clicking vendor just opens edit form
- Should show PO history, spend totals, performance metrics inline

---

## 3. Internal Feature Gaps

### 3.1 — No Reorder Automation
`StockLevel.needsReorder()` returns true when on_hand ≤ reorder_level but nothing acts on it. No command, observer, or job to auto-create a draft PO or alert.

### 3.2 — No Three-Way Match Validation
PO total vs Finance AP invoice vs GRN received value are never compared. A vendor could invoice more than PO approved amount and the system would not detect it.

### 3.3 — SyncProcurementToExpensesCommand Is a Stub
`finance:sync-procurement` exists but only outputs a placeholder message. Batch sync for POs whose finance_invoice_id is still null doesn't work.

### 3.4 — No Vendor Performance Tracking
No model or table for:
- On-time delivery rate
- Quality acceptance rate (qty_rejected / qty_received)
- Price variance (PO price vs actual invoice price)
- Total spend by period

### 3.5 — No Purchase Requisition
No request-before-order workflow. Any user with create permission can raise a PO directly, bypassing a requisition/approval step.

### 3.6 — No Goods Inspection / Quality Check
GRN has no rejection/defect field. Items received in poor condition are still counted as fully received.

### 3.7 — No Supplier Contracts / Price Lists
No model for blanket orders, rate contracts, or agreed vendor price lists. All prices are manually entered per PO.

### 3.8 — No Stock Movement Log (Ledger)
Stock is updated by StockService but there is no history of each in/out transaction, making it impossible to audit stock changes or investigate discrepancies.

### 3.9 — No Barcode / QR Scanning Support
Items have SKU but no barcode generation or scanning integration for receiving or stock counts.

### 3.10 — Seeder is Empty
`ProcurementInventoryDatabaseSeeder` has no seed data — no item categories, no default vendors, no sample items. Difficult to onboard without a starting dataset.

---

## 4. Cross-Module Integration Analysis

### 4.1 — Finance Module — 95% Integrated
**Working:**
- PurchaseOrderApproved → RecordPurchaseOrderExpense → creates AP vendor invoice
- GoodsReceived → PostInventoryOnGoodsReceived → posts GL (DR Inventory, CR AP)
- PO.finance_invoice_id FK links back to Finance invoice
- EnhancedIntegrationService.recordProcurementExpense() fully implemented

**Still Missing:**
- SyncProcurementToExpensesCommand — batch fix for POs missing finance_invoice_id (stub only)
- Three-way match: Finance invoice amount must equal PO total (no validation)
- AP payment release after three-way match approval (Finance cannot mark invoice 'paid' until GRN confirmed)
- Rejection path: if GRN qty < PO qty, AP invoice should be credit-noted or partially cancelled

---

### 4.2 — CommunicationCentre Module — 0% Integrated
**Missing:**
- No email templates for PO approval notifications to vendors
- No email for GRN confirmation to vendors
- No SMS/email alert when stock falls below reorder level
- No notification to approver when PO submitted for approval

**Templates to seed:**
| Code | Channel | Variables | Trigger |
|---|---|---|---|
| `po_submitted_approval` | email | `po_number`, `vendor_name`, `total`, `submitted_by` | PO status → submitted |
| `po_approved_vendor` | email | `po_number`, `company_name`, `delivery_date`, `items_count` | PO status → approved |
| `grn_confirmed` | email | `grn_number`, `po_number`, `vendor_name`, `received_qty` | GRN confirmed |
| `stock_low_alert` | email + sms | `item_name`, `sku`, `on_hand`, `reorder_level` | Stock check command |
| `po_cancelled` | email | `po_number`, `vendor_name`, `reason` | PO cancelled |

---

### 4.3 — Hostels Module — 0% Integrated
**What Hostels needs from Procurement:**
- Cleaning supplies (detergents, mops, linen)
- Staff uniforms and PPE
- Maintenance/repair materials (paint, fittings, light bulbs)
- Kitchen supplies (if hostel has a kitchen)

**Integration Pattern:**
- Add `hostel_id` or `entity_type/entity_id` to PurchaseOrder (already partially there with generic module/entity fields)
- Hostel managers can raise POs tagged to their hostel
- Hostel expense posts to Finance automatically via existing listener

---

### 4.4 — HR Module — 0% Integrated
**What HR needs from Procurement:**
- Staff PPE (gloves, boots, vests, helmets)
- Office supplies by department
- Training materials/books
- New employee onboarding equipment (laptop, phone, desk items)

**Integration Pattern:**
- Add optional `department_id` or `cost_centre_id` to PurchaseOrderLine
- HR events (NewEmployeeOnboarded) could trigger draft PO for onboarding items
- Finance cost centre allocation (Phase 7.6) already implemented — can link PO lines to cost centres

---

### 4.5 — Fleet Module — 0% Integrated
**What Fleet needs from Procurement:**
- Spare parts (tires, oil, filters, brakes, belts, batteries)
- Fuel bulk purchase (PO to fuel station)
- Vehicle cleaning supplies
- Servicing consumables

**Integration Pattern:**
- Add `vehicle_id` to PurchaseOrderLine for parts tracking
- Fleet maintenance creates draft PO for required parts
- Spare parts received from GRN auto-reduce maintenance cost estimate
- New events needed: `MaintenancePartsRequested` → triggers draft PO

---

### 4.6 — Farms Module — 0% Integrated
**What Farms needs from Procurement:**
- Seeds by crop cycle
- Fertilizers and pesticides
- Animal feed and veterinary medicines
- Farm equipment parts
- Irrigation supplies

**Integration Pattern:**
- Add `farm_id` and `crop_cycle_id` to PurchaseOrder
- `CropCycleStarted` event → triggers draft PO for seed/fertilizer based on season template
- Harvest season planning drives bulk procurement

---

### 4.7 — Construction Module — 0% Integrated
**What Construction needs from Procurement:**
- Building materials per project phase (cement, steel, sand, blocks, timber)
- Equipment rental (POs to equipment suppliers)
- Subcontractor material supply agreements

**Integration Pattern:**
- Add `project_id` and `phase_id` to PurchaseOrder
- `ProjectMilestoneApproved` event → triggers materials PO for that phase
- Three-way match is CRITICAL here (materials billed vs delivered vs used)
- Construction site inventory (materials consumed per project) needs StockLevel integration

---

### 4.8 — Manufacturing Modules — 0% Integrated
**What Manufacturing needs from Procurement:**
- Raw materials (paper stock, water treatment chemicals, dyes, etc.)
- Packaging materials
- Maintenance parts for production line

**Integration Pattern:**
- Bill of Materials (BOM) concept: each product has a list of raw materials needed
- `ProductionBatchStarted` event → auto-creates POs for raw materials if stock insufficient
- Finished goods production reduces raw material stock levels via StockService

---

### 4.9 — Core Module — 0% Integrated
**What Core could provide:**
- AutomationService triggers:
  - Daily: Check all stock levels → send reorder alerts
  - Weekly: Generate draft POs for items below reorder threshold
  - Monthly: Vendor performance summary report
  - PO aging alert (PO submitted > 3 days without approval)

---

## 5. All Events Across Codebase — Procurement Listener Status

| Event | Source Module | Procurement Listens? | Action |
|---|---|---|---|
| `PurchaseOrderApproved` | ProcurementInventory | Fires | Finance creates AP invoice |
| `GoodsReceived` | ProcurementInventory | Fires | Finance posts inventory GL |
| `[POSubmitted]` | ProcurementInventory | Not fired | Could notify approver |
| `[StockLow]` | ProcurementInventory | Not fired | Could alert purchaser |
| `[MaintenancePartsRequested]` | Fleet | Does not exist | Could create draft PO |
| `[CropCycleStarted]` | Farms | Does not exist | Could create draft seasonal PO |
| `[ProjectMilestoneApproved]` | Construction | Does not exist | Could create materials PO |
| `[ProductionBatchStarted]` | Manufacturing | Does not exist | Could trigger raw material PO |
| `[NewEmployeeOnboarded]` | HR | Does not exist | Could create onboarding items PO |

---

## 6. Missing Features for Full ERP Procurement

### 6.1 — Critical (Complete the Core Module)
| Feature | Why Needed | Effort |
|---|---|---|
| View pages for Vendor, Item, ItemCategory | Users can't view details without editing | Small |
| PO workflow actions on ViewPurchaseOrder | Submit/Approve/Receive must be clickable in UI | Medium |
| Receive Goods UI (inline form) | Specify qty per line on GRN creation | Medium |
| Stock Movement Log table | Audit trail for every in/out | Medium |
| ReorderAlertCommand | Daily check + email/SMS to purchaser | Small |
| Fix SyncProcurementToExpensesCommand | Batch fix for POs with no finance_invoice_id | Small |

### 6.2 — High Priority (Cross-Module & Operations)
| Feature | Why Needed | Effort |
|---|---|---|
| CommunicationCentre templates | Vendor PO notifications, GRN confirm | Small |
| `cost_centre_id` on PurchaseOrderLine | Link spend to dept/project (Finance Phase 7.6 ready) | Small |
| Stock Adjustment form | Manual corrections, write-offs, shrinkage | Small |
| Vendor View page with PO history | Vendor relationship management | Small |
| Three-Way Match validation | Finance cannot pay without GRN confirmation | Medium |
| Vendor Performance Report page | Track on-time %, spend, variance | Medium |
| Procurement Dashboard Filament page | KPIs: open POs, low stock count, spend MTD | Medium |

### 6.3 — Medium Priority (Module Extensions)
| Feature | Why Needed | Effort |
|---|---|---|
| PO entity tagging (hostel/farm/project) | Department/project cost allocation | Small |
| Fleet spare parts integration | Track parts per vehicle | Medium |
| Farms seasonal procurement | Seed/fertilizer POs by crop cycle | Medium |
| Construction materials per project | Phase-level procurement tracking | Large |
| Purchase Requisition workflow | Request before PO (budget control) | Large |

### 6.4 — Low Priority / Post-MVP
| Feature | Why Needed | Effort |
|---|---|---|
| Vendor rate contracts / price lists | Lock vendor prices for period | Large |
| Barcode/QR scanning | Fast receiving workflow | Large |
| GRN rejection / quality hold | QC before stock posting | Medium |
| Bill of Materials (BOM) | Manufacturing raw material planning | Large |
| Import/export items via CSV | Bulk onboarding | Medium |
| Vendor portal (external access) | Vendors self-manage quotes/deliveries | Very Large |

---

## 7. Full Implementation Plan

### Phase 1 — Filament UI Completion (QUICK WINS)
1. Add View pages for Vendor, Item, ItemCategory with sectioned infolists
2. Fix navigation icons and split into **Procurement** / **Inventory** nav groups
3. Wire PO workflow actions on ViewPurchaseOrder (Submit, Approve, MarkOrdered, Cancel, ReceiveGoods)
4. Build inline Receive Goods modal on ViewPurchaseOrder (form per line with qty input)
5. Add "Cancel PO" action with confirmation dialog
6. Bulk action: "Approve Selected POs" for admin

### Phase 2 — Stock Operations
1. Create `stock_movements` table and StockMovement model (type: receipt/adjustment/issue/write_off)
2. Log every StockService call to stock_movements
3. Add StockAdjustment form (item, qty, reason, type) on StockLevelResource
4. Create StockMovementResource (read-only, filterable by item/date/type)
5. "Reorder Now" button on StockLevelResource that creates a draft PO

### Phase 3 — Automation & Alerts
1. `ReorderAlertCommand` (`procurement:reorder-alert`) — daily check, email/SMS via CommunicationCentre
2. Seed CommunicationCentre templates: `po_submitted_approval`, `po_approved_vendor`, `grn_confirmed`, `stock_low_alert`, `po_cancelled`
3. Fire `POSubmittedForApproval` event on PO submit → notify approvers
4. Fix `SyncProcurementToExpensesCommand` — batch create Finance AP invoices for unlinked POs
5. Add `cost_centre_id` (nullable) to `purchase_order_lines` — link to Finance cost centres

### Phase 4 — Finance Three-Way Match
1. `VendorInvoice` concept: when Finance AP invoice created, link back to specific GRN
2. Add match_status to invoices: `unmatched` / `matched` / `disputed`
3. Finance listener: on GRN confirmed → check AP invoice qty matches GRN qty → update match_status
4. Block AP payment release if match_status != 'matched'
5. Discrepancy report: PO total vs GRN value vs AP invoice amount

### Phase 5 — Vendor Management & Reporting
1. Vendor View page with: PO history table, total spend, on-time delivery %, last order date
2. `VendorPerformance` model/report page — track delivery_date vs expected_delivery_date
3. Procurement Dashboard Filament page:
   - Open POs count + value, Low stock items count, Spend MTD by category
   - GRNs pending confirmation, Overdue POs (ordered but not received)
4. Add PO entity tagging: `hostel_id`, `project_id`, `farm_id` (nullable on purchase_orders)

### Phase 6 — Cross-Module Integration Events
1. Fleet: `MaintenancePartsRequested` event → ProcurementInventory creates draft PO for parts
2. Farms: `CropCycleStarted` event → ProcurementInventory creates seasonal draft PO
3. Construction: `ProjectPhaseApproved` event → ProcurementInventory creates materials PO for phase
4. HR: `NewEmployeeOnboarded` event → ProcurementInventory creates onboarding items draft PO
5. Manufacturing: Listen to `ProductionBatchPlanned` → check stock, create raw material POs if needed

---

## 8. Database Changes Needed

| Table | Change | Phase |
|---|---|---|
| `stock_movements` (new) | type, item_id, company_id, qty_change, qty_before, qty_after, reference, reason, user_id | Phase 2 |
| `purchase_order_lines` | Add `cost_centre_id` (nullable FK cost_centres) | Phase 3 |
| `purchase_orders` | Add `hostel_id`, `project_id`, `farm_id` (nullable) | Phase 5 |
| `purchase_orders` | Add `requisition_id` (nullable, Phase 6 purchase requisition) | Phase 6 |
| `goods_receipt_lines` | Add `quantity_rejected`, `rejection_reason` | Phase 4 |
| `finance_invoices` | Add `match_status` enum [unmatched/matched/disputed] | Phase 4 |

---

## 9. File Reference

| Path | Purpose |
|---|---|
| `app/Models/` | 8 models (Vendor, Item, ItemCategory, PO, POLine, GRN, GRNLine, StockLevel) |
| `app/Services/ProcurementService.php` | PO lifecycle + GRN creation |
| `app/Services/StockService.php` | Stock level management |
| `app/Events/` | PurchaseOrderApproved, GoodsReceived |
| `app/Filament/Resources/` | 6 resources (VendorResource, ItemResource, ItemCategoryResource, PurchaseOrderResource, GoodsReceiptResource, StockLevelResource) |
| `app/Filament/ProcurementInventoryFilamentPlugin.php` | Plugin registration |
| `database/migrations/` | 8 migrations |
| `app/Policies/` | 6 policies |
| `Modules/Finance/app/Listeners/ProcurementInventory/` | AP invoice + inventory GL listeners |
