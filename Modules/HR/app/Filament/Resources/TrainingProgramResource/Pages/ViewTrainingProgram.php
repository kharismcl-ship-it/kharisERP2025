<?php

namespace Modules\HR\Filament\Resources\TrainingProgramResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\TrainingProgramResource;
use Modules\HR\Models\TrainingProgram;

class ViewTrainingProgram extends ViewRecord
{
    protected static string $resource = TrainingProgramResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Program Details')
                    ->collapsible()
                    ->columns(['default' => 2, 'xl' => 3])
                    ->schema([
                        TextEntry::make('title')->label('Program Title')->weight('bold')->columnSpanFull(),
                        TextEntry::make('company.name'),
                        TextEntry::make('type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'internal' => 'primary', 'external' => 'info',
                                'online' => 'success', 'conference' => 'warning', default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'planned' => 'gray', 'ongoing' => 'warning',
                                'completed' => 'success', 'cancelled' => 'danger', default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                        TextEntry::make('provider')->placeholder('—'),
                        TextEntry::make('max_participants')->label('Max Participants')->placeholder('Unlimited'),
                        TextEntry::make('cost')->money('GHS')->placeholder('Free'),
                        TextEntry::make('start_date')->date(),
                        TextEntry::make('end_date')->date()->placeholder('Ongoing'),
                    ]),

                Section::make('Description')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('description')->columnSpanFull()->placeholder('No description'),
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