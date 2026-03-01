<?php

namespace Modules\Construction\Filament\Resources\ContractorResource\Pages;

use Carbon\Carbon;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Construction\Filament\Resources\ContractorResource;

class ViewContractor extends ViewRecord
{
    protected static string $resource = ContractorResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contractor Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->weight('bold'),
                    TextEntry::make('specialization')
                        ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : '—'),
                    TextEntry::make('contact_person')->label('Contact Person')->placeholder('—'),
                    TextEntry::make('phone')->placeholder('—'),
                    TextEntry::make('email')->placeholder('—'),
                    TextEntry::make('address')->placeholder('—')->columnSpanFull(),
                    IconEntry::make('is_active')->label('Active')->boolean(),
                ]),

            Section::make('License')
                ->columns(2)
                ->schema([
                    TextEntry::make('license_number')->label('License No.')->placeholder('—'),
                    TextEntry::make('license_expiry')
                        ->label('License Expiry')
                        ->date()
                        ->placeholder('—')
                        ->color(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'danger' : null),
                ]),

            Section::make('Task Summary')
                ->columns(3)
                ->schema([
                    TextEntry::make('total_tasks')
                        ->label('Total Tasks')
                        ->getStateUsing(fn ($record) => $record->tasks()->count()),
                    TextEntry::make('open_tasks')
                        ->label('Open / In Progress')
                        ->getStateUsing(fn ($record) => $record->tasks()
                            ->whereIn('status', ['pending', 'in_progress'])->count()),
                    TextEntry::make('completed_tasks')
                        ->label('Completed')
                        ->getStateUsing(fn ($record) => $record->tasks()
                            ->where('status', 'completed')->count()),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')->placeholder('None'),
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
