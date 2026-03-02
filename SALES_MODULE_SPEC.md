# Sales Module — Specification & Architecture

**Date:** 2026-03-01
**Status:** Approved — In Implementation
**Phases:** A (CRM + Catalog) → B (Pipeline + Orders) → C (POS) → D (Restaurant) → E (Source Enhancements)

---

## Purpose

The Sales module is the **commercial front-end** for the entire KharisERP. It acts as:
- A unified CRM for all customer relationships
- A product/service catalog sourced from every module
- A quotation and order management system
- A POS terminal for walk-in customers
- A restaurant/food-service management system
- The fulfillment orchestrator that dispatches to source modules on sale

---

## Architecture

### Two Core Services

**`CatalogSyncService`** — pulls sellable definitions from each module into `SalesCatalog`. Runs on schedule + triggered by module events (batch completed, harvest recorded, etc.)

**`SalesFulfillmentService`** — when an order/POS sale is confirmed, dispatches to the correct module handler and posts Finance GL.

```
SalesOrder (confirmed)
    └── SalesFulfillmentService::fulfill()
            ├── WaterFulfillmentHandler       → creates/updates MwDistributionRecord
            ├── PaperFulfillmentHandler        → decrements MpProductionBatch pool
            ├── FarmFulfillmentHandler         → creates FarmSale + decrements FarmProduceInventory
            ├── InventoryFulfillmentHandler    → StockService::adjust() per warehouse
            ├── FleetFulfillmentHandler        → creates TripLog with fare
            ├── ConstructionFulfillmentHandler → creates ConstructionProject
            └── HostelsFulfillmentHandler      → creates Booking
                        ↓ (all handlers)
            CreateInvoiceForSalesOrder         → Finance Invoice + GL double-entry
            SendOrderConfirmationSms           → CommunicationCentre
```

---

## Models (23 total)

### CRM (4 models)
| Model | Key Fields |
|---|---|
| SalesLead | name, email, phone, company_name, source, status, assigned_to |
| SalesContact | first_name, last_name, email, phone, whatsapp_number, organization_id, job_title, tags |
| SalesOrganization | name, industry, website, city, country, credit_limit, payment_terms, currency |
| SalesActivity | type (call/email/meeting/demo/task/note), subject, body, scheduled_at, completed_at, outcome, morphable related |

### Catalog (3 models)
| Model | Key Fields |
|---|---|
| SalesCatalog | source_module, source_type, source_id (nullable), name, sku, unit, base_price, tax_rate, availability_mode, is_active |
| SalesPriceList | name, currency, valid_from, valid_to, is_default |
| SalesPriceListItem | price_list_id, catalog_item_id, override_price, min_quantity |

### Pipeline (2 models)
| Model | Key Fields |
|---|---|
| SalesOpportunity | title, contact_id, organization_id, estimated_value, probability_pct, stage, expected_close_date, assigned_to |
| SalesOpportunityItem | opportunity_id, catalog_item_id, quantity, unit_price |

### Quotation & Orders (4 models)
| Model | Key Fields |
|---|---|
| SalesQuotation | contact_id, org_id, reference (QUO-YYYYMM-00001), status, valid_until, subtotal, tax, total |
| SalesQuotationLine | quotation_id, catalog_item_id, quantity, unit_price, discount_pct, line_total |
| SalesOrder | quotation_id (nullable), contact_id, org_id, reference (SO-YYYYMM-00001), status, invoice_id |
| SalesOrderLine | order_id, catalog_item_id, quantity, unit_price, fulfilled_quantity, fulfillment_status |

### POS (5 models)
| Model | Key Fields |
|---|---|
| PosTerminal | name, location, is_active |
| PosSession | terminal_id, cashier_id, opened_at, closed_at, opening_float, closing_cash, cash_variance, status |
| PosSale | session_id, contact_id (nullable), reference (POS-YYYYMMDD-00001), subtotal, tax, total, invoice_id |
| PosSaleLine | pos_sale_id, catalog_item_id, quantity, unit_price, discount_pct, line_total |
| PosPayment | pos_sale_id, method (cash/momo/card/credit/voucher), amount, reference |

### Restaurant (5 models)
| Model | Key Fields |
|---|---|
| SalesRestaurant | name, address, default_vat_rate, receipt_header |
| DiningTable | restaurant_id, section, table_number, capacity, status (available/occupied/reserved/cleaning) |
| DiningOrder | table_id, waiter_id, status (open/in_kitchen/ready/served/paid/cancelled), subtotal, tax, total, invoice_id |
| DiningOrderItem | dining_order_id, catalog_item_id, quantity, unit_price, status, notes |
| KitchenTicket | dining_order_id, station, status (pending/in_progress/completed), fired_at, completed_at |

---

## Migrations (18 new)

