# KharisERP ProcurementInventory Module — Gap Analysis vs. World-Class Procurement Systems
> Generated: 2026-03-28 | Benchmarked against: SAP Ariba, Coupa, Oracle Procurement Cloud, Jaggaer, Ivalua, Basware, GEP SMART
> Reference: Gartner Magic Quadrant for Source-to-Pay Suites 2025 (published March 24, 2025)
> Cross-module context: Requisition, Finance, HR, Farms, Fleet, Construction modules also analysed

---

## What You Already Have (Strong Foundation)

| Area | Status |
|---|---|
| Item master with categories (product/service/raw_material/asset, SKU, UOM, reorder levels) | ✅ Complete |
| Vendor master (contact, address, banking, payment terms, currency, status) | ✅ Complete |
| Vendor portal authentication (VendorContact model, separate `/vendor` Filament panel) | ✅ Complete |
| Purchase Order full lifecycle (draft → submitted → approved → ordered → received → closed/cancelled) | ✅ Complete |
| Auto-generated PO numbers (PO-YYYYMM-NNNNNN) | ✅ Complete |
| PO line-level tax calculation (tax_rate, tax_amount, line_total auto-computed) | ✅ Complete |
| PO cost centre allocation per line | ✅ Complete |
| Goods Receipt Notes (GRN-YYYYMM-NNNNNN) with line-level quantity received/rejected | ✅ Complete |
| Rejection tracking on GRN lines (quantity_rejected + rejection_reason) | ✅ Complete |
| Stock level tracking (on_hand, reserved, on_order, available) | ✅ Complete |
| Multi-warehouse stock levels (warehouse-scoped StockLevel) | ✅ Complete |
| Warehouse master with geospatial mapping (lat/lng + MapPicker) | ✅ Complete |
| Warehouse transfers (draft → in_transit → completed, with stock debit/credit) | ✅ Complete |
| Stock movements audit ledger (receipt/adjustment/issue/transfer/return/opening) | ✅ Complete |
| Reorder level monitoring + daily alert command | ✅ Complete |
| Finance AP invoice integration (PO confirmed → Finance module AP invoice auto-created) | ✅ Complete |
| Cross-module auto-PO triggers (Fleet, Farms, Construction, HR events create draft POs) | ✅ Complete |
| CommunicationCentre notifications (PO approved, low stock, warehouse transfer completed) | ✅ Complete |
| Vendor self-service PO view (read-only in VendorPanel) | ✅ Partial |
| Procurement dashboard (8 KPIs: pending approvals, spend MTD/YTD, active vendors, low stock) | ✅ Partial |

---

## The Gaps — Prioritised

### 🔴 HIGH PRIORITY — Blockers for enterprise-grade procurement operations

**1. Three-Way Match (PO ↔ GRN ↔ AP Invoice)**
- GRN confirms receipt but is never validated against the Finance AP invoice amount
- No tolerance management (e.g., accept within ±2% of PO value)
- No automated "hold payment until GRN confirmed" rule
- No invoice exception queue when amounts diverge
- Finance module creates the AP invoice but there is no cross-reference validation back to the GRN
- Required by: Coupa, SAP Ariba, Oracle, Basware — considered Table Stakes for enterprise AP
- Tables needed: `procurement_invoice_matches` (PO line ↔ GRN line ↔ finance_invoice_line match records)

**2. Supplier Performance Scorecards**
- No on-time delivery tracking (expected_delivery_date vs actual received_at)
- No quality rate tracking (quantity_rejected / quantity_received per vendor)
- No price variance tracking (quoted price vs. actual invoice price)
- No responsiveness metrics (PO acknowledgment speed)
- No vendor performance dashboard or scoring history
- No improvement plans for underperforming vendors
- This is core SIM (Supplier Information Management) — present in every enterprise platform
- Tables needed: `procurement_vendor_performance_records`, `procurement_vendor_scorecards`

