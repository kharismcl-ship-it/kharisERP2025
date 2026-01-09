# Finance Module

The **Finance** module is the accounting backbone for the entire Kharis ERP. It provides comprehensive financial management capabilities including chart of accounts, journal entries, invoicing, and payment processing.

## Features

- Chart of Accounts management
- Journal entries and double-entry bookkeeping
- Invoice creation and management
- Payment processing and tracking
- Financial reporting
- Integration with other modules (Hostels, Farms, Construction, Manufacturing)
- Automated invoice creation for hostel bookings
- Automatic payment processing from payment channels
- Console commands for data synchronization
- Prepared integrations for all business modules (Farms, Construction, Manufacturing, Procurement, Fleet, HR)
- Extensible architecture for future modules

## Entities

### Account
Represents a financial account in the chart of accounts.
- `id`
- `company_id`
- `code`
- `name`
- `type` (asset, liability, equity, income, expense)
- `parent_id` (for hierarchical accounts)

### JournalEntry
Represents a journal entry in the general ledger.
- `id`
- `company_id`
- `date`
- `reference`
- `description`

### JournalLine
Represents a line item in a journal entry.
- `id`
- `journal_entry_id`
- `account_id`
- `debit`
- `credit`

### Invoice
Represents a customer invoice.
- `id`
- `company_id`
- `customer_name`
- `customer_type` (hostel_occupant, external, etc.)
- `customer_id`
- `invoice_number`
- `invoice_date`
- `due_date`
- `status` (draft, sent, paid, overdue, cancelled)
- `sub_total`
- `tax_total`
- `total`
- `hostel_id` (nullable)
- `farm_id` (nullable)
- `construction_project_id` (nullable)
- `plant_id` (nullable)

### InvoiceLine
Represents a line item in an invoice.
- `id`
- `invoice_id`
- `description`
- `quantity`
- `unit_price`
- `line_total`

### Payment
Represents a payment received.
- `id`
- `company_id`
- `invoice_id` (nullable)
- `amount`
- `payment_date`
- `payment_method` (cash, bank, momo, etc.)
- `reference`

## Livewire Components

The module includes the following Livewire components for frontend operations:

- **FinanceIndex** - Main dashboard
- **Invoices\Index** - List all invoices
- **Invoices\Create** - Create new invoices
- **Payments\Index** - List all payments
- **Accounts\ChartOfAccounts** - View chart of accounts
- **Reports\FinancialReports** - Access financial reports

## Console Commands

The module includes console commands for data synchronization:

- `finance:sync-bookings` - Create invoices for hostel bookings
- `finance:sync-payments` - Process payments from payment channels
- `finance:sync-farms` - Create invoices for farm sales
- `finance:sync-construction` - Create invoices for construction projects
- `finance:sync-manufacturing` - Create invoices for manufacturing batches
- `finance:sync-procurement` - Record procurement expenses
- `finance:sync-fleet` - Record fleet expenses
- `finance:sync-hr` - Record HR/payroll expenses

## Filament Resources

The module includes Filament admin panel resources for managing:
- Accounts
- Journal Entries
- Journal Lines
- Invoices
- Invoice Lines
- Payments

## Routes

The module provides the following web routes:

- `/finance` - Main dashboard
- `/finance/invoices` - List invoices
- `/finance/invoices/create` - Create new invoice
- `/finance/payments` - List payments
- `/finance/accounts/chart` - View chart of accounts
- `/finance/reports` - Access financial reports

All routes are protected by authentication and company middleware.