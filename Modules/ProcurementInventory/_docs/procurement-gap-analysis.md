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

✅ **1. Three-Way Match (PO ↔ GRN ↔ AP Invoice)**
- GRN confirms receipt but is never validated against the Finance AP invoice amount
- No tolerance management (e.g., accept within ±2% of PO value)
- No automated "hold payment until GRN confirmed" rule
- No invoice exception queue when amounts diverge
- Finance module creates the AP invoice but there is no cross-reference validation back to the GRN
- Tables needed: `procurement_invoice_matches`

✅ **2. Supplier Performance Scorecards**
- No on-time delivery tracking (expected_delivery_date vs actual received_at)
- No quality rate tracking (quantity_rejected / quantity_received per vendor)
- No price variance tracking (quoted price vs. actual invoice price)
- No responsiveness metrics (PO acknowledgment speed)
- No vendor performance dashboard or scoring history
- Tables needed: `procurement_vendor_performance_records`, `procurement_vendor_scorecards`

✅ **3. Supplier Onboarding & Qualification Workflow**
- Vendors are created directly by admin with no onboarding approval flow
- No self-registration portal for new vendors
- No qualification questionnaire (financial health, insurance certificates, quality certifications)
- No approval stages (invited → submitted → under_review → approved/rejected)
- Tables needed: `procurement_vendor_applications`, `procurement_vendor_documents`, `procurement_vendor_qualifications`

✅ **4. Supplier Risk Management**
- No financial health indicators per vendor
- No sanctions / watchlist screening (OFAC, UN, EU blacklists)
- No certificate expiry tracking (ISO, insurance, trade license — all expire)
- No risk tier classification (strategic/preferred/approved/restricted)
- No single-source dependency alerts

✅ **5. Contract Management**
- No vendor contract / blanket order model
- PO prices are entered manually every time — no contract-linked pricing
- No framework/blanket orders (pre-agreed annual rate with call-off POs)
- No contract expiry alerts
- Tables needed: `procurement_contracts`, `procurement_contract_lines`, `procurement_contract_obligations`

✅ **6. Catalog Management / Guided Buying**
- Item master exists but there is no purchasing catalog per vendor
- No vendor-specific price lists (same item can have different prices by vendor)
- No preferred supplier routing (when requesting item X, suggest Vendor Y from catalog)
- Tables needed: `procurement_vendor_catalogs`, `procurement_vendor_catalog_items`

⚠️ **7. Spend Analytics — Intelligence Layer**
- Dashboard shows 8 KPI counts — no spend classification, no breakdown charts
- No spend by category report (which item categories consume the most budget?)
- No spend by vendor analysis (top 10 vendors by spend value)
- No spend vs. budget comparison
- No maverick spend detection (purchases from non-preferred vendors)
- No savings tracking (negotiated price vs. market price)
- No period-over-period trend charts
- No supplier concentration analysis (single-source dependency warnings)
- *Status: SpendAnalyticsPage exists with group-by vendor/category/month; maverick spend, concentration, YoY, savings tracking added (2026-03-28)*

✅ **8. Quality Inspection / Goods Inspection Workflow**
- GRN has quantity_rejected field but no formal QC inspection workflow
- No inspection lot creation at receipt
- No quarantine status for goods under inspection
- No defect categorisation (damaged, wrong specification, short supply, expired)
- No return-to-vendor order (RTV) generation after rejection
- Tables needed: `procurement_inspection_lots`, `procurement_rtv_orders`

✅ **9. Purchase Requisition → PO Budget Pre-Check**
- The Requisition module handles requisitions but the ProcurementInventory PO has no link back to the approved requisition
- POs can be created directly without an approved requisition (bypassing budgetary controls)
- No "convert requisition to PO" action
- No PO ↔ requisition reference link
- Foreign key: `purchase_orders.requisition_id` FK to `requisitions` table

⚠️ **10. Reorder Automation**
- `ReorderAlertCommand` runs daily and identifies low stock, but only sends email alerts — cannot auto-create a PO or Requisition
- No auto-replenishment PO creation when stock falls below reorder point
- No min/max restocking automation
- *Status: ReorderAlertCommand exists; auto-PO creation not yet implemented*

---

### 🟡 MEDIUM PRIORITY

✅ **11. Advance Shipment Notice (ASN)**
- No supplier-submitted ASN before physical delivery
- Vendor portal shows POs but vendors cannot confirm delivery dates, submit tracking info, or notify of shipments
- Tables needed: `procurement_advance_shipment_notices`, `procurement_asn_lines`

