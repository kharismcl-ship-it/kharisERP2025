# Payment Method Switching and Room/Bed Change Features

## Overview

This document explains two new features implemented in the hostel booking system:

1. **Payment Method Switching** - Allows users to change their payment method during the booking process
2. **Room/Bed Change Requests** - Allows users to request changes to their room or bed assignment after booking confirmation

## Payment Method Switching

### Implementation Details

The payment method switching feature allows users to change their selected payment method before initiating the payment process. This is particularly useful when a user selects one payment method but wants to switch to another.

### How It Works

1. On the payment page, users can select a payment method from the dropdown
2. If they want to change their payment method:
   - Click the "Change Payment Method" link (visible only when there's a pending payment intent)
   - This cancels the existing payment intent
   - Users can then select a different payment method
   - A new payment intent is created with the newly selected method

### Technical Implementation

- Added a "Change Payment Method" button in the [booking-payment.blade.php](file:///Users/lexistudio/trae-projects/kharisERP2025/Modules/Hostels/resources/views/livewire/public/booking-payment.blade.php#L1-L157) view
- Implemented `changePaymentMethod()` method in [BookingPayment](file:///Users/lexistudio/trae-projects/kharisERP2025/Modules/Hostels/app/Http/Livewire/Public/BookingPayment.php#L13-L159) Livewire component
- When changing payment method:
  - The existing payment intent is cancelled (status set to 'cancelled')
  - A new payment intent is created with the selected method
  - User can then proceed with payment using the new method

## Room/Bed Change Requests

### Implementation Details

The room/bed change request feature allows users to request changes to their assigned room or bed after booking confirmation. This workflow involves:

1. User submits a change request through a form
2. Admin reviews and approves/rejects the request
3. If approved, the system updates the booking and manages inventory

### How It Works

#### For Users:
1. After booking confirmation, users can access the "Request Room/Bed Change" option
2. They select a new room and optionally a new bed
3. They provide a reason for the change
4. The request is submitted and awaits admin approval

#### For Admins:
1. Admins can view all change requests in the admin panel
2. They can filter requests by status (pending, approved, rejected)
3. They can search requests by booking reference or tenant information
4. For pending requests, admins can:
   - Approve the request (which updates the booking and manages inventory)
   - Reject the request (with an optional reason)

### Technical Implementation

#### Database:
- Created [BookingChangeRequest](file:///Users/lexistudio/trae-projects/kharisERP2025/Modules/Hostels/app/Models/BookingChangeRequest.php#L13-L83) model with the following fields:
  - `booking_id` - The booking to be changed
  - `requested_room_id` - The requested new room
  - `requested_bed_id` - The requested new bed (optional)
  - `reason` - Reason for the change request
  - `status` - pending, approved, or rejected
  - `approved_by` - User who approved/rejected the request
  - `approved_at` - Timestamp of approval/rejection
  - `notes` - Additional notes (e.g., rejection reason)

#### Components:
1. **Public User Interface**:
   - [BookingChangeRequest](file:///Users/lexistudio/trae-projects/kharisERP2025/Modules/Hostels/app/Http/Livewire/Public/BookingChangeRequest.php#L11-L83) Livewire component for users to submit change requests
   - Added link in [booking-confirmation.blade.php](file:///Users/lexistudio/trae-projects/kharisERP2025/Modules/Hostels/resources/views/livewire/public/booking-confirmation.blade.php#L1-L111) to access the change request form

2. **Admin Interface**:
   - [BookingChangeRequests](file:///Users/lexistudio/trae-projects/kharisERP2025/Modules/Hostels/app/Http/Livewire/BookingChangeRequests.php#L13-L112) Livewire component for admins to manage change requests
   - List view with filtering and search capabilities
   - Approval/rejection functionality with inventory management

#### Inventory Management:
When a change request is approved:
1. The old bed is released (status changed to 'available')
2. The booking is updated with new room/bed
3. The new bed is reserved (status changed to 'occupied')

This process is wrapped in a database transaction to ensure data consistency.

### Routes

#### Public Routes:
- `GET /hostels/bookings/{booking}/change-request` - Access the change request form

#### Admin Routes:
- `GET /hostels/admin/{hostel:slug}/change-requests` - Manage change requests

## Testing

Both features have been implemented with proper error handling and validation. The database schema includes appropriate foreign key constraints and indexes for performance.

## Future Improvements

1. Add email notifications for change request status updates
2. Add ability for users to view the status of their change requests
3. Add ability to cancel pending change requests
4. Add more detailed logging for audit purposes
5. Add validation to prevent requests for unavailable rooms/beds