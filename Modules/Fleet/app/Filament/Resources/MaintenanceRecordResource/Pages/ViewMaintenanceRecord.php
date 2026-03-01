<?php

namespace Modules\Fleet\Filament\Resources\MaintenanceRecordResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Fleet\Events\MaintenancePartsRequested;
use Modules\Fleet\Filament\Resources\MaintenanceRecordResource;
use Modules\Fleet\Models\MaintenanceRecord;
use Modules\Fleet\Services\FleetService;

class ViewMaintenanceRecord extends ViewRecord
{
    protected static string $resource = MaintenanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => $this->record->status !== 'completed'),

            Action::make('start_service')
                ->label('Start Service')
                ->icon('heroicon-o-play')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'scheduled')
                ->requiresConfirmation()
                ->modalHeading('Start Service Job')
                ->modalDescription('This will mark the maintenance as in progress and set the vehicle status to Under Maintenance.')
                ->action(function () {
                    $this->record->update(['status' => 'in_progress']);
                    $this->record->vehicle?->update(['status' => 'under_maintenance']);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Service started — vehicle marked as Under Maintenance')->warning()->send();
                }),

            Action::make('complete_service')
                ->label('Mark Completed')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status === 'in_progress')
                ->requiresConfirmation()
                ->modalHeading('Complete Service Job')
                ->modalDescription('This will mark the maintenance as completed and restore the vehicle to Active status.')
                ->action(function () {
                    app(FleetService::class)->completeMaintenance($this->record);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Service completed — vehicle restored to Active')->success()->send();
                }),

            Action::make('request_parts')
                ->label('Request Parts')
                ->icon('heroicon-o-shopping-cart')
                ->color('info')
                ->visible(fn () => in_array($this->record->status, ['scheduled', 'in_progress']))
                ->form([
                    \Filament\Forms\Components\Repeater::make('parts')
                        ->label('Parts Required')
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('description')
                                ->label('Part / Description')
                                ->required()
                                ->placeholder('e.g. Engine oil filter'),
                            \Filament\Forms\Components\TextInput::make('quantity')
                                ->label('Qty')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->minValue(0.01),
                            \Filament\Forms\Components\TextInput::make('unit_price')
                                ->label('Est. Unit Price (GHS)')
                                ->numeric()
                                ->prefix('GHS')
                                ->default(0)
                                ->step(0.01),
                        ])
                        ->columns(3)
                        ->minItems(1)
                        ->addActionLabel('Add Part'),
                ])
                ->action(function (array $data) {
                    MaintenancePartsRequested::dispatch($this->record, $data['parts']);

                    Notification::make()
                        ->title('Parts requested — a draft Purchase Order has been created in Procurement')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Service Overview')
                ->description('Core maintenance job details')
                ->columns(3)
                ->schema([
                    TextEntry::make('vehicle.name')
                        ->label('Vehicle')
                        ->weight('bold')
                        ->icon('heroicon-o-truck'),

                    TextEntry::make('vehicle.plate')
                        ->label('Plate Number')
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'completed'   => 'success',
                            'in_progress' => 'warning',
                            'scheduled'   => 'info',
                            default       => 'gray',
                        }),

                    TextEntry::make('type')
                        ->label('Service Type')
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('service_date')
                        ->label('Service Date')
                        ->date('d M Y'),

                    TextEntry::make('service_provider')
                        ->label('Service Provider')
                        ->placeholder('—'),
                ]),

            Section::make('Description & Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('description')
                        ->columnSpanFull()
                        ->placeholder('No description provided'),
                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('No notes'),
                ]),

            Section::make('Cost & Financial Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('cost')
                        ->label('Service Cost')
                        ->money('GHS')
                        ->weight('bold')
                        ->size(TextEntry\TextEntrySize::Large),

                    TextEntry::make('finance_expense_id')
                        ->label('Finance Expense ID')
                        ->placeholder('Not posted'),

                    TextEntry::make('purchase_order_id')
                        ->label('Linked PO ID')
                        ->placeholder('No parts PO'),
                ]),

            Section::make('Mileage & Next Service')
                ->columns(4)
                ->schema([
                    TextEntry::make('mileage_at_service')
                        ->label('Mileage at Service')
                        ->numeric(decimalPlaces: 0)
                        ->suffix(' km')
                        ->placeholder('—'),

                    TextEntry::make('next_service_date')
                        ->label('Next Service Due')
                        ->date('d M Y')
                        ->placeholder('—'),

                    TextEntry::make('next_service_mileage')
                        ->label('Next Service Mileage')
                        ->numeric(decimalPlaces: 0)
                        ->suffix(' km')
                        ->placeholder('—'),

                    TextEntry::make('vehicle.current_mileage')
                        ->label('Current Mileage')
                        ->numeric(decimalPlaces: 0)
                        ->suffix(' km'),
                ]),

            Section::make('Audit')
                ->columns(2)
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('created_at')->dateTime()->label('Created'),
                    TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                ]),
        ]);
    }
}
