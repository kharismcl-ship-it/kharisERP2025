<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Requisition\Models\RequisitionActivity;

class RequisitionActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Activity Log';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->formatStateUsing(fn ($state) => RequisitionActivity::ACTION_LABELS[$state] ?? $state)
                    ->color(fn ($state) => RequisitionActivity::ACTION_COLORS[$state] ?? 'gray'),
                TextColumn::make('description')
                    ->label('Detail')
                    ->wrap()
                    ->limit(120),
                TextColumn::make('from_status')
                    ->label('From')
                    ->badge()
                    ->color('gray')
                    ->default(''),
                TextColumn::make('to_status')
                    ->label('To')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved'   => 'success',
                        'rejected'   => 'danger',
                        'fulfilled'  => 'success',
                        'submitted'  => 'info',
                        'under_review', 'pending_revision' => 'warning',
                        default      => 'gray',
                    })
                    ->default(''),
                TextColumn::make('user.name')
                    ->label('By')
                    ->default('System'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}