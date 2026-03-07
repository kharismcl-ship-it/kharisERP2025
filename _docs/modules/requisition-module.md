# Requisition Module — Reference Document

**Last updated:** 2026-03-05
**Module path:** `Modules/Requisition`
**Status:** Built (85%)

---

## Purpose

The Requisition module is a **cross-company purchase/resource request system**. Any employee in any tenant company can raise a request — either within their own company or directed to another tenant company in the application. It manages the full lifecycle from draft → fulfilment with approval workflows, document attachments, party notifications, and a full activity audit trail.

---

## Cross-Company Architecture

| Field | Meaning |
|---|---|
| `company_id` | The requester's company (auto-set by BelongsToCompany / TenantScope) |
| `target_company_id` | The company being requested FROM (optional — for cross-company requests) |
| `target_department_id` | Department within the target company |

### Visibility Rule
- In the **admin panel**: all requisitions visible
- In the **company-admin panel (tenant)**: users see requisitions where `company_id = current_tenant` OR `target_company_id = current_tenant`
- This is enforced via `RequisitionResource::getEloquentQuery()` override

---

## Request Types

| Type | Cost Calculation | Notes |
|---|---|---|
| `material` | Auto-sum from items | Items with unit_cost get summed |
| `equipment` | Auto-sum from items | Items with unit_cost get summed |
| `fund` | Manual entry | No items required |
| `service` | Manual entry | Items optional |
| `general` | Manual entry | No cost required at all |
| `other` | Manual entry | Catch-all |

**Important:** Cost calculation is optional. General requests may have zero estimated cost.
`total_estimated_cost` auto-updates from `SUM(requisition_items.total_cost)` only when items have `unit_cost` set. Otherwise remains manually editable.

---

## Status Workflow

```
[draft]
    ↓  Submit action
[submitted]
    ↓  Auto-notify Approver #1 (sequential by order)
[under_review]
    ↓  Reviewer annotates / escalates
         ↓  Return for Revision
[pending_revision]  →  Requester edits  →  Re-submit  →  [submitted]
         ↓  Escalate
[approved]  ──────── (future: auto-create PO for material/equipment)
[rejected]
[fulfilled]  ←  staff marks complete, items delivered
[closed]    ←  (future: archived after confirmation)
```

---

## Database Tables

| Table | Description |
|---|---|
| `requisitions` | Main request record |
| `requisition_items` | Line items (with optional catalog link) |
| `requisition_approvers` | Approvers/reviewers with decisions and signatures |
| `requisition_parties` | Parties notified (employees or entire departments) |
| `requisition_attachments` | Supporting documents (invoices, quotes, specs) |
| `requisition_activities` | Immutable audit log of all actions |

---

## Models & Key Fields

### Requisition
- `company_id` — requester's company (FK → companies)
- `requester_employee_id` — who raised it (FK → hr_employees)
- `target_company_id` — target company (FK → companies, nullable)
- `target_department_id` — target department (FK → hr_departments, nullable)
- `reference` — auto-generated `REQ-YYYYMM-00001`
- `request_type` — enum: material, fund, general, equipment, service, other
- `urgency` — enum: low, medium, high, urgent
- `status` — enum: draft, submitted, under_review, pending_revision, approved, rejected, fulfilled
- `cost_centre_id` — FK → finance.cost_centres (nullable)
- `total_estimated_cost` — decimal, auto-summed from items or manual
- `due_by` — optional SLA date
- `approved_by`, `approved_at`, `fulfilled_at`, `rejection_reason`, `notes`

### RequisitionItem
- `requisition_id`, `item_id` (FK → procurement_items, nullable)
- `description`, `quantity`, `unit`, `unit_cost`, `total_cost` (auto-calculated)
- On save: `total_cost = quantity × unit_cost`
- On save/delete: recalculates parent `total_estimated_cost` if items have costs

