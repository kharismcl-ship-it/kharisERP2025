<?php

namespace Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Requisition\Models\Requisition;

class RequisitionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Requisition')
                ->columns(3)
                ->schema([
                    TextEntry::make('reference')->badge()->color('gray'),
                    TextEntry::make('request_type')
                        ->label('Type')
                        ->formatStateUsing(fn ($state) => Requisition::TYPES[$state] ?? ucfirst($state))
                        ->badge(),
                    TextEntry::make('urgency')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'urgent' => 'danger',
                            'high'   => 'warning',
                            'medium' => 'info',
                            default  => 'gray',
                        }),
                    TextEntry::make('title')->columnSpanFull()->weight('bold'),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                    TextEntry::make('targetDepartment.name')->label('Department')->placeholder('—'),
                    TextEntry::make('due_by')->date()->label('Required By')->placeholder('—'),
                    TextEntry::make('total_estimated_cost')
                        ->label('Estimated Cost')
                        ->money('GHS')
                        ->placeholder('—'),
                    TextEntry::make('preferredVendor.name')
                        ->label('Preferred Vendor')
                        ->placeholder('—'),
                ]),

            Section::make('Status')
                ->columns(3)
                ->schema([
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'approved'         => 'success',
                            'fulfilled'        => 'success',
                            'rejected'         => 'danger',
                            'submitted'        => 'info',
                            'under_review'     => 'warning',
                            'pending_revision' => 'warning',
                            default            => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => Requisition::STATUSES[$state] ?? ucfirst($state)),
                    TextEntry::make('approved_at')->dateTime()->label('Approved At')->placeholder('—'),
                    TextEntry::make('fulfilled_at')->dateTime()->label('Fulfilled At')->placeholder('—'),
                    TextEntry::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->columnSpanFull()
                        ->placeholder('—')
                        ->visible(fn ($record) => $record->status === 'rejected'),
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }
}
