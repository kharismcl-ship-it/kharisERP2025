# Booking Change Request Filament Resource

## Overview

This document explains the implementation of the Filament resource for managing Booking Change Requests in the Hostels module admin panel.

## Implementation Details

### Resource Structure

The BookingChangeRequest Filament resource follows the standard structure:

```
Modules/Hostels/Filament/Resources/BookingChangeRequests/
├── BookingChangeRequestResource.php
├── Pages/
│   ├── CreateBookingChangeRequest.php
│   ├── EditBookingChangeRequest.php
│   ├── ListBookingChangeRequests.php
│   └── ViewBookingChangeRequest.php
├── Schemas/
│   ├── BookingChangeRequestForm.php
│   ├── BookingChangeRequestInfolist.php
│   └── BookingChangeRequestTable.php
└── Tables/
    └── BookingChangeRequestsTable.php
```

### Key Components

#### 1. BookingChangeRequestResource.php

Main resource class that defines:
- Model binding to `BookingChangeRequest`
- Navigation icon and grouping
- Form, infolist, and table configurations
- Resource pages (CRUD operations)

#### 2. Tables Configuration

The table displays:
- Booking reference
- Tenant name
- Current room
- Requested room
- Requested bed
- Reason for change
- Status with color-coded badges
- Creation/update timestamps

Includes filters for status (pending, approved, rejected).

#### 3. Form Configuration

The form includes fields for:
- Booking selection (relationship)
- Requested room (relationship)
- Requested bed (relationship)
- Reason (textarea)
- Status (select)
- Notes (textarea)

#### 4. Infolist Configuration

The infolist is organized in sections:
- Booking Information (reference, tenant, current room/bed)
- Change Request Details (requested room/bed, reason, status, notes)
- Approval Information (approved by, approved at)

#### 5. Edit Page Actions

Custom actions added to the Edit page:
- Approve Request: Updates the change request status to 'approved', manages inventory
- Reject Request: Updates the change request status to 'rejected'

Both actions include confirmation dialogs and form inputs for notes.

## Navigation

The resource appears in the admin panel under the "Hostels" navigation group with the default rectangle stack icon.

## Usage

Administrators can:
1. View all change requests in a table with filtering capabilities
2. Create new change requests manually if needed
3. View detailed information about a specific change request
4. Edit change requests and approve/reject pending requests
5. Delete change requests if necessary

## Approval Workflow

When approving a change request:
1. System checks if the requested room/bed is still available
2. Starts a database transaction
3. Updates the change request status to 'approved'
4. Records the approver and approval timestamp
5. Releases the old bed (if assigned)
6. Updates the booking with new room/bed
7. Reserves the new bed (if selected)
8. Commits the transaction

If any step fails, the transaction is rolled back to maintain data consistency.

## Future Improvements

1. Add email notifications to tenants when their change requests are approved/rejected
2. Add validation to prevent selection of unavailable rooms/beds in the form
3. Add bulk approval/rejection actions
4. Add export functionality for change requests
5. Add more detailed logging for audit purposes