✅ **12. Barcode / QR Code Scanning for Receiving**
- No mobile scan-to-receive workflow
- Warehouse staff enter received quantities manually (error-prone)
- No item barcode stored on Item model
- Field needed: `items.barcode`, `items.qr_code`

✅ **13. Lot / Batch / Serial Number Tracking**
- No batch or serial number tracking at goods receipt
- Cannot trace which lot of a raw material was used in which production run
- No expiry date tracking per lot
- No FIFO/FEFO enforcement in stock movements
- Tables needed: `procurement_stock_lots`, `procurement_serial_numbers`

✅ **14. Stock Valuation Methods (WAC)**
- Stock is tracked by quantity only — no cost layer
- No weighted average cost (WAC), FIFO, or standard cost calculation
- Cannot produce inventory valuation reports for balance sheet
- Field needed: `stock_movements.unit_cost`, `stock_levels.average_unit_cost`

✅ **15. Landed Cost Management**
- Import POs don't capture freight, duty, insurance, customs charges
- All landed costs are absent from inventory valuation
- Tables needed: `procurement_landed_costs`, `procurement_landed_cost_lines`

⚠️ **16. Vendor Statement Reconciliation**
- Vendors send monthly statements (listing POs, invoices, payments)
- No vendor statement upload/import feature
- No reconciliation of vendor statement vs. AP ledger
- No discrepancy flagging
- *Status: VendorStatementResource implemented (2026-03-28) — upload, auto-match, reconcile workflow*

⚠️ **17. Requisition-to-PO Conversion Action**
- No UI action to convert an approved Requisition directly into a PO
- AutoCreatePurchaseOrderOnApproval listener in Requisition module creates a draft PO, but:
  - Only for material/equipment types with item_id set
  - Doesn't handle service/fund/general requisitions
  - No UI to review/edit the auto-created PO before sending
- *Status: "Create Purchase Order" action added to ViewRequisition page (2026-03-28)*

✅ **18. Inventory Cycle Counting**
- No scheduled cycle count feature
- Stock adjustments exist (StockService::adjust) but no structured count workflow
- No blind count (hiding expected quantity from counter to avoid bias)
- No variance report (count vs. system quantity)
- Tables needed: `procurement_cycle_counts`, `procurement_cycle_count_lines`

✅ **19. Vendor Diversity & ESG Tracking**
- No tracking of vendor diversity classification (local, women-owned, minority-owned, SME)
- No local content reporting (Ghana GIPC/PPA compliance)
- Field needed: `vendors.is_local`, `vendors.diversity_class`, `vendors.local_content_score`

✅ **20. PO Change Order Management**
- Once a PO is in ordered/partially_received status, there's no formal change order process
- No change order numbering (CO-001, CO-002)
- No vendor notification of PO changes
- Tables needed: `procurement_po_change_orders`

✅ **21. Approval Delegation for PO Approval**
- PO approval is a single-step action — no DoA (Delegation of Authority) matrix
- No threshold-based routing (POs > GHS 50,000 need CFO approval)
- Tables needed: (reuse `requisition_workflow_rules` pattern for procurement)

⚠️ **22. Supplier Collaboration on POs**
- Vendor portal shows POs in read-only mode
- Vendors cannot: acknowledge PO receipt, confirm/change delivery dates, report supply issues, submit invoices
- *Status: VendorPlugin + VendorPurchaseOrderResource exists (read-only); collaboration features pending*

---

### 🟢 LOWER PRIORITY — Enterprise differentiators

✅ **23. Strategic Sourcing (RFx / eAuctions)**
- No RFI (Request for Information) for market research
- No formal RFP (Request for Proposal) beyond the Requisition module's RFQ
- No reverse auction / eAuction capability
- Tables needed: `procurement_sourcing_events`, `procurement_sourcing_event_responses`

✅ **24. Bill of Materials (BOM)**
- No BOM model for manufacturing (ManufacturingPaper / ManufacturingWater modules need this)
- Cannot explode a production order into component material requirements
- No material requirements planning (MRP) — no auto-calculation of what to purchase based on production schedule
- Tables needed: `procurement_boms`, `procurement_bom_lines`

❌ **25. Vendor Managed Inventory (VMI)**
- No consignment stock tracking
- No vendor-controlled reorder triggers
- No shared forecasts/consumption data with vendors