**3. Supplier Onboarding & Qualification Workflow**
- Vendors are created directly by admin with no onboarding approval flow
- No self-registration portal for new vendors
- No qualification questionnaire (financial health, insurance certificates, quality certifications)
- No approval stages (invited → submitted → under_review → approved/rejected)
- No document collection at onboarding (trade license, tax certificate, insurance, banking confirmation)
- Enterprise standard: Coupa, Jaggaer, Ivalua all provide guided supplier onboarding portals
- Tables needed: `procurement_vendor_applications`, `procurement_vendor_documents`, `procurement_vendor_qualifications`

**4. Supplier Risk Management**
- No financial health indicators per vendor
- No sanctions / watchlist screening (OFAC, UN, EU blacklists)
- No certificate expiry tracking (ISO, insurance, trade license — all expire)
- No risk tier classification (strategic/preferred/approved/restricted)
- No single-source dependency alerts (company buys >80% of a category from one vendor)
- This gap is particularly critical for Ghanaian compliance (GRA vendor registration, Ghana Revenue Authority checks)
- Tables needed: `procurement_vendor_risk_scores`, `procurement_vendor_certificates`

**5. Contract Management**
- No vendor contract / blanket order model
- PO prices are entered manually every time — no contract-linked pricing
- No framework/blanket orders (pre-agreed annual rate with call-off POs)
- No contract expiry alerts (e.g., "your supply contract with Vendor X expires in 30 days")
- No price compliance check (PO unit_price vs. contract agreed price)
- No contract obligation tracking (volume commitments, delivery SLAs)
- Every enterprise platform considers CLM a core module — currently entirely absent
- Tables needed: `procurement_contracts`, `procurement_contract_lines`, `procurement_contract_obligations`

**6. Catalog Management / Guided Buying**
- Item master exists but there is no purchasing catalog per vendor
- No vendor-specific price lists (same item can have different prices by vendor)
- No preferred supplier routing (when requesting item X, suggest Vendor Y from catalog)
- No punchout catalog support (OCI/cXML for external vendor catalogs)
- No "guided buying" experience — staff create free-form POs without catalog direction
- Maverick spend (purchases outside preferred vendors) cannot be detected or prevented
- Tables needed: `procurement_vendor_catalogs`, `procurement_vendor_catalog_items`

**7. Spend Analytics — Intelligence Layer**
- Dashboard shows 8 KPI counts — no spend classification, no breakdown charts
- No spend by category report (which item categories consume the most budget?)
- No spend by vendor analysis (top 10 vendors by spend value)
- No spend vs. budget comparison
- No maverick spend detection (purchases from non-preferred vendors)
- No savings tracking (negotiated price vs. market price)
- No period-over-period trend charts
- No supplier concentration analysis (single-source dependency warnings)
- Tables needed: no new tables — computed from existing data, but new report pages required

**8. Quality Inspection / Goods Inspection Workflow**
- GRN has quantity_rejected field but no formal QC inspection workflow
- No inspection lot creation at receipt
- No quarantine status for goods under inspection
- No defect categorisation (damaged, wrong specification, short supply, expired)
- No return-to-vendor order (RTV) generation after rejection
- No acceptance criteria per item
- Tables needed: `procurement_inspection_lots`, `procurement_rtv_orders`

**9. Purchase Requisition → PO Budget Pre-Check**
- The Requisition module handles requisitions but the ProcurementInventory PO has no link back to the approved requisition
- POs can be created directly without an approved requisition (bypassing budgetary controls)
- No "convert requisition to PO" action
- No PO ↔ requisition reference link
- Foreign key needed: `purchase_orders.requisition_id` FK to `requisitions` table

**10. Reorder Automation**
- `ReorderAlertCommand` runs daily and identifies low stock, but only sends email alerts — cannot auto-create a PO or Requisition
- No auto-replenishment PO creation when stock falls below reorder point
- No min/max restocking automation
- No integration between StockLevel.needsReorder() and the requisition/PO creation flow

---

### 🟡 MEDIUM PRIORITY

