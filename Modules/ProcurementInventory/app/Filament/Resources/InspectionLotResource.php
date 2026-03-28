<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\InspectionLotResource\Pages;
use Modules\ProcurementInventory\Models\InspectionLot;

class InspectionLotResource extends Resource
{
    protected static ?string $model = InspectionLot::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?string $navigationLabel = 'Inspections';

    protected static ?int $navigationSort = 51;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('lot_number')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('goodsReceipt.grn_number')
                    ->label('GRN Number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'              => 'gray',
                        'in_progress'          => 'info',
                        'passed'               => 'success',
                        'conditionally_passed' => 'warning',
                        'failed'               => 'danger',
                        default                => 'gray',
                    }),

                Tables\Columns\TextColumn::make('overall_result')
                    ->badge()
                    ->placeholder('—')
                    ->color(fn (?string $state): string => match ($state) {
                        'accept'           => 'success',
                        'conditional_accept' => 'warning',
                        'reject'           => 'danger',
                        default            => 'gray',
                    }),

                Tables\Columns\TextColumn::make('inspection_date')
                    ->date()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('inspectedBy.name')
                    ->label('Inspected By')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'              => 'Pending',
                        'in_progress'          => 'In Progress',
                        'passed'               => 'Passed',
                        'conditionally_passed' => 'Conditionally Passed',
                        'failed'               => 'Failed',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInspectionLots::route('/'),
            'view'  => Pages\ViewInspectionLot::route('/{record}'),
        ];
    }
}