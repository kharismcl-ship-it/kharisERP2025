<?php

namespace Modules\Farms\Filament\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;
use Modules\Farms\Models\FarmCommodityPrice;

class CommodityPriceFeedPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Market Intelligence';

    protected static ?string $navigationLabel = 'Commodity Prices';

    protected static ?int $navigationSort = 1;

    protected string $view = 'farms::filament.pages.commodity-price-feed';

    public function table(Table $table): Table
    {
        $companyId = Filament::getTenant()?->id;

        return $table
            ->query(
                FarmCommodityPrice::query()
                    ->where(fn ($q) => $q->whereNull('company_id')->orWhere('company_id', $companyId))
                    ->orderByDesc('price_date')
            )
            ->columns([
                TextColumn::make('commodity_name')->label('Commodity')->sortable()->searchable(),
                TextColumn::make('market_name')->label('Market')->sortable()->searchable(),
                TextColumn::make('price_per_unit')
                    ->label('Price')
                    ->formatStateUsing(fn ($state, $record): string => 'GHS ' . number_format((float) $state, 2) . ' / ' . $record->unit)
                    ->sortable(),
                TextColumn::make('price_date')->date()->sortable(),
                TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'esoko_api' => 'success',
                        'mofa_mis'  => 'info',
                        'manual'    => 'gray',
                        default     => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('commodity_name')
                    ->label('Commodity')
                    ->options(FarmCommodityPrice::distinct()->pluck('commodity_name', 'commodity_name')),
            ])
            ->defaultSort('price_date', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh_prices')
                ->label('Refresh Prices')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (): void {
                    Artisan::call('farms:fetch-commodity-prices');
                    $this->notify('success', 'Commodity prices refreshed.');
                }),

            Action::make('add_price')
                ->label('Add Manual Price')
                ->icon('heroicon-o-plus')
                ->form([
                    TextInput::make('commodity_name')->required()->maxLength(100),
                    TextInput::make('market_name')->required()->maxLength(150),
                    TextInput::make('price_per_unit')->label('Price')->numeric()->required()->prefix('GHS'),
                    Select::make('unit')
                        ->options(['kg' => 'kg', 'bag(50kg)' => 'bag (50kg)', 'crate' => 'crate', 'tonne' => 'tonne'])
                        ->default('kg')
                        ->required(),
                    DatePicker::make('price_date')->required()->default(today()),
                ])
                ->action(function (array $data): void {
                    $companyId = Filament::getTenant()?->id;
                    FarmCommodityPrice::create([
                        'company_id'     => $companyId,
                        'commodity_name' => $data['commodity_name'],
                        'market_name'    => $data['market_name'],
                        'price_per_unit' => $data['price_per_unit'],
                        'unit'           => $data['unit'],
                        'price_date'     => $data['price_date'],
                        'source'         => 'manual',
                    ]);
                    $this->notify('success', 'Price entry added.');
                }),
        ];
    }
}