**11. Advance Shipment Notice (ASN)**
- No supplier-submitted ASN before physical delivery
- Vendor portal shows POs but vendors cannot confirm delivery dates, submit tracking info, or notify of shipments
- No dock scheduling for warehouse managers
- Tables needed: `procurement_advance_shipment_notices`, `procurement_asn_lines`

**12. Barcode / QR Code Scanning for Receiving**
- No mobile scan-to-receive workflow
- Warehouse staff enter received quantities manually (error-prone)
- No item barcode stored on Item model
- No bin-level putaway direction
- Field needed: `items.barcode`, `items.qr_code`; new mobile-friendly receiving UI needed

**13. Lot / Batch / Serial Number Tracking**
- No batch or serial number tracking at goods receipt
- Cannot trace which lot of a raw material was used in which production run
- No expiry date tracking per lot (critical for Farms module: perishables, agro-chemicals)
- No FIFO/FEFO enforcement in stock movements
- Tables needed: `procurement_stock_lots`, `procurement_serial_numbers`

**14. Stock Valuation Methods**
- Stock is tracked by quantity only — no cost layer
- No weighted average cost (WAC), FIFO, or standard cost calculation
- Cannot produce inventory valuation reports for balance sheet
- Stock movements don't record unit cost per movement
- This is a Finance/Inventory integration gap (stock asset on balance sheet has no value)
- Field needed: `stock_movements.unit_cost`, `stock_levels.average_unit_cost`

**15. Landed Cost Management**
- Import POs don't capture freight, duty, insurance, customs charges
- All landed costs are absent from inventory valuation
- No cost allocation to specific GRN lines
- Critical for Ghanaian importers (import duty, NHIL levy, customs processing fee all affect true cost)
- Tables needed: `procurement_landed_costs`, `procurement_landed_cost_lines`

**16. Vendor Statement Reconciliation**
- Vendors send monthly statements (listing POs, invoices, payments)
- No vendor statement upload/import feature
- No reconciliation of vendor statement vs. AP ledger
- No discrepancy flagging
- Common source of audit findings in Ghana and West Africa

**17. Requisition-to-PO Conversion Action**
- No UI action to convert an approved Requisition directly into a PO
- AutoCreatePurchaseOrderOnApproval listener in Requisition module creates a draft PO, but:
  - Only for material/equipment types with item_id set
  - Doesn't handle service/fund/general requisitions
  - No UI to review/edit the auto-created PO before sending
- A "Convert to PO" wizard in RequisitionResource is needed

**18. Inventory Cycle Counting**
- No scheduled cycle count feature
- Stock adjustments exist (StockService::adjust) but no structured count workflow
- No blind count (hiding expected quantity from counter to avoid bias)
- No variance report (count vs. system quantity)
- No approval workflow for count variances above a threshold
- Tables needed: `procurement_cycle_counts`, `procurement_cycle_count_lines`

**19. Vendor Diversity & ESG Tracking**
- No tracking of vendor diversity classification (local, women-owned, minority-owned, SME)
- No local content reporting (critical for Ghana government contracts: GIPC/PPA compliance)
- No ESG score per vendor
- Ghana Public Procurement Act (Act 663) requires reporting on local content in certain contracts
- Field needed: `vendors.is_local`, `vendors.diversity_class`, `vendors.local_content_score`

**20. PO Change Order Management**
- Once a PO is in ordered/partially_received status, there's no formal change order process
- Changes to price, quantity, or delivery date must be done via Edit (no audit trail)
- No change order numbering (CO-001, CO-002)
- No vendor notification of PO changes
- No re-approval required when PO value increases beyond a threshold
- Tables needed: `procurement_po_change_orders`

**21. Approval Delegation for PO Approval**
- PO approval is a single-step action — no DoA (Delegation of Authority) matrix
- No threshold-based routing (POs > GHS 50,000 need CFO approval)
- No delegation calendar (when approver is absent)
- This was just built in the Requisition module — the same pattern is needed here
- Tables needed: (reuse `requisition_workflow_rules` pattern for procurement)

