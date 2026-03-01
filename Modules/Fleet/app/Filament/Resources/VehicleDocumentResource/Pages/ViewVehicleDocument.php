<?php

namespace Modules\Fleet\Filament\Resources\VehicleDocumentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Fleet\Filament\Resources\VehicleDocumentResource;

class ViewVehicleDocument extends ViewRecord
{
    protected static string $resource = VehicleDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => ! $this->record->is_expired),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document Overview')
                ->description('Vehicle document details and validity')
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

                    TextEntry::make('type')
                        ->label('Document Type')
                        ->badge()
                        ->color('primary')
                        ->formatStateUsing(fn ($state) => ucfirst($state)),

                    TextEntry::make('document_number')
                        ->label('Document / Reference Number')
                        ->placeholder('—')
                        ->copyable(),

                    TextEntry::make('issue_date')
                        ->label('Issue Date')
                        ->date('d M Y')
                        ->placeholder('—'),

                    TextEntry::make('expiry_date')
                        ->label('Expiry Date')
                        ->date('d M Y')
                        ->color(fn ($record) => $record->is_expired ? 'danger' : ($record->is_expiring_soon ? 'warning' : 'success'))
                        ->weight('bold'),
                ]),

            Section::make('Validity Status')
                ->columns(2)
                ->schema([
                    TextEntry::make('validity')
                        ->label('Current Status')
                        ->getStateUsing(fn ($record) => $record->is_expired
                            ? 'Expired'
                            : ($record->is_expiring_soon ? 'Expiring Soon' : 'Valid')
                        )
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Expired'       => 'danger',
                            'Expiring Soon' => 'warning',
                            'Valid'         => 'success',
                            default         => 'gray',
                        }),

                    TextEntry::make('days_until_expiry')
                        ->label('Days Until Expiry')
                        ->getStateUsing(fn ($record) => $record->expiry_date
                            ? (int) now()->diffInDays($record->expiry_date, false) . ' days'
                            : '—'
                        )
                        ->color(fn ($record) => $record->is_expired ? 'danger' : ($record->is_expiring_soon ? 'warning' : null)),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('No notes recorded'),
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
