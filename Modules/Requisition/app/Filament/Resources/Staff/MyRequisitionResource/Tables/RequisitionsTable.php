<?php

namespace Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\Requisition\Models\Requisition;

class RequisitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref')
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')->wrap()->limit(50),
                Tables\Columns\TextColumn::make('request_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Requisition::TYPES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('urgency')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        default  => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'approved'  => 'success',
                        'fulfilled' => 'success',
                        'rejected'  => 'danger',
                        'submitted' => 'info',
                        'under_review' => 'warning',
                        'pending_revision' => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => Requisition::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('targetDepartment.name')
                    ->label('Department')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('total_estimated_cost')
                    ->label('Est. Cost')
                    ->money('GHS'),
                Tables\Columns\TextColumn::make('created_at')->date()->label('Submitted'),
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make()
                    ->visible(fn (Requisition $r) => in_array($r->status, ['draft', 'submitted', 'pending_revision'])),
            ]);
    }
}
