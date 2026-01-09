# Module Integration Guide

This document explains how the Finance module integrates with other modules in the Kharis ERP system.

## Overview

The Finance module serves as the accounting backbone for the entire ERP system. It integrates with other modules to provide financial tracking and reporting for business operations across all departments.

## Integration Points

### 1. Hostels Module Integration

#### Automatic Invoice Creation
When a booking is created or confirmed in the Hostels module, the Finance module automatically creates an invoice for the booking amount.

**Integration Mechanism:**
- Event listener that monitors booking creation/updates
- Automatic invoice generation with appropriate line items
- Journal entries created for accounting purposes

**Data Flow:**
1. Booking is created in Hostels module
2. `CreateInvoiceForBooking` listener is triggered
3. Invoice is created in Finance module
4. Journal entries are created for accounting records

#### Console Command
A console command `finance:sync-bookings` is available to manually sync existing bookings to invoices.

### 2. PaymentsChannel Module Integration

#### Payment Processing
When a payment is processed through the PaymentsChannel module, the Finance module automatically records the payment and updates the corresponding invoice status.

**Integration Mechanism:**
- Event listener that monitors payment transactions
- Automatic payment record creation
- Invoice status updates (paid, partial, etc.)
- Journal entries for payment accounting

**Data Flow:**
1. Payment is processed in PaymentsChannel module
2. `ProcessPaymentFromChannel` listener is triggered
3. Payment record is created in Finance module
4. Invoice status is updated
5. Journal entries are created for accounting records

#### Console Command
A console command `finance:sync-payments` is available to manually sync existing payment transactions.

### 3. Core Module Integration

#### Company Context
All financial records are associated with companies from the Core module, ensuring proper multi-tenancy and data isolation.

**Integration Mechanism:**
- Foreign key relationships to Core Company model
- Middleware to set company context for financial operations

## Implementation Details

### Service Layer
The `IntegrationService` class provides methods for handling cross-module operations:
- `createInvoiceForBooking()` - Creates invoices for hostel bookings
- `processPaymentTransaction()` - Processes payments from payment channels

### Event Listeners
Event listeners provide real-time integration between modules:
- `CreateInvoiceForBooking` - Handles booking to invoice conversion
- `ProcessPaymentFromChannel` - Handles payment transaction processing

### Console Commands
Console commands provide batch processing capabilities:
- `SyncBookingsToInvoicesCommand` - Batch process bookings to invoices
- `SyncPaymentsCommand` - Batch process payment transactions

## Future Integration Opportunities

### Farms Module
- Automatic invoice creation for farm product sales
- Expense tracking for farm operations
- Inventory valuation integration

### Construction Module
- Project-based invoicing
- Progress billing for construction projects
- Cost tracking and project profitability analysis

### Manufacturing Modules
- Production cost tracking
- Work-in-progress inventory valuation
- Finished goods costing

## Best Practices

1. **Data Consistency**: All integration points should maintain data consistency between modules
2. **Error Handling**: Integration processes should handle errors gracefully without data loss
3. **Performance**: Batch operations should use appropriate chunking to avoid memory issues
4. **Audit Trail**: All cross-module operations should maintain proper audit trails
5. **Security**: Integration should respect module boundaries and user permissions

## Testing

Integration testing should verify:
- Data flows correctly between modules
- Financial records are created accurately
- Journal entries balance properly
- Error conditions are handled appropriately