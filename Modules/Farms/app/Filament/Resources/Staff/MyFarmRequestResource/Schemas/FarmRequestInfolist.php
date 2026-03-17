<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FarmRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('reference')
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('request_type')
                        ->label('Type')
                        ->formatStateUsing(fn ($state) => ucfirst($state)),

                    TextEntry::make('urgency')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'urgent' => 'danger',
                            'high'   => 'warning',
                            'medium' => 'info',
                            'low'    => 'gray',
                            default  => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst($state)),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'draft'     => 'gray',
                            'submitted' => 'warning',
                            'approved'  => 'success',
                            'rejected'  => 'danger',
                            'fulfilled' => 'primary',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst($state)),

                    TextEntry::make('farm.name')
                        ->label('Farm')
                        ->placeholder('—'),

                    TextEntry::make('title')
                        ->columnSpanFull(),

                    TextEntry::make('description')
                        ->label('Description')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),

            Section::make('Decision')
                ->columns(2)
                ->visible(fn ($record) => in_array($record->status, ['approved', 'rejected', 'fulfilled']))
                ->schema([
                    TextEntry::make('approvedBy.name')
                        ->label('Decided By')
                        ->placeholder('—'),

                    TextEntry::make('approved_at')
                        ->label('Decision Date')
                        ->dateTime()
                        ->placeholder('—'),

                    TextEntry::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->columnSpanFull()
                        ->placeholder('—')
                        ->visible(fn ($record) => $record->status === 'rejected'),
                ]),
        ]);
    }
}
