<?php

namespace Modules\HR\Filament\Resources\ShiftResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\ShiftResource;
use Modules\HR\Models\Shift;

class ViewShift extends ViewRecord
{
    protected static string $resource = ShiftResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Shift Information')
                    ->collapsible()
                    ->columns(['default' => 2, 'xl' => 3])
                    ->schema([
                        TextEntry::make('company.name')->label('Company'),
                        TextEntry::make('name')->label('Shift Name')->weight('bold'),
                        TextEntry::make('start_time')->label('Start Time'),
                        TextEntry::make('end_time')->label('End Time'),
                        TextEntry::make('break_duration_minutes')
                            ->label('Break Duration')
                            ->suffix(' minutes'),
                        TextEntry::make('day_names')
                            ->label('Working Days')
                            ->getStateUsing(fn (Shift $record) => $record->day_names),
                        IconEntry::make('is_active')->label('Active')->boolean(),
                    ]),

                Section::make('Description')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('description')->columnSpanFull()->placeholder('No description'),
                    ]),

                Section::make('Audit')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}