### RequisitionApprover
- `requisition_id`, `employee_id`
- `role` — reviewer | approver
- `order` — sequence number (1 = first to be notified)
- `is_active` — whether this approver slot is currently active
- `decision` — pending | approved | rejected | commented
- `decided_at`, `comment`, `signature` (SignaturePad)

### RequisitionParty
- `requisition_id`
- `party_type` — employee | department
- `employee_id` (nullable), `department_id` (nullable)
- `reason` — for_info | for_action | for_approval
- `notified_at` — when the notification was dispatched

### RequisitionAttachment
- `requisition_id`
- `uploaded_by_user_id` (FK → users)
- `label` — human label e.g. "Vendor Invoice", "Technical Spec"
- `file_path`, `file_name`, `mime_type`, `file_size`

### RequisitionActivity
- `requisition_id`, `user_id`, `employee_id`
- `action` — requisition_created, status_changed, item_added, item_updated, item_removed, approver_added, party_added, attachment_uploaded, attachment_removed
- `from_status`, `to_status` (for status_changed)
- `description`, `meta` (json)
- **No updated_at** — immutable, insert-only

---

## Events & Listeners

| Event | Trigger | Listener |
|---|---|---|
| `RequisitionStatusChanged` | Model boot `updating` when status dirty | `NotifyRequisitionStatusChanged` → email requester |
| `RequisitionShared` | Approver added via RM | `NotifyRequisitionShared` → email approver |
| `RequisitionPartyAdded` | Party added via RM | `NotifyRequisitionPartyAdded` → email employee or all dept employees |

---

## Communication Templates

| Code | Channel | Purpose |
|---|---|---|
| `requisition_status_changed` | email | Notify requester of status change |
| `requisition_shared_with_you` | email | Notify approver they were added |
| `requisition_party_notified` | email | Notify party they are cc'd on a request |

Variables: `reference`, `title`, `status`, `requester`, `role`, `reason`

---

## Filament Resources

### RequisitionResource
- Navigation group: Requisitions, sort 1
- Infolist with CommentsEntry (Commentions) at bottom
- Cross-company query override (shows requests TO current tenant too)
- Row actions: Submit, Approve, Reject, Return for Revision, View, Edit, Delete

### Relation Managers (5)
1. `RequisitionItemsRelationManager` — line items with catalog picker
2. `RequisitionApproversRelationManager` — approvers with order + signature
3. `RequisitionPartiesRelationManager` — parties (employee or department)
4. `RequisitionAttachmentsRelationManager` — document uploads
5. `RequisitionActivitiesRelationManager` — read-only audit timeline

### Widgets (registered in plugin)
- `RequisitionStatsWidget` — pending, approved, rejected, fulfilled counts + avg resolution time
- `RequisitionChartWidget` — monthly trend (submitted vs fulfilled, 6 months)

---

## Module Integrations

| Module | How |
|---|---|
| HR | `Employee` (requester, approver, party), `Department` (target, party) |
| Finance | `CostCentre` (budget tracking) |
| ProcurementInventory | `Item` (catalog link on requisition items) |
| CommunicationCentre | `CommunicationService::sendFromTemplate()` for all notifications |

---

## Future Enhancements (Not Yet Built)

1. **Budget enforcement** — check CostCentre allocated budget vs `total_estimated_cost` on submission
2. **PO auto-creation** — on approval of material/equipment, auto-create PO in ProcurementInventory
3. **Requisition templates** — save/reuse common request types
4. **Partial fulfilment** — per-item fulfilment tracking with `fulfilled_quantity`
5. **Vendor quotes** — `vendor_name`, `quote_reference` per item
6. **SLA escalation** — auto-escalate urgency when `due_by` passes without action
7. **Closed status** — archived after fulfilment confirmed

---

## Known Constraints

- `BelongsToCompany` adds `TenantScope` globally — always use `withoutGlobalScopes()` in seeder and cross-company queries
- Commentions restricted to authenticated users — no per-requisition party restriction yet
- Sequential approval by `order` column is tracked but auto-notification of next approver is not yet automated (manual workflow)