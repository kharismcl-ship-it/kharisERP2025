# KharisERP Requisition Module — Gap Analysis vs. World-Class Procurement Systems
> Generated: 2026-03-28 | Based on: SAP Ariba, Coupa, Oracle Procurement Cloud, Jaggaer, Ivalua, Basware, Kissflow Procurement

---

## What You Already Have (Strong Foundation)

| Area | Status |
|---|---|
| Requisition creation with 6 request types (material/fund/general/equipment/service/other) | ✅ Complete |
| 8-stage status workflow (draft → submitted → under_review → pending_revision → approved → rejected → fulfilled → closed) | ✅ Complete |
| Sequential multi-approver chain (role: reviewer/approver, with order + decision) | ✅ Complete |
| Digital signature capture on approver decisions | ✅ Complete |
| Requisition templates with pre-filled items | ✅ Complete |
| File attachments per requisition (mime/size tracking) | ✅ Complete |
| Multi-channel notifications (email/SMS/WhatsApp/in-app via CommunicationCentre) | ✅ Complete |
| Cross-company requisitions (target_company_id + target_department_id) | ✅ Complete |
| Budget tracking per cost centre (budget_amount + budgetOverage()) | ✅ Complete |
| Auto-PO creation on approval (material/equipment with catalog items → draft PurchaseOrder) | ✅ Complete |
| Audit trail (RequisitionActivity log: action, from_status→to_status, user, timestamp) | ✅ Complete |
| Shared parties system (for_info / for_action / for_approval per employee or department) | ✅ Complete |
| Urgency escalation command (overdue → auto-bump urgency daily at 06:00) | ✅ Complete |
| Item-level vendor quote fields (vendor_name, vendor_quote_ref, vendor_unit_price) | ✅ Complete |
| Item fulfillment tracking (fulfilled_quantity, fulfilmentPercentage()) | ✅ Complete |
| Staff portal self-service (MyRequisitionResource scoped to employee) | ✅ Complete |
| Commentions integration (threaded comments on requisitions) | ✅ Complete |
| Dashboard with 6 KPI stats | ✅ Partial |
| Procurement catalog link (item_id FK to ProcurementInventory items) | ✅ Partial |
| Preferred vendor per requisition | ✅ Partial |

---

## The Gaps — Prioritised

### 🔴 HIGH PRIORITY — Blockers for enterprise-grade procurement

**1. Flexible Approval Workflow Engine**
- Current model is strictly sequential (approver 1 → 2 → 3)
- No conditional routing (e.g., "if total > GHS 5,000 add CFO approval")
- No parallel approvals (department head AND finance at the same time)
- No delegation (when approver is on leave, route to delegate)
- No approval templates (pre-defined chains per department, cost centre, or amount tier)
- No SLA timers on each approval step with auto-escalation
- No "approve on behalf" with proper audit trail
- Tables needed: `requisition_workflow_rules`, `requisition_workflow_templates`

**2. Spend Control & Policy Enforcement**
- No threshold-based routing rules (amount, category, vendor type)
- No category-level spend policies (e.g., "all IT hardware > GHS 2,000 needs CTO sign-off")
- No vendor blacklist enforcement at submission time
- No over-budget hard block (only soft budgetOverage() calculation — no UI warning)
- No segregation-of-duties enforcement (requester can also be approver in current model)
- No spend caps per employee or department per period

**3. Vendor Comparison & RFQ (Request for Quotation)**
- Only one preferred_vendor_id per requisition — no multi-vendor comparison
- No RFQ creation from a requisition (invite N vendors to quote)
- No bid collection and comparison matrix (price/delivery/quality scoring)
- No award decision with justification trail
- No contract-linked pricing (catalog prices should come from active vendor contracts)
- Tables needed: `requisition_rfqs`, `requisition_rfq_bids`

**4. Goods Receipt & 3-Way Match**
- Fulfillment tracking only updates fulfilled_quantity — no formal GRN record
- No Goods Receipt Note (GRN) creation from a requisition/PO
- No acceptance/rejection workflow for received goods (quality inspection step)
- No split deliveries (partial shipments against one PO line)
- No 3-way match (PO ↔ GRN ↔ Vendor Invoice) — critical for AP payment authorisation
- No return-to-vendor workflow

**5. Duplicate Requisition Detection**
- No check at submission for existing similar/identical open requisitions
- No "potential duplicate" warning (same item, same department, within same period)
- No merge requisitions feature
- Leads to duplicate POs and wasted spend — high audit risk

