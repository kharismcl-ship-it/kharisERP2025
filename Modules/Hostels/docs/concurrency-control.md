# Concurrency Control in Hostel Booking System

## Overview

The hostel booking system implements concurrency control to prevent duplicate bookings for the same bed. This is a critical feature to ensure data integrity and provide a good user experience.

## Implementation Details

### 1. Database-Level Optimizations

1. **Indexing**: Added an index on `bed_id` and `status` columns in the `bookings` table for faster queries when checking for existing bookings:
   ```php
   $table->index(['bed_id', 'status'], 'idx_bookings_bed_status');
   ```

2. **Database Transactions**: All booking creation operations use database transactions to ensure atomicity.

3. **Row-Level Locking**: When checking bed availability, we use `lockForUpdate()` to prevent race conditions.

### 2. Application-Level Controls

1. **Active/Inactive Booking Scopes**: The [Booking](file:///Users/lexistudio/trae-projects/kharisERP2025/Modules/Hostels/app/Models/Booking.php#L13-L112) model includes scopes to easily query active and inactive bookings:
   ```php
   public function scopeActive($query)
   {
       return $query->whereIn('status', self::ACTIVE_STATUSES);
   }

   public function scopeInactive($query)
   {
       return $query->whereIn('status', self::INACTIVE_STATUSES);
   }
   ```

2. **Booking Status Definitions**: Clear definitions of what constitutes an "active" vs "inactive" booking:
   ```php
   public const ACTIVE_STATUSES = ['pending', 'awaiting_payment', 'confirmed', 'checked_in'];
   public const INACTIVE_STATUSES = ['cancelled', 'checked_out', 'no_show'];
   ```

### 3. Booking Creation Process

The [BookingWizard](file:///Users/lexistudio/trae-projects/kharisERP2025/Modules/Hostels/app/Http/Livewire/Public/BookingWizard.php#L11-L285) component implements the following process to ensure concurrency control:

1. Start a database transaction
2. Check if the selected bed is available using row-level locking
3. Check for existing active bookings for the same bed
4. If no conflicts, create the booking and update the bed status
5. If conflicts exist, throw an exception with a user-friendly message

```php
// Lock the bed record for update
$bed = Bed::where('id', $this->selectedBed)
    ->where('status', 'available')
    ->lockForUpdate()
    ->first();

// Check if there are any active bookings for this bed
$existingBooking = Booking::where('bed_id', $this->selectedBed)
    ->active()
    ->lockForUpdate()
    ->first();

if ($existingBooking) {
    throw new Exception('This bed has already been booked by another user. Please select another bed.');
}
```

## Handling Concurrent Requests

When multiple users attempt to book the same bed simultaneously:

1. The first request will acquire locks and proceed with booking
2. Subsequent requests will wait for the lock to be released
3. Once the lock is released, subsequent requests will find that the bed is no longer available
4. Users receive a clear error message indicating the bed is no longer available

## Performance Considerations

1. **Indexing**: The index on `bed_id` and `status` ensures fast lookups when checking for existing bookings
2. **Minimal Lock Duration**: Locks are held only for the duration of the transaction
3. **Scoped Queries**: Using the `active()` scope ensures we only check relevant bookings

## Testing

The concurrency control implementation is verified through the `BookingConcurrencyTest` which tests:

1. Active/inactive scope functionality
2. Proper filtering of bookings based on status

## Future Improvements

1. **Retry Logic**: Implement automatic retry with exponential backoff for failed bookings due to temporary conflicts
2. **Queue-Based Processing**: For high-traffic scenarios, consider using a queue system to process bookings
3. **Caching**: Implement caching for bed availability to reduce database load