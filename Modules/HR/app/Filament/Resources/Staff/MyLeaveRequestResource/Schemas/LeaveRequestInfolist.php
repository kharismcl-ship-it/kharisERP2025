<?php

namespace Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeaveRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Leave Request Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('leaveType.name')->label('Leave Type'),
                    TextEntry::make('start_date')->date(),
                    TextEntry::make('end_date')->date(),
                    TextEntry::make('total_days')->suffix(' days'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending'          => 'warning',
                            'pending_approval' => 'info',
                            'approved'         => 'success',
                            'rejected'         => 'danger',
                            default            => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'pending_approval' => 'In Review',
                            default            => ucfirst(str_replace('_', ' ', $state ?? '')),
                        }),
                    TextEntry::make('reason')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
            Section::make('Decision')
                ->columns(3)
                ->visible(fn ($record) => $record->status !== 'pending')
                ->schema([
                    TextEntry::make('approvedBy.full_name')
                        ->label('Decided By')
                        ->placeholder('—'),
                    TextEntry::make('approved_at')
                        ->dateTime()
                        ->label('Decision Date')
                        ->placeholder('—'),
                    TextEntry::make('rejected_reason')
                        ->label('Rejection Reason')
                        ->columnSpanFull()
                        ->placeholder('—')
                        ->visible(fn ($record) => $record->status === 'rejected'),
                ]),
        ]);
    }
}