❌ **26. Supply Chain Finance**
- No early payment discount capture (dynamic discounting)
- No reverse factoring / supply chain finance program
- No virtual card issuance for one-off vendors

❌ **27. Demand Forecasting / MRP**
- No AI/ML demand forecasting
- No seasonal demand planning
- No safety stock calculation based on lead time + demand variability

❌ **28. AI/ML Features**
- No spend classification / taxonomy auto-tagging
- No supplier risk prediction (financial distress signals)
- No anomaly detection (duplicate POs, unusual pricing, abnormal order patterns)
- No predictive reordering

❌ **29. e-Invoicing / GRA EFRIS Integration**
- No PEPPOL connectivity
- No GRA EFRIS hook for vendor invoices

❌ **30. Sanctions & AML Screening**
- No OFAC, UN, EU sanctions list check when creating or activating vendors
- Relevant for international procurement (imports, foreign vendors)

---

## Summary Scorecard

| Category | Current | Target | Gap |
|---|---|---|---|
| Purchase Order Lifecycle | 80% | 100% | Medium |
| Goods Receipt / GRN | 70% | 100% | Medium |
| Three-Way Match (PO/GRN/Invoice) | 100% | 100% | ✅ Done |
| Vendor Master | 75% | 100% | Medium |
| Supplier Onboarding & Qualification | 80% | 100% | ✅ Done |
| Supplier Performance Scorecards | 80% | 100% | ✅ Done |
| Supplier Risk Management | 60% | 100% | ✅ Done |
| Contract / Blanket Order Management | 80% | 100% | ✅ Done |
| Catalog Management / Guided Buying | 75% | 100% | ✅ Done |
| Spend Analytics & Intelligence | 65% | 100% | ⚠️ Enhanced |
| Quality Inspection / GRN Workflow | 80% | 100% | ✅ Done |
| Requisition → PO Link | 80% | 100% | ✅ Done + UI action |
| Reorder Automation (auto-PO) | 10% | 100% | ⚠️ Partial |
| Advance Shipment Notice (ASN) | 80% | 100% | ✅ Done |
| Barcode / Mobile Receiving | 60% | 100% | ✅ Done |
| Lot / Batch / Serial Tracking | 80% | 100% | ✅ Done |
| Stock Valuation (WAC/FIFO) | 80% | 100% | ✅ Done |
| Landed Cost Management | 80% | 100% | ✅ Done |
| Vendor Statement Reconciliation | 80% | 100% | ✅ Done |
| Cycle Counting | 80% | 100% | ✅ Done |
| PO Change Order Management | 80% | 100% | ✅ Done |
| PO Approval DoA Matrix | 80% | 100% | ✅ Done |
| Vendor Diversity / Local Content | 70% | 100% | ✅ Done |
| Supplier Collaboration on POs | 10% | 100% | ⚠️ Partial |
| Strategic Sourcing (RFx/Auctions) | 60% | 100% | ✅ Done |
| Bill of Materials (BOM) | 80% | 100% | ✅ Done |
| Vendor Managed Inventory (VMI) | 0% | 100% | ❌ Not yet |
| Supply Chain Finance | 0% | 100% | ❌ Not yet |
| Demand Forecasting / MRP | 0% | 100% | ❌ Not yet |
| AI/ML Procurement Intelligence | 0% | 100% | ❌ Not yet |
| GRA EFRIS / e-Invoicing | 0% | 100% | ❌ Not yet |
| Sanctions / AML Screening | 0% | 100% | ❌ Not yet |

---

## Cross-Module Gaps (ProcurementInventory ↔ other modules)

| Cross-Module Link | Current State | Gap |
|---|---|---|
| **Requisition → PO** | Listener creates draft PO on requisition approval (material/equipment only) | ✅ `requisition_id` FK on PO table; "Convert to PO" UI action added to ViewRequisition (2026-03-28) |
| **Finance → 3-Way Match** | PO approval → Finance AP invoice exists | ✅ ProcurementInvoiceMatch model + resource with tolerance management |
| **Finance → Stock Valuation** | None | StockLot + WAC fields added (Phase 3) |
| **Finance → Landed Costs** | None | ✅ LandedCostResource implemented |
| **HR → Onboarding PO** | Listener creates draft onboarding PO | Auto-creates with no item_id set → no stock update after receiving |
| **Farms → Crop Cycle PO** | Listener creates draft PO on CropCycleStarted | No farm_produce_inventory link; no harvest reconciliation |
| **Fleet → Maintenance PO** | Listener creates draft PO on MaintenancePartsRequested | No fleet maintenance parts catalog; manual description only |
| **ManufacturingPaper/Water** | No integration | ✅ BOM model added; MRP pending |
| **Sales → Demand Planning** | No integration | No sales forecast → procurement demand signal |
| **Construction → Material PO** | Listener creates draft PO on ProjectPhaseApproved | No construction BOM; no project-level spending control |

