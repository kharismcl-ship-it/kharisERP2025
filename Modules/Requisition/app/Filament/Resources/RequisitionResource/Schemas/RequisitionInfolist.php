<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;
use Modules\Requisition\Models\Requisition;

class RequisitionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request Details')->schema([
                Grid::make(4)->schema([
                    TextEntry::make('reference')->badge()->color('primary'),
                    TextEntry::make('request_type')
                        ->badge()
                        ->formatStateUsing(fn ($state) => Requisition::TYPES[$state] ?? $state)
                        ->color(fn ($state) => match ($state) {
                            'fund'      => 'warning',
                            'material'  => 'info',
                            'equipment' => 'success',
                            'service'   => 'gray',
                            default     => 'primary',
                        }),
                    TextEntry::make('urgency')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'urgent' => 'danger',
                            'high'   => 'warning',
                            'medium' => 'info',
                            default  => 'gray',
                        }),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'approved', 'fulfilled'        => 'success',
                            'submitted'                    => 'info',
                            'under_review'                 => 'warning',
                            'pending_revision'             => 'warning',
                            'rejected', 'cancelled'        => 'danger',
                            'closed'                       => 'gray',
                            default                        => 'gray',
                        }),
                ]),
                TextEntry::make('title')->columnSpanFull(),
                TextEntry::make('description')->columnSpanFull(),
                Grid::make(2)->schema([
                    TextEntry::make('requesterEmployee.full_name')->label('Requester'),
                    TextEntry::make('company.name')->label('Requesting Company'),
                    TextEntry::make('targetCompany.name')->label('Target Company')->placeholder('—'),
                    TextEntry::make('targetDepartment.name')->label('Target Department')->placeholder('—'),
                ]),
                Grid::make(2)->schema([
                    TextEntry::make('due_by')->label('Due By')->date()->placeholder('—'),
                    TextEntry::make('created_at')->label('Raised On')->dateTime(),
                ]),
            ]),

            Section::make('Budget & Procurement')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('costCentre.name')->label('Cost Centre')->placeholder('—'),
                    TextEntry::make('total_estimated_cost')
                        ->label('Total Estimated Cost')
                        ->money('GHS')
                        ->placeholder('—'),
                    TextEntry::make('preferredVendor.name')->label('Preferred Vendor')->placeholder('—'),
                ]),
            ]),

            Section::make('Resolution')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('approvedByUser.name')->label('Approved By')->placeholder('—'),
                    TextEntry::make('approved_at')->label('Approved At')->dateTime()->placeholder('—'),
                    TextEntry::make('fulfilled_at')->label('Fulfilled At')->dateTime()->placeholder('—'),
                ]),
                TextEntry::make('rejection_reason')
                    ->label('Rejection / Revision Notes')
                    ->columnSpanFull()
                    ->visible(fn ($record) => filled($record?->rejection_reason)),
                TextEntry::make('cancellation_reason')
                    ->label('Cancellation Reason')
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record?->status === 'cancelled' && filled($record?->cancellation_reason)),
                TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
            ]),

            Section::make('Notification Preferences')->schema([
                TextEntry::make('notification_channels')
                    ->label('Active Channels')
                    ->formatStateUsing(fn ($state) => is_array($state)
                        ? implode(', ', array_map(fn ($ch) => Requisition::NOTIFICATION_CHANNELS[$ch] ?? $ch, $state))
                        : '—'
                    )
                    ->placeholder('—'),
            ]),

            Section::make('Comments & Discussion')->schema([
                CommentsEntry::make('comments')->columnSpanFull(),
            ]),
        ]);
    }
}