**22. Supplier Collaboration on POs**
- Vendor portal shows POs in read-only mode
- Vendors cannot: acknowledge PO receipt, confirm/change delivery dates, report supply issues, submit invoices
- No two-way communication channel on POs in the vendor portal
- True supplier collaboration (Coupa, Ariba) means 90%+ of POs are acknowledged within 24hrs

---

### 🟢 LOWER PRIORITY — Enterprise differentiators

**23. Strategic Sourcing (RFx / eAuctions)**
- No RFI (Request for Information) for market research
- No formal RFP (Request for Proposal) beyond the Requisition module's RFQ
- No reverse auction / eAuction capability
- No sourcing event management (multi-round events, total cost of ownership comparison)
- Note: Requisition module has basic RFQ — but that's supplier-response collection only, not strategic sourcing
- Tables needed: `procurement_sourcing_events`, `procurement_sourcing_event_responses`

**24. Bill of Materials (BOM)**
- No BOM model for manufacturing (ManufacturingPaper / ManufacturingWater modules need this)
- Cannot explode a production order into component material requirements
- No material requirements planning (MRP) — no auto-calculation of what to purchase based on production schedule
- Tables needed: `procurement_boms`, `procurement_bom_lines`

**25. Vendor Managed Inventory (VMI)**
- No consignment stock tracking
- No vendor-controlled reorder triggers
- No shared forecasts/consumption data with vendors
- Relevant for Farms module (seed/fertilizer suppliers managing stocking at farm level)

**26. Supply Chain Finance**
- No early payment discount capture (dynamic discounting)
- No reverse factoring / supply chain finance program
- No virtual card issuance for one-off vendors
- Relevant for large GHS value orders where suppliers may need early payment

**27. Demand Forecasting / MRP**
- No AI/ML demand forecasting
- No seasonal demand planning (critical for Farms module — planting seasons, harvest periods)
- No safety stock calculation based on lead time + demand variability
- No connection between sales orders and procurement planning

**28. AI/ML Features**
- No spend classification / taxonomy auto-tagging
- No supplier risk prediction (financial distress signals)
- No anomaly detection (duplicate POs, unusual pricing, abnormal order patterns)
- No predictive reordering
- No NLP/conversational procurement interface

**29. e-Invoicing / GRA EFRIS Integration**
- No PEPPOL connectivity
- No GRA EFRIS (Electronic Fiscal Receipting and Invoicing Solution) hook for vendor invoices
- Relevant when Ghana's mandatory e-invoicing mandate is enforced for large enterprises

**30. Sanctions & AML Screening**
- No OFAC, UN, EU sanctions list check when creating or activating vendors
- Relevant for international procurement (imports, foreign vendors)
- Also relevant for Bank of Ghana AML compliance for payments

---

## Summary Scorecard

| Category | Current | Target | Gap |
|---|---|---|---|
| Purchase Order Lifecycle | 80% | 100% | Medium |
| Goods Receipt / GRN | 70% | 100% | Medium |
| Three-Way Match (PO/GRN/Invoice) | 0% | 100% | **Critical** |
| Vendor Master | 75% | 100% | Medium |
| Supplier Onboarding & Qualification | 5% | 100% | **Critical** |
| Supplier Performance Scorecards | 0% | 100% | **Critical** |
| Supplier Risk Management | 0% | 100% | **Critical** |
| Contract / Blanket Order Management | 0% | 100% | **Critical** |
| Catalog Management / Guided Buying | 10% | 100% | **Critical** |
| Spend Analytics & Intelligence | 15% | 100% | **Critical** |
| Quality Inspection / GRN Workflow | 20% | 100% | Large |
| Requisition → PO Link | 20% | 100% | Large |
| Reorder Automation (auto-PO) | 10% | 100% | Large |
| Advance Shipment Notice (ASN) | 0% | 100% | Large |
| Barcode / Mobile Receiving | 0% | 100% | Large |
| Lot / Batch / Serial Tracking | 0% | 100% | Large |
| Stock Valuation (WAC/FIFO) | 0% | 100% | Large |
| Landed Cost Management | 0% | 100% | Medium |
| Vendor Statement Reconciliation | 0% | 100% | Medium |
| Cycle Counting | 0% | 100% | Medium |
| PO Change Order Management | 0% | 100% | Medium |
| PO Approval DoA Matrix | 10% | 100% | Medium |
| Vendor Diversity / Local Content | 0% | 100% | Medium |
| Supplier Collaboration on POs | 10% | 100% | Medium |
| Strategic Sourcing (RFx/Auctions) | 5% | 100% | Lower priority |
| Bill of Materials (BOM) | 0% | 100% | Lower priority |
| Vendor Managed Inventory (VMI) | 0% | 100% | Lower priority |
| Supply Chain Finance | 0% | 100% | Lower priority |
| Demand Forecasting / MRP | 0% | 100% | Lower priority |
| AI/ML Procurement Intelligence | 0% | 100% | Lower priority |
| GRA EFRIS / e-Invoicing | 0% | 100% | Lower priority |
| Sanctions / AML Screening | 0% | 100% | Lower priority |

