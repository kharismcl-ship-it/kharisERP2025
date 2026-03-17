<?php

namespace Modules\HR\Filament\Resources\Staff\MyContractResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContractInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contract Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('contract_number')
                        ->badge()
                        ->color('info'),
                    TextEntry::make('contract_type')
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state ?? ''))),
                    TextEntry::make('is_current')
                        ->label('Current Contract')
                        ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                    TextEntry::make('start_date')->date(),
                    TextEntry::make('end_date')
                        ->date()
                        ->placeholder('Indefinite'),
                    TextEntry::make('probation_end_date')
                        ->date()
                        ->placeholder('—'),
                ]),
            Section::make('Compensation')
                ->columns(3)
                ->schema([
                    TextEntry::make('basic_salary')
                        ->numeric(2),
                    TextEntry::make('currency'),
                    TextEntry::make('working_hours_per_week')
                        ->suffix(' hrs/wk'),
                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}
