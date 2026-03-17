<?php

namespace Modules\HR\Filament\Resources\Staff\MyGrievanceResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Models\GrievanceCase;

class GrievanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Grievance Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('grievance_type')
                        ->label('Type')
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state ?? ''))),
                    TextEntry::make('filed_date')->date(),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'filed'               => 'warning',
                            'under_investigation' => 'info',
                            'hearing_scheduled'   => 'primary',
                            'resolved'            => 'success',
                            'closed'              => 'gray',
                            'escalated'           => 'danger',
                            default               => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => GrievanceCase::STATUSES[$state] ?? ucfirst(str_replace('_', ' ', $state ?? ''))),
                    TextEntry::make('is_anonymous')
                        ->label('Anonymous')
                        ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                    TextEntry::make('description')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
            Section::make('Resolution')
                ->columns(2)
                ->visible(fn ($record) => in_array($record->status, ['resolved', 'closed']))
                ->schema([
                    TextEntry::make('assignedTo.full_name')
                        ->label('Handled By')
                        ->placeholder('—'),
                    TextEntry::make('resolution_date')
                        ->date()
                        ->placeholder('—'),
                    TextEntry::make('resolution')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}