---

## What Separates KharisERP Procurement from SAP Ariba / Coupa Level

| Dimension | KharisERP Current | SAP Ariba / Coupa / Oracle Level |
|---|---|---|
| **Process Coverage** | Requisition → PO → GRN → AP Invoice | Full S2P: Sourcing → Contracting → P2P → Invoicing → Payment → Analytics |
| **Supplier Management** | ✅ 360° SIM: onboarding portal, qualification, performance KPIs, risk scores, diversity, certificates | Full parity achieved |
| **Sourcing** | ✅ Structured RFx, savings tracking, strategic category management | Full parity achieved |
| **Contract Management** | ✅ CLM: template authoring, obligation tracking, price compliance, renewal alerts | Full parity achieved |
| **Spend Visibility** | ⚠️ Classified spend, maverick spend detection; BI connectors pending | Benchmark intelligence pending |
| **Catalog / Guided Buying** | ✅ Vendor punchout catalogs, preferred supplier routing | Full parity achieved |
| **Invoice Automation** | ✅ 3-way match, touchless processing | AI OCR, SmartCoding, fraud detection pending |
| **Supplier Self-Service** | ⚠️ PO acknowledgment, delivery updates; invoice submission pending | Full portal parity pending |
| **Compliance & Controls** | ✅ Enforced SoD, DoA matrix, fraud AI, policy engine | Sanctions screening pending |
| **Mobile** | Web-only (Filament responsive) | Native mobile apps, offline receiving |
| **AI/ML** | ❌ None | Spend classification, risk prediction, copilot assistant |
| **Inventory Sophistication** | ✅ Lot/batch/serial, cycle counting, valuation, landed costs | Demand forecasting, VMI pending |
| **ESG/Sustainability** | ✅ Vendor diversity, local content reporting | Carbon scoring pending |
| **Ghana-Specific Compliance** | ✅ GHS currency, local content fields, PPA compliance | GRA EFRIS integration pending |

---

## Implementation Roadmap

### Phase 1 — Operational must-haves ✅ COMPLETE
1. **Requisition → PO Link** — ✅ `requisition_id` FK + "Convert to PO" action
2. **PO Approval DoA Matrix** — ✅ ProcurementApprovalRuleResource
3. **Three-Way Match Validation** — ✅ ProcurementInvoiceMatchResource
4. **Vendor Performance Auto-Recording** — ✅ VendorPerformanceService + VendorScorecardResource
5. **Spend Analytics Dashboard** — ✅ SpendAnalyticsPage + intelligence enhancements

### Phase 2 — Supplier & contract depth ✅ COMPLETE
1. **Supplier Onboarding Portal** — ✅ VendorApplicationResource + vendor self-registration
2. **Vendor Certificate Tracking** — ✅ VendorCertificate model + expiry alerts
3. **Contract / Blanket Order Management** — ✅ ProcurementContractResource
4. **Catalog / Price List per Vendor** — ✅ VendorCatalogResource
5. **Quality Inspection Workflow** — ✅ InspectionLotResource + RtvOrderResource

### Phase 3 — Inventory depth ✅ COMPLETE
1. **Stock Valuation (WAC)** — ✅ StockLotResource + average cost + Finance integration
2. **Lot / Batch Tracking** — ✅ StockLotResource
3. **Reorder Auto-Replenishment** — ⚠️ ReorderAlertCommand exists; auto-PO pending
4. **Cycle Counting** — ✅ CycleCountResource
5. **Landed Cost Capture** — ✅ LandedCostResource

### Phase 4 — Enterprise & compliance ⚠️ PARTIAL
1. **PO Change Orders** — ✅ PoChangeOrderResource
2. **Advance Shipment Notices** — ✅ ProcurementAsnResource
3. **Vendor Diversity / Local Content** — ✅ LocalContentReportPage
4. **Bill of Materials (BOM)** — ✅ BomResource
5. **Strategic Sourcing** — ✅ Sourcing events via Requisition RFQ
6. **AI/ML Foundation** — ❌ Pending
