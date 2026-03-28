<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\LandedCostResource\Pages;
use Modules\ProcurementInventory\Models\LandedCost;

class LandedCostResource extends Resource
{
    protected static ?string $model = LandedCost::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 53;

    protected static ?string $label = 'Landed Cost';

    protected static ?string $pluralLabel = 'Landed Costs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Landed Cost Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        ->searchable(),

                    Forms\Components\Select::make('goods_receipt_id')
                        ->label('Goods Receipt')
                        ->relationship('goodsReceipt', 'grn_number')
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('reference')
                        ->label('Reference / Customs Entry')
                        ->nullable(),

                    Forms\Components\Select::make('allocation_method')
                        ->options([
                            'by_value'    => 'By Line Value',
                            'by_quantity' => 'By Quantity',
                            'by_weight'   => 'By Weight',
                        ])
                        ->default('by_value')
                        ->required(),

                    Forms\Components\TextInput::make('total_freight')
                        ->label('Freight')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),

                    Forms\Components\TextInput::make('total_duty')
                        ->label('Duty')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),

                    Forms\Components\TextInput::make('total_insurance')
                        ->label('Insurance')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),

                    Forms\Components\TextInput::make('total_customs_fee')
                        ->label('Customs Fee')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),

                    Forms\Components\TextInput::make('total_other')
                        ->label('Other Charges')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),

                    Forms\Components\Textarea::make('notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('goodsReceipt.grn_number')
                    ->label('GRN')
                    ->searchable(),

                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money('GHS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('allocation_method')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'by_value'    => 'By Value',
                        'by_quantity' => 'By Quantity',
                        'by_weight'   => 'By Weight',
                        default       => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'allocated' => 'success',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'allocated' => 'Allocated',
                    ]),
            ])
            ->actions([
                Action::make('allocate')
                    ->label('Allocate')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn (LandedCost $record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->action(function (LandedCost $record) {
                        try {
                            $record->allocate();
                            Notification::make()->title('Landed costs allocated to GRN lines')->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                EditAction::make()
                    ->visible(fn (LandedCost $record) => $record->status === 'draft'),

                DeleteAction::make()
                    ->visible(fn (LandedCost $record) => $record->status === 'draft'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLandedCosts::route('/'),
            'create' => Pages\CreateLandedCost::route('/create'),
            'edit'   => Pages\EditLandedCost::route('/{record}/edit'),
        ];
    }
}