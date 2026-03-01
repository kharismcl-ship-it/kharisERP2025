<?php

namespace Modules\Hostels\Filament\Resources\BookingResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;
use Modules\Hostels\Filament\Resources\BookingResource;
use Modules\Hostels\Models\Booking;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    public function getTitle(): string|Htmlable
    {
        return "Booking #{$this->record->booking_reference}";
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Booking Overview')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('booking_reference')
                                    ->label('Booking Reference')
                                    ->weight('font-bold'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'gray',
                                        'awaiting_payment' => 'warning',
                                        'confirmed' => 'success',
                                        'checked_in' => 'info',
                                        'checked_out' => 'primary',
                                        'cancelled' => 'danger',
                                        'no_show' => 'gray',
                                        default => 'gray',
                                    }),

                                TextEntry::make('booking_type')
                                    ->label('Booking Type')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'academic' => 'Academic Year',
                                        'semester' => 'Semester',
                                        'short_stay' => 'Short Stay',
                                        default => ucfirst($state),
                                    }),

                                TextEntry::make('payment_status')
                                    ->label('Payment Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'gray',
                                        'partial' => 'warning',
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        'refunded' => 'info',
                                        default => 'gray',
                                    }),

                                TextEntry::make('total_amount')
                                    ->label('Total Amount')
                                    ->money('GHS')
                                    ->weight('font-bold'),

                                TextEntry::make('amount_paid')
                                    ->label('Amount Paid')
                                    ->money('GHS')
                                    ->color('success'),

                                TextEntry::make('balance_amount')
                                    ->label('Balance')
                                    ->money('GHS')
                                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),

                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime(),
                            ]),
                    ]),

                Section::make('Accommodation Details')
                    ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('hostel.name')
                                    ->label('Hostel')
                                    ->weight('font-medium'),

                            TextEntry::make('room.room_number')
                                    ->label('Room Number'),

                            TextEntry::make('bed.bed_number')
                                    ->label('Bed Number')
                                    ->default('Not assigned'),

                            TextEntry::make('check_in_date')
                                    ->label('Check-in Date')
                                    ->date(),

                            TextEntry::make('check_out_date')
                                    ->label('Check-out Date')
                                    ->date(),

                            TextEntry::make('actual_check_in_at')
                                    ->label('Actual Check-in')
                                    ->dateTime(),

                            TextEntry::make('actual_check_out_at')
                                    ->label('Actual Check-out')
                                    ->dateTime(),
                        ]),
                ]),

                Section::make('Guest Information')
                    ->schema([
                    Grid::make(4)
                        ->schema([
                            TextEntry::make('hostel_occupant.full_name')
                                    ->label('Full Name')
                                    ->weight('font-medium'),

                            TextEntry::make('hostel_occupant.email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope'),

                            TextEntry::make('hostel_occupant.phone')
                                    ->label('Phone')
                                    ->icon('heroicon-o-phone'),

                            TextEntry::make('hostel_occupant.student_id')
                                    ->label('Student ID'),

                            TextEntry::make('hostel_occupant.institution')
                                    ->label('Institution'),

                            TextEntry::make('hostel_occupant.gender')
                                    ->label('Gender'),

                            TextEntry::make('hostel_occupant.dob')
                                    ->label('Date of Birth')
                                    ->date(),

                            TextEntry::make('hostel_occupant.national_id_number')
                                    ->label('National ID'),
                        ]),
                ]),

                Section::make('Payment Details')
                    ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('payment_method')
                                    ->label('Payment Method')
                                    ->formatStateUsing(fn (?string $state): string => $state ? match ($state) {
                                        'manual_bank' => 'Manual Bank Transfer',
                                        'manual_cash' => 'Manual Cash Payment',
                                        'momo' => 'Mobile Money',
                                        'card' => 'Credit/Debit Card',
                                        'bank' => 'Online Bank Transfer',
                                        'flutterwave_online' => 'Flutterwave',
                                        'paystack_online' => 'Paystack',
                                        default => ucwords(str_replace('_', ' ', $state)),
                                    } : 'Not specified'),

                            TextEntry::make('channel')
                                    ->label('Payment Channel'),

                            TextEntry::make('deposit_amount')
                                    ->label('Deposit Amount')
                                    ->money('GHS'),

                            TextEntry::make('deposit_paid')
                                    ->label('Deposit Paid'),

                            TextEntry::make('deposit_balance')
                                    ->label('Deposit Balance')
                                    ->money('GHS'),

                            TextEntry::make('deposit_refunded')
                                    ->label('Deposit Refunded'),

                            TextEntry::make('refund_processed_at')
                                    ->label('Refund Processed')
                                    ->dateTime(),
                        ]),
                ]),

                Section::make('Timeline & Notes')
                    ->schema([
                    TextEntry::make('notes')
                        ->label('Notes')
                        ->placeholder('No notes')
                        ->columnSpanFull(),

                    TextEntry::make('accepted_terms_at')
                        ->label('Terms Accepted')
                        ->dateTime(),

                    TextEntry::make('hold_expires_at')
                        ->label('Hold Expires')
                        ->dateTime(),
                ]),

                Section::make('Documents')
                    ->schema([
                    Grid::make(3)
                        ->schema([
                            ImageEntry::make('id_card_front_photo')
                                    ->label('ID Card Front')
                                    ->default('No image')
                                    ->height('150px'),

                            ImageEntry::make('id_card_back_photo')
                                    ->label('ID Card Back')
                                    ->default('No image')
                                    ->height('150px'),

                            ImageEntry::make('profile_photo')
                                    ->label('Profile Photo')
                                    ->default('No image')
                                    ->height('150px'),
                        ]),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Booking')
                ->icon('heroicon-o-pencil')
                ->color('primary'),

            Action::make('check_in')
                ->label('Check In')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('success')
                ->visible(fn (Booking $record) => $record->status === 'confirmed' && ! $record->actual_check_in_at)
                ->action(function (Booking $record) {
                    // Enforce minimum payment requirement before allowing check-in
                    if (! $record->meetsMinimumPaymentRequirement()) {
                        $outstanding = number_format($record->getOutstandingAmount(), 2);

                        Notification::make()
                            ->title('Check-in blocked — Payment required')
                            ->body("Guest has an outstanding balance of GHS {$outstanding}. Minimum payment must be received before check-in.")
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }

                    $record->checkIn();

                    $this->refresh();

                    Notification::make()
                        ->title('Guest checked in successfully')
                        ->success()
                        ->send();
                }),

            Action::make('check_out')
                ->label('Check Out')
                ->icon('heroicon-o-arrow-left-circle')
                ->color('warning')
                ->visible(fn (Booking $record) => $record->status === 'checked_in' && ! $record->actual_check_out_at)
                ->action(function (Booking $record) {
                    $record->update([
                        'status'               => 'checked_out',
                        'actual_check_out_at'  => now(),
                    ]);

                    $this->refresh();

                    Notification::make()
                        ->title('Guest checked out successfully')
                        ->success()
                        ->send();
                }),

            Action::make('cancel_booking')
                ->label('Cancel Booking')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (Booking $record) => in_array($record->status, ['pending', 'awaiting_payment', 'confirmed']))
                ->requiresConfirmation()
                ->modalDescription(fn (Booking $record): string => $record->getTotalPaidAmount() > 0
                    ? 'This booking has a paid amount of GHS '.number_format($record->getTotalPaidAmount(), 2).'. A refund of up to GHS '.number_format($record->getEstimatedRefundAmount(), 2).' may be processed per the cancellation policy.'
                    : 'Are you sure you want to cancel this booking?'
                )
                ->action(function (Booking $record) {
                    try {
                        $result = $record->cancelBooking();

                        $this->refresh();

                        $message = 'Booking cancelled successfully.';
                        if ($result['refund_amount'] > 0) {
                            $refundFormatted = number_format($result['refund_amount'], 2);
                            $message .= $result['refund_processed']
                                ? " Refund of GHS {$refundFormatted} has been processed."
                                : " Refund of GHS {$refundFormatted} could not be processed automatically — please handle manually.";
                        }

                        Notification::make()
                            ->title('Booking cancelled')
                            ->body($message)
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        // Cancellation policy not configured — do a safe manual cancellation
                        Log::warning('Cancellation policy error, falling back to manual cancel', [
                            'booking_id' => $record->id,
                            'error'      => $e->getMessage(),
                        ]);

                        $record->update(['status' => 'cancelled']);

                        if ($record->bed_id) {
                            $record->bed->update(['status' => 'available']);
                        }

                        $this->refresh();

                        Notification::make()
                            ->title('Booking cancelled')
                            ->body('Booking cancelled. No cancellation policy was found — please process any applicable refund manually.')
                            ->warning()
                            ->send();
                    }
                }),

            Action::make('view_payments')
                ->label('View Payments')
                ->icon('heroicon-o-credit-card')
                ->color('info')
                ->url(fn (Booking $record) => route('filament.admin.resources.bookings.view', $record).'#payments'),

            DeleteAction::make()
                ->label('Delete Booking')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // BookingResource\Widgets\BookingTimeline::class,
        ];
    }
}