1. `create_sales_leads_table`
2. `create_sales_contacts_table`
3. `create_sales_organizations_table`
4. `create_sales_activities_table`
5. `create_sales_catalogs_table`
6. `create_sales_price_lists_table`
7. `create_sales_price_list_items_table`
8. `create_sales_opportunities_table`
9. `create_sales_opportunity_items_table`
10. `create_sales_quotations_table`
11. `create_sales_quotation_lines_table`
12. `create_sales_orders_table`
13. `create_sales_order_lines_table`
14. `create_pos_terminals_table`
15. `create_pos_sessions_table`
16. `create_pos_sales_table`
17. `create_pos_sale_lines_table`
18. `create_pos_payments_table`
19. `create_sales_restaurants_table`
20. `create_dining_tables_table`
21. `create_dining_orders_table`
22. `create_dining_order_items_table`
23. `create_kitchen_tickets_table`

---

## Source Module Enhancements (Phase E)

| Module | Table | Columns Added |
|---|---|---|
| Fleet | trip_logs | fare_amount (decimal:2), client_name, client_phone, client_email |
| ManufacturingPaper | mp_paper_grades | unit_selling_price (decimal:4), min_order_quantity (decimal:3) |
| ManufacturingWater | mw_distribution_records | customer_name, customer_phone, customer_email |

---

## Finance GL (Revenue Accounts)

| Source Module | Revenue Account | Entry |
|---|---|---|
| ManufacturingWater | 4300 Water Revenue | DR 1110 AR / CR 4300 |
| ManufacturingPaper | 4200 Paper Revenue | DR 1110 AR / CR 4200 |
| Farms | 4400 Agricultural Revenue | DR 1110 AR / CR 4400 |
| ProcurementInventory | 4100 Retail Revenue | DR 1110 AR / CR 4100 |
| Fleet | 4600 Transport Revenue | DR 1110 AR / CR 4600 |
| Construction | 4500 Service Revenue | DR 1110 AR / CR 4500 |
| Hostels | 4700 Accommodation Revenue | DR 1110 AR / CR 4700 |
| Restaurant (F&B) | 4800 F&B Revenue | DR 1110 AR / CR 4800 |
| POS Cash Sale | any of above | DR 1000 Cash / CR Revenue |

---

## Events & Listeners

| Event | Listener |
|---|---|
| SalesOrderConfirmed | FulfillSalesOrder → dispatches module handlers |
| SalesOrderFulfilled | CreateInvoiceForSalesOrder, SendOrderFulfilledSms |
| PosSaleCompleted | CreateInvoiceForPosSale, SendPosReceiptSms, FulfillPosSale |
| QuotationSent | SendQuotationEmail |
| LeadAssigned | (Filament DB notification) |
| DiningOrderSentToKitchen | CreateKitchenTickets |
| CatalogSyncRequested | CatalogSyncService::syncAll() |

---

## CommTemplates

| Code | Channel | Trigger |
|---|---|---|
| sales_quotation_sent | email | Quotation sent to customer |
| sales_order_confirmed | sms + email | Order confirmed |
| sales_order_fulfilled | sms | All lines delivered |
| pos_receipt_sms | sms | POS sale completed |
| sales_lead_assigned | db | Lead assigned to salesperson |
| sales_follow_up_reminder | sms | Overdue CRM activity |
| restaurant_order_ready | db | Kitchen marks order ready |

---

## Filament Resources

| Resource | Sub-section |
|---|---|
| LeadResource (Kanban pipeline view) | CRM |
| ContactResource (activity timeline) | CRM |
| OrganizationResource (360 view) | CRM |
| OpportunityResource | Pipeline |
| ActivityResource | CRM |
| CatalogItemResource (source badge) | Catalog |
| PriceListResource | Catalog |
| QuotationResource (Send/Accept actions) | Orders |
| SalesOrderResource (fulfillment tracking) | Orders |
| PosTerminalResource | POS |
| PosSessionResource (open/close + reconcile) | POS |
| PosSaleResource (receipt view) | POS |
| PosDashboard (custom page — live cart) | POS |
| DiningTableResource (live status grid) | Restaurant |
| DiningOrderResource | Restaurant |
| KitchenBoardPage (Livewire real-time) | Restaurant |

---

## Implementation Phases

| Phase | Scope | Status |
|---|---|---|
| A | CRM + Catalog: models, migrations, policies, Filament resources | Implementing |
| B | Pipeline + Orders + FulfillmentService + 7 handlers + Finance GL | Implementing |
| C | POS Terminal: terminal, session, sale, payment, PosDashboard | Implementing |
| D | Restaurant: table, order, kitchen board (Livewire real-time) | Implementing |
| E | Source module enhancements: Fleet fare, Paper pricing, Water customer | Implementing |

---

*Spec approved 2026-03-01. See CROSS_MODULE_INTEGRATION_AUDIT.md for cross-module wiring log.*
