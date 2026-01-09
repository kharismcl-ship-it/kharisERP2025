<?php

namespace Modules\Hostels\Filament\Resources\BookingChangeRequests\Pages;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;
use Modules\Hostels\Filament\Resources\BookingChangeRequests\BookingChangeRequestResource;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\BookingChangeRequest;
use Modules\Hostels\Models\Room;

class EditBookingChangeRequest extends EditRecord
{
    protected static string $resource = BookingChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('approve')
                ->label('Approve Request')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn (BookingChangeRequest $record) => $record->status === 'pending')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Textarea::make('notes')
                        ->label('Approval Notes (Optional)')
                        ->maxLength(500),
                ])
                ->action(function (BookingChangeRequest $record, array $data) {
                    // Check if the requested bed/room is still available
                    if ($record->requested_bed_id) {
                        $bed = Bed::find($record->requested_bed_id);
                        if (! $bed || $bed->status !== 'available') {
                            \Filament\Notifications\Notification::make()
                                ->title('Bed Not Available')
                                ->body('The requested bed is no longer available.')
                                ->danger()
                                ->send();

                            return;
                        }
                    }

                    if ($record->requested_room_id) {
                        $room = Room::find($record->requested_room_id);
                        if (! $room || $room->status !== 'available') {
                            \Filament\Notifications\Notification::make()
                                ->title('Room Not Available')
                                ->body('The requested room is no longer available.')
                                ->danger()
                                ->send();

                            return;
                        }
                    }

                    // Start a database transaction
                    \DB::beginTransaction();

                    try {
                        // Update the change request
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'notes' => $data['notes'] ?? null,
                        ]);

                        // Get the booking
                        $booking = $record->booking;

                        // Release the old bed if it exists
                        if ($booking->bed_id) {
                            $oldBed = Bed::find($booking->bed_id);
                            if ($oldBed) {
                                $oldBed->update(['status' => 'available']);
                            }
                        }

                        // Update the booking with new room/bed
                        $booking->update([
                            'room_id' => $record->requested_room_id,
                            'bed_id' => $record->requested_bed_id,
                        ]);

                        // Reserve the new bed if it exists
                        if ($record->requested_bed_id) {
                            $newBed = Bed::find($record->requested_bed_id);
                            if ($newBed) {
                                $newBed->update(['status' => 'occupied']);
                            }
                        }

                        // Commit the transaction
                        \DB::commit();

                        \Filament\Notifications\Notification::make()
                            ->title('Request Approved')
                            ->body('The change request has been approved successfully.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        // Rollback the transaction
                        \DB::rollback();

                        \Log::error('Error approving booking change request: '.$e->getMessage());
                        \Filament\Notifications\Notification::make()
                            ->title('Approval Failed')
                            ->body('An error occurred while approving the change request. Please try again.')
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('reject')
                ->label('Reject Request')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn (BookingChangeRequest $record) => $record->status === 'pending')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Textarea::make('notes')
                        ->label('Rejection Reason')
                        ->required()
                        ->maxLength(500),
                ])
                ->action(function (BookingChangeRequest $record, array $data) {
                    $record->update([
                        'status' => 'rejected',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'notes' => $data['notes'],
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Request Rejected')
                        ->body('The change request has been rejected.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
