<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\VendorPerformanceResource\Pages;
use Modules\ProcurementInventory\Models\VendorPerformanceRecord;
use Modules\ProcurementInventory\Models\VendorScorecard;

class VendorPerformanceResource extends Resource
{
    protected static ?string $model = VendorScorecard::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?string $navigationLabel = 'Vendor Performance';

    protected static ?int $navigationSort = 26;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('period_year', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('period_year')
                    ->label('Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('period_month')
                    ->label('Month')
                    ->formatStateUsing(fn ($state) => date('F', mktime(0, 0, 0, (int) $state, 1)))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_orders')
                    ->label('Orders')
                    ->numeric(),

                Tables\Columns\TextColumn::make('on_time_rate')
                    ->label('On-Time %')
                    ->numeric(1)
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('avg_quality_rate')
                    ->label('Quality %')
                    ->numeric(1)
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('avg_price_variance_pct')
                    ->label('Price Variance %')
                    ->numeric(1)
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('overall_score')
                    ->label('Score')
                    ->badge()
                    ->color(fn ($state): string => (float) $state >= 80
                        ? 'success'
                        : ((float) $state >= 50 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 1) . '/100'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vendor')
                    ->relationship('vendor', 'name'),
            ])
            ->actions([
                Action::make('view_records')
                    ->label('View Records')
                    ->icon('heroicon-o-list-bullet')
                    ->modalHeading(fn ($record) => "Performance Records — {$record->vendor?->name}")
                    ->modalContent(function ($record) {
                        $records = VendorPerformanceRecord::where('vendor_id', $record->vendor_id)
                            ->where('company_id', $record->company_id)
                            ->whereYear('created_at', $record->period_year)
                            ->whereMonth('created_at', $record->period_month)
                            ->with(['purchaseOrder', 'goodsReceipt'])
                            ->get();

                        return view('procurementinventory::filament.modals.vendor-performance-records', compact('records'));
                    })
                    ->modalSubmitAction(false),
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
            'index' => Pages\ListVendorPerformances::route('/'),
        ];
    }
}