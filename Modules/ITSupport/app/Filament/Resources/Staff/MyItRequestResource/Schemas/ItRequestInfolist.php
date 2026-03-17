<?php

namespace Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\ITSupport\Models\ItRequest;

class ItRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Your Request')
                ->columns(3)
                ->schema([
                    TextEntry::make('reference')->badge()->color('gray')->columnSpan(1),
                    TextEntry::make('category')
                        ->formatStateUsing(fn ($state) => ItRequest::CATEGORIES[$state] ?? ucfirst($state))
                        ->badge(),
                    TextEntry::make('priority')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'critical' => 'danger',
                            'high'     => 'warning',
                            'medium'   => 'info',
                            default    => 'gray',
                        }),
                    TextEntry::make('subject')->columnSpanFull()->weight('bold'),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                    TextEntry::make('department.name')->label('Department')->placeholder('—'),
                    TextEntry::make('created_at')->dateTime()->label('Submitted'),
                ]),

            Section::make('Status & Assignment')
                ->columns(3)
                ->schema([
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'resolved'     => 'success',
                            'closed'       => 'success',
                            'in_progress'  => 'warning',
                            'pending_info' => 'gray',
                            'open'         => 'info',
                            'cancelled'    => 'danger',
                            default        => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ItRequest::STATUSES[$state] ?? ucfirst($state)),
                    TextEntry::make('assignedToEmployee.full_name')
                        ->label('Assigned To')
                        ->placeholder('Unassigned'),
                    TextEntry::make('estimated_resolution_date')
                        ->date()
                        ->label('Est. Resolution')
                        ->placeholder('—'),
                ]),

            Section::make('Resolution')
                ->columns(2)
                ->visible(fn ($record) => in_array($record->status, ['resolved', 'closed']))
                ->schema([
                    TextEntry::make('resolved_at')->dateTime()->label('Resolved At')->placeholder('—'),
                    TextEntry::make('resolution_notes')->label('Resolution Notes')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }
}
