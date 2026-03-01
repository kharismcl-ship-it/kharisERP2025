<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmSaleResource\Pages;
use Modules\Farms\Models\FarmSale;

class FarmSaleResource extends Resource
{
    protected static ?string $model = FarmSale::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Farm Sales';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Sale Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    DatePicker::make('sale_date')->required(),

                    TextInput::make('product_name')->required()->maxLength(255),

                    Select::make('product_type')
                        ->options(array_combine(
                            FarmSale::PRODUCT_TYPES,
                            array_map('ucfirst', FarmSale::PRODUCT_TYPES)
                        ))
                        ->default('crop'),

                    Select::make('crop_cycle_id')
                        ->label('Crop Cycle (optional)')
                        ->relationship('cropCycle', 'crop_name')
                        ->searchable()
                        ->nullable(),

                    Select::make('livestock_batch_id')
                        ->label('Livestock Batch (optional)')
                        ->relationship('livestockBatch', 'batch_reference')
                        ->searchable()
                        ->nullable(),
                ]),

            Section::make('Quantities & Pricing')
                ->columns(4)
                ->schema([
                    TextInput::make('quantity')->required()->numeric()->step(0.001),
                    TextInput::make('unit')->maxLength(50)->placeholder('kg, bags, head'),
                    TextInput::make('unit_price')->label('Unit Price (GHS)')->required()->numeric()->prefix('GHS')->step(0.0001),
                    TextInput::make('total_amount')->label('Total (GHS)')->numeric()->prefix('GHS')->step(0.01)
                        ->helperText('Auto-calculated from quantity × unit price.'),
                ]),

            Section::make('Buyer & Payment')
                ->columns(2)
                ->schema([
                    TextInput::make('buyer_name')->maxLength(255)->placeholder('—'),
                    TextInput::make('buyer_contact')->maxLength(255)->placeholder('Phone / Email'),

                    Select::make('payment_status')
                        ->options(array_combine(
                            FarmSale::PAYMENT_STATUSES,
                            array_map('ucfirst', FarmSale::PAYMENT_STATUSES)
                        ))
                        ->default('pending'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([Textarea::make('notes')->rows(2)->columnSpanFull()]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sale_date')->date('d M Y')->sortable(),
                TextColumn::make('product_name')->searchable()->limit(30),

                TextColumn::make('product_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'crop'      => 'success',
                        'livestock' => 'info',
                        'processed' => 'primary',
                        default     => 'gray',
                    }),

                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('quantity')->numeric(decimalPlaces: 2),
                TextColumn::make('unit')->placeholder('—'),
                TextColumn::make('unit_price')->money('GHS')->label('Unit Price'),
                TextColumn::make('total_amount')->money('GHS')->sortable(),

                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'    => 'success',
                        'partial' => 'warning',
                        'pending' => 'danger',
                        default   => 'gray',
                    }),

                TextColumn::make('invoice_id')
                    ->label('Invoice')
                    ->formatStateUsing(fn ($state) => $state ? '#' . $state : '—')
                    ->color(fn ($state) => $state ? 'primary' : null),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('product_type')
                    ->options(array_combine(FarmSale::PRODUCT_TYPES, array_map('ucfirst', FarmSale::PRODUCT_TYPES))),
                SelectFilter::make('payment_status')
                    ->options(array_combine(FarmSale::PAYMENT_STATUSES, array_map('ucfirst', FarmSale::PAYMENT_STATUSES))),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('create_invoice')
                    ->label('Create Invoice')
                    ->icon('heroicon-o-document-plus')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => ! $record->invoice_id)
                    ->action(function ($record) {
                        // Delegate to FarmService
                        app(\Modules\Farms\Services\FarmService::class)->createSaleInvoice($record);
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('sale_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmSales::route('/'),
            'create' => Pages\CreateFarmSale::route('/create'),
            'view'   => Pages\ViewFarmSale::route('/{record}'),
            'edit'   => Pages\EditFarmSale::route('/{record}/edit'),
        ];
    }
}
