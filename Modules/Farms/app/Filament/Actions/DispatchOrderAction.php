<?php

namespace Modules\Farms\Filament\Actions;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Modules\Farms\Models\FarmOrder;
use Modules\Farms\Models\FarmOrderDelivery;
use Modules\Fleet\Models\TripLog;
use Modules\Fleet\Models\Vehicle;

class DispatchOrderAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'dispatch';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Dispatch')
            ->icon('heroicon-o-truck')
            ->color('warning')
            ->visible(fn (FarmOrder $record): bool => $record->status === 'ready' && ! $record->delivery)
            ->form(function (FarmOrder $record): array {
                $companyId = $record->company_id;

                return [
                    Select::make('vehicle_id')
                        ->label('Vehicle')
                        ->options(
                            Vehicle::where('company_id', $companyId)
                                ->where('status', 'active')
                                ->pluck('plate_number', 'id')
                        )
                        ->searchable()
                        ->required(),

                    Select::make('driver_user_id')
                        ->label('Driver')
                        ->options(
                            \App\Models\User::whereHas('roles', fn ($q) => $q
                                ->where('name', 'driver')
                                ->where('company_id', $companyId)
                            )->pluck('name', 'id')
                        )
                        ->searchable()
                        ->required(),

                    DateTimePicker::make('estimated_delivery_at')
                        ->label('Estimated Delivery')
                        ->nullable()
                        ->placeholder('Optional'),

                    Textarea::make('notes')
                        ->rows(2)
                        ->placeholder('Delivery instructions or notes'),
                ];
            })
            ->action(function (FarmOrder $record, array $data): void {
                // Create a TripLog in Fleet module
                $trip = TripLog::create([
                    'company_id'      => $record->company_id,
                    'vehicle_id'      => $data['vehicle_id'],
                    'driver_id'       => $data['driver_user_id'],
                    'trip_date'       => now()->toDateString(),
                    'origin'          => 'Farm Store',
                    'destination'     => $record->delivery_address ?? $record->delivery_landmark ?? 'Customer Address',
                    'purpose'         => 'Farm Order Delivery — ' . $record->ref,
                    'client_name'     => $record->customer_name,
                    'client_phone'    => $record->customer_phone,
                    'status'          => 'in_progress',
                    'trip_reference'  => 'DEL-' . $record->ref,
                ]);

                // Create delivery record bridging order ↔ trip
                FarmOrderDelivery::create([
                    'farm_order_id'        => $record->id,
                    'trip_log_id'          => $trip->id,
                    'vehicle_id'           => $data['vehicle_id'],
                    'driver_user_id'       => $data['driver_user_id'],
                    'estimated_delivery_at' => $data['estimated_delivery_at'] ?? null,
                    'status'               => 'dispatched',
                    'notes'                => $data['notes'] ?? null,
                ]);

                // Advance order status
                $record->update(['status' => 'processing']);

                Notification::make()
                    ->title('Order dispatched — TripLog created in Fleet')
                    ->success()
                    ->send();
            });
    }
}
