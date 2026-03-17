<?php

namespace Modules\Sales\Filament\Resources\Staff\MyOpportunityResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OpportunityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Opportunity')
                ->columns(3)
                ->schema([
                    TextEntry::make('title')->columnSpanFull()->weight('bold'),
                    TextEntry::make('stage')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'closed_won'  => 'success',
                            'closed_lost' => 'danger',
                            'negotiation' => 'warning',
                            default       => 'info',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),
                    TextEntry::make('probability_pct')->label('Probability')->suffix('%'),
                    TextEntry::make('expected_close_date')->date()->label('Close Date')->placeholder('—'),
                    TextEntry::make('contact.full_name')->label('Contact')->placeholder('—'),
                    TextEntry::make('organization.name')->label('Organisation')->placeholder('—'),
                    TextEntry::make('estimated_value')->label('Estimated Value')->money('KES')->placeholder('—'),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }
}