---

## Cross-Module Gaps (ProcurementInventory ↔ other modules)

| Cross-Module Link | Current State | Gap |
|---|---|---|
| **Requisition → PO** | Listener creates draft PO on requisition approval (material/equipment only) | No `requisition_id` FK on PO table; no "Convert to PO" UI action; service/fund types not handled |
| **Finance → 3-Way Match** | PO approval → Finance AP invoice exists | GRN not validated against Finance invoice amount; no tolerance management |
| **Finance → Stock Valuation** | None | Stock on-hand has no GHS cost value → cannot appear on Balance Sheet |
| **Finance → Landed Costs** | None | Import duties/freight not captured → inventory value understated |
| **HR → Onboarding PO** | Listener creates draft onboarding PO | Auto-creates with no item_id set → no stock update after receiving |
| **Farms → Crop Cycle PO** | Listener creates draft PO on CropCycleStarted | No farm_produce_inventory link; no harvest reconciliation |
| **Fleet → Maintenance PO** | Listener creates draft PO on MaintenancePartsRequested | No fleet maintenance parts catalog; manual description only |
| **ManufacturingPaper/Water** | No integration | No BOM-driven purchasing; no raw material requirement planning |
| **Sales → Demand Planning** | No integration | No sales forecast → procurement demand signal |
| **Construction → Material PO** | Listener creates draft PO on ProjectPhaseApproved | No construction BOM; no project-level spending control |

---

## What Separates KharisERP Procurement from SAP Ariba / Coupa Level

| Dimension | KharisERP Current | SAP Ariba / Coupa / Oracle Level |
|---|---|---|
| **Process Coverage** | Requisition → PO → GRN → AP Invoice | Full S2P: Sourcing → Contracting → P2P → Invoicing → Payment → Analytics |
| **Supplier Management** | Vendor master (name, bank, contact, status) | 360° SIM: onboarding portal, qualification, performance KPIs, risk scores, diversity, certificates |
| **Sourcing** | Requisition module RFQ only | Structured RFx, eAuctions, savings tracking, TCO analysis, strategic category management |
| **Contract Management** | None | CLM: template authoring, obligation tracking, price compliance, AI extraction, renewal alerts |
| **Spend Visibility** | 8 KPI count stats on dashboard | Classified spend cube, maverick spend detection, benchmark intelligence, BI connectors |
| **Catalog / Guided Buying** | Item master list | Vendor punchout catalogs, preferred supplier routing, guided buying UX |
| **Invoice Automation** | Manual AP invoice from Finance module | AI OCR, SmartCoding, 3-way match, touchless processing, fraud detection |
| **Supplier Self-Service** | Read-only PO view in vendor portal | Full portal: PO acknowledgment, delivery updates, invoice submission, document compliance |
| **Compliance & Controls** | Audit trail on stock movements | Enforced SoD, DoA matrix, sanctions screening, fraud AI, policy engine |
| **Mobile** | Web-only (Filament responsive) | Native mobile apps, offline receiving, scan-to-receive, push approval notifications |
| **AI/ML** | None | Spend classification, risk prediction, copilot assistant, predictive reordering |
| **Inventory Sophistication** | Quantity-only by warehouse | Lot/batch/serial, WMS integration, demand forecasting, VMI, cycle counting, valuation |
| **ESG/Sustainability** | None | Carbon in sourcing decisions, supplier ESG scores, diversity spend tracking, regulatory reports |
| **Ghana-Specific Compliance** | Partial (GHS currency, Ghana as default country) | GRA EFRIS hook, NHIL/GETFund on POs, PPA local content compliance, sanctions screening |