**6. Spend Analytics & Reporting**
- Dashboard only shows 6 count-based KPIs (no spend amounts, no trends)
- No spend by category report
- No spend by vendor report
- No spend by department / cost centre report
- No budget vs. actual variance by cost centre
- No savings tracking (negotiated price vs. catalog price)
- No supplier concentration risk (single-vendor dependency warning)
- No maverick spend detection (purchases bypassing procurement)
- No cycle time analysis (average days by stage)

**7. REST API & Webhook Integration**
- No REST API endpoints for requisition CRUD
- No webhook callbacks (external systems can't subscribe to status events)
- No import (CSV/XML) for bulk requisition creation
- Prevents integration with field-management apps, ERPs, or BI tools

**8. Recurring Requisitions**
- No schedule-based auto-creation of requisitions (e.g., monthly office supplies)
- Recurring templates exist but must be manually triggered each time
- No "recurring" flag with cron schedule on a requisition
- Table needed: `requisition_schedules`

---

### 🟡 MEDIUM PRIORITY

**9. Requisition Cancellation Workflow**
- No explicit "Cancel" action with reason — only admin can change status manually
- Staff cannot recall/cancel their own submitted requisition
- No "withdrawal" state with notification to approvers
- Status 'closed' is the only terminal state besides rejected/fulfilled

**10. Clone / Reorder from History**
- No "Clone this Requisition" action from the list or view page
- Replicate policy method exists but no UI action wired
- Staff must re-enter all fields for repeat requests
- No "order again" from fulfilled requisition history

**11. Custom Fields per Requisition Type**
- All request types share the same hard-coded schema
- A "Service" requisition has no SOW/deliverables field
- An "Equipment" requisition has no asset tag / serial number field
- A "Fund" requisition has no payment destination / beneficiary field
- No company-level custom fields configuration
- Table needed: `requisition_custom_fields`, `requisition_custom_field_values`

**12. Requisition Splitting & Consolidation**
- No way to split one requisition into multiple POs (different vendors per item)
- No consolidation of multiple requisitions into a single PO (bulk purchasing)
- Currently one requisition → one PO (auto-create logic is all-or-nothing)

**13. Cost Allocation Across Multiple Cost Centres**
- Only one cost_centre_id per requisition
- Cannot split cost across departments (common for shared services)
- No project-level cost coding
- No GL account code at item level

**14. Approval History & Diff View**
- Activity log captures status changes but no field-level diff
- Cannot see what changed between "submitted" and "pending_revision"
- No version snapshots (before/after edit views)

**15. Email-Based Approval (Approve/Reject from Email)**
- Approvers must log into the system to act on a requisition
- World-class tools (Coupa, Kissflow) support approve/reject directly from email notification
- No signed email token for one-click decisions

**16. Bulk Submission & Import**
- No bulk requisition import (CSV/Excel template)
- Cannot create 50 requisitions at once from a spreadsheet
- No API for batch creation

**17. Advanced Search & Filtering**
- Text search is limited (no full-text search across items, descriptions, vendors)
- No saved filter presets per user
- No global search across all requisition fields
- Date range filters are basic (no fiscal period awareness)

**18. Requisition Dashboard — Depth**
- Only count-based KPIs — no spend amounts
- No chart breakdown by type, urgency, department
- No drill-down into pending approvals
- No "my pending approvals" widget (for managers)
- No time-to-approve trend chart

**19. Notification Reminders & Escalation**
- Notifications fire on status change but no reminder if approver doesn't act
- No configurable reminder rules (e.g., send again at +24h, +48h if no decision)
- No escalation to next-in-chain if SLA breached
- Table needed: `requisition_reminder_rules`

**20. Document Version Control**
- Attachments are append-only — no versioning
- Cannot replace an attachment with a newer version
- No primary/supporting document classification beyond label field

---

### 🟢 LOWER PRIORITY — Enterprise differentiators

**21. Vendor Portal Self-Service**
- Vendors cannot log in to view their awarded requisitions / POs
- No vendor acknowledgement of PO receipt
- No vendor delivery schedule updates
- VendorPanel exists for ProcurementInventory POs but not linked to Requisition flow

**22. Approval Delegation Calendar**
- No out-of-office delegation with start/end date
- Cannot pre-schedule delegation (e.g., "delegate to X from Monday to Friday")
- No delegation chain (if delegate also absent, who gets it?)

**23. Compliance & eSignature**
- Signature pad captures base64 image but no certificate, no timestamp binding
- Not compliant with EU eIDAS, Ghana Electronic Transactions Act, or similar
- No immutable signature log (stored as longText, editable)

**24. Mobile App / PWA**
- Filament responsive web only — no native mobile
- Field workers (construction, farm) cannot easily submit on mobile
- No barcode/QR scan for item lookup
- No offline draft creation

**25. Supplier Onboarding from Requisition**
- Cannot initiate vendor registration request from a requisition
- No "invite new vendor" flow when preferred vendor doesn't exist in the system

**26. Intercompany Billing from Requisitions**
- Cross-company requisitions don't auto-generate intercompany invoices
- No cost allocation entry in the Finance module when target_company differs

**27. AI / Smart Features**
- No suggested items based on past orders
- No anomaly detection (unusual quantity, price, or vendor)
- No predicted approval time based on history

**28. Sustainability / ESG Tracking**
- No carbon footprint calculation per requisition
- No preferred supplier diversity tracking (local, women-owned, minority-owned)
- No ESG scoring on vendor selection

---

## Summary Scorecard

| Category | Current | Target | Gap |
|---|---|---|---|
| Requisition Creation & Types | 75% | 100% | Medium |
| Approval Workflow Engine | 40% | 100% | **Critical** |
| Spend Policy Enforcement | 15% | 100% | **Critical** |
| Vendor Comparison / RFQ | 5% | 100% | **Critical** |
| Goods Receipt & 3-Way Match | 20% | 100% | **Critical** |
| Duplicate Detection | 0% | 100% | **Critical** |
| Spend Analytics & Reporting | 15% | 100% | **Critical** |
| REST API / Integration | 0% | 100% | **Critical** |
| Recurring Requisitions | 10% | 100% | Large |
| Cancellation Workflow | 20% | 100% | Large |
| Clone / Reorder | 10% | 100% | Medium |
| Custom Fields per Type | 0% | 100% | Medium |
| Requisition Splitting / Consolidation | 0% | 100% | Medium |
| Multi Cost Centre Allocation | 10% | 100% | Medium |
| Email-Based Approval | 0% | 100% | Medium |
| Notification Reminders & Escalation | 20% | 100% | Medium |
| Bulk Import / API | 0% | 100% | Medium |
| Dashboard Depth | 30% | 100% | Medium |
| Document Version Control | 10% | 100% | Low |
| Vendor Portal Integration | 5% | 100% | Lower priority |
| Approval Delegation | 0% | 100% | Lower priority |
| eSignature Compliance | 15% | 100% | Lower priority |
| Mobile / PWA | 0% | 100% | Lower priority |
| AI / Smart Features | 0% | 100% | Lower priority |

---

## Implementation Roadmap

### Phase 1 — Operational must-haves (unblock daily procurement)
1. **Approval Workflow Rules** — threshold-based conditional routing (amount / cost centre / type)
2. **Spend Policy Enforcement** — budget hard-block at submission, SOD check (requester ≠ approver)
3. **Requisition Cancellation** — staff can withdraw; approvers notified; status = "cancelled"
4. **Clone Requisition** — "Reorder" action on fulfilled/approved requisitions
5. **Spend Analytics Dashboard** — spend by category/vendor/department; avg cycle time

### Phase 2 — Procurement depth
1. **RFQ / Vendor Quote Comparison** — invite N vendors, collect bids, award with justification
2. **Goods Receipt Note (GRN)** — formal receipt linked to PO, quantity accepted/rejected
3. **3-Way Match** — PO ↔ GRN ↔ Vendor Invoice auto-reconciliation
4. **Recurring Requisitions** — schedule-based auto-create with configurable frequency
5. **Notification Reminders** — configurable reminder rules with escalation chains

### Phase 3 — Self-service & integration
1. **REST API** — full CRUD + status webhook endpoints
2. **Custom Fields** — per-type field definitions (SOW for services, serial for equipment)
3. **Multi Cost Centre Allocation** — split cost across departments per item
4. **Email-Based Approval** — one-click approve/reject from notification email
5. **Bulk Import** — CSV/Excel template for bulk requisition creation

### Phase 4 — Enterprise & compliance
1. **Approval Delegation Calendar** — out-of-office delegation with date range
2. **eSignature Compliance** — timestamp-bound, certificate-linked signatures
3. **Vendor Portal** — vendor acknowledgement + delivery updates from VendorPanel
4. **Intercompany Billing** — auto-generate intercompany invoice when target_company ≠ requester_company
5. **Mobile PWA** — offline draft, barcode scan, push notifications