<?php

namespace Modules\Finance\Filament\Resources\Staff\MyFixedAssetResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\Finance\Models\FixedAsset;

class FixedAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('acquisition_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('asset_code')
                    ->label('Code')
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Serial')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('location')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('cost')
                    ->money('KES'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'      => 'success',
                        'disposed'    => 'gray',
                        'written_off' => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => FixedAsset::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('warranty_expiry_date')
                    ->label('Warranty Expires')
                    ->date()
                    ->placeholder('—'),
            ])
            ->actions([ViewAction::make()]);
    }
}