---

## Implementation Roadmap

### Phase 1 — Operational must-haves (30 days)
1. **Requisition → PO Link** — add `requisition_id` FK to purchase_orders; "Convert to PO" action on approved requisitions
2. **PO Approval DoA Matrix** — threshold-based routing (amount-based, mirrors Requisition workflow rules); reuse `RequisitionWorkflowService` pattern
3. **Three-Way Match Validation** — when Finance AP invoice is created, validate total against PO total + GRN received amount; block if outside tolerance
4. **Vendor Performance Auto-Recording** — on GRN confirmation, calculate on-time %, quality rate, price variance; save to vendor_performance_records
5. **Spend Analytics Dashboard** — new report pages: spend by category, spend by vendor, PO aging report

### Phase 2 — Supplier & contract depth (60 days)
1. **Supplier Onboarding Portal** — vendor self-registration in VendorPanel; qualification questionnaire; approval workflow
2. **Vendor Certificate Tracking** — certificate type, expiry date, file upload, expiry alerts
3. **Contract / Blanket Order Management** — vendor contracts with line prices; contract-linked PO pricing
4. **Catalog / Price List per Vendor** — vendor-specific price lists; preferred vendor per item/category
5. **Quality Inspection Workflow** — inspection lots at GRN; quarantine; accept/reject/return-to-vendor

### Phase 3 — Inventory depth (90 days)
1. **Stock Valuation (WAC)** — unit_cost on stock movements; average cost calculation; Finance balance sheet integration
2. **Lot / Batch Tracking** — lot_number, expiry_date on GRN lines; FIFO stock movements
3. **Reorder Auto-Replenishment** — ReorderAlertCommand triggers requisition creation (not just email)
4. **Cycle Counting** — scheduled cycle count workflow; blind count; variance approval
5. **Landed Cost Capture** — freight/duty/customs fields on GRN; allocation to inventory valuation

### Phase 4 — Enterprise & compliance (ongoing)
1. **PO Change Orders** — formal change order workflow with re-approval above threshold
2. **Advance Shipment Notices** — vendor-submitted ASN in vendor portal
3. **Vendor Diversity / Local Content** — Ghana PPA compliance fields; local content reporting
4. **Bill of Materials (BOM)** — BOM model for Manufacturing and Construction modules
5. **Strategic Sourcing** — RFI/RFP events beyond Requisition RFQ; savings tracking
6. **AI/ML Foundation** — spend classification, anomaly detection, predictive reordering

---

## Quick Wins (Can Be Done This Week)

1. Add `barcode` field to `items` table (1 migration, minor item form update)
2. Add `requisition_id` FK to `purchase_orders` (1 migration, display-only in PO view page)
3. Add `on_time_deliveries` / `late_deliveries` / `total_orders` aggregation to Vendor model
4. Wire existing `GoodsReceiptResource` "Confirm" action to fire quality inspection check
5. Add "Spend by Vendor" section to ProcurementDashboard blade view (pure SQL aggregation, no new tables)
6. Add `vendors.is_local` + `vendors.diversity_class` fields (1 migration, dropdown on vendor form)
7. Enable ReorderAlertCommand to auto-create a draft Requisition (extend existing command, no new tables)
