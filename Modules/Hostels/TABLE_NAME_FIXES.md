# Table Name Fixes

This document summarizes the fixes made to resolve the "Table doesn't exist" errors in the Hostels module.

## Issue
The error `SQLSTATE[42S2]: Base table or view not found: 1146 Table 'kharis_erp2025.fee_types' doesn't exist` occurred because Laravel was using the default table name convention instead of the actual table names defined in the migrations.

## Root Cause
Laravel's default naming convention for tables is the plural snake_case version of the model name. However, all tables in the Hostels module are prefixed with `hostel_`, which Laravel doesn't automatically know about.

## Solution
Added explicit `$table` property definitions to all models to specify the correct table names that match the migrations.

## Models Updated

1. **FeeType** - Added `protected $table = 'hostel_fee_types';`
2. **BookingCharge** - Added `protected $table = 'hostel_booking_charges';`
3. **MaintenanceRequest** - Added `protected $table = 'hostel_maintenance_requests';`
4. **Incident** - Added `protected $table = 'hostel_incidents';`
5. **VisitorLog** - Added `protected $table = 'hostel_visitor_logs';`
6. **HostelOccupantDocument** - Added `protected $table = 'hostel_occupant_documents';`
7. **HostelCharge** - Added `protected $table = 'hostel_charges';`
8. **HostelBlock** - Added `protected $table = 'hostel_blocks';`
9. **HostelFloor** - Added `protected $table = 'hostel_floors';`

## Verification
All migrations have been confirmed to be run successfully:
- 2025_11_17_083714_create_hostels_table
- 2025_11_17_111436_create_rooms_table
- 2025_11_17_111449_create_beds_table
- 2025_11_17_111502_create_tenants_table
- 2025_11_17_111515_create_bookings_table
- 2025_11_17_111530_create_hostel_charges_table
- 2025_11_17_160830_create_hostel_blocks_table
- 2025_11_17_161210_create_hostel_floors_table
- 2025_11_17_162000_create_hostel_fee_types_table
- 2025_11_17_162100_create_hostel_booking_charges_table
- 2025_11_17_162200_create_hostel_maintenance_requests_table
- 2025_11_17_162300_create_hostel_incidents_table
- 2025_11_17_162400_create_hostel_visitor_logs_table
- 2025_11_17_162500_create_hostel_tenant_documents_table

## Additional Steps
Cleared all caches to ensure the changes take effect:
1. Configuration cache cleared
2. Route cache cleared
3. View cache cleared

These fixes ensure that all models correctly reference their respective database tables, resolving the "Table doesn't exist" errors.