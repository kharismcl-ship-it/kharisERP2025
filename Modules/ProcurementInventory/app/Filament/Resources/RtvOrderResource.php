<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\RtvOrderResource\Pages;
use Modules\ProcurementInventory\Models\GoodsReceipt;
use Modules\ProcurementInventory\Models\RtvOrder;
use Modules\ProcurementInventory\Models\Vendor;

class RtvOrderResource extends Resource
{
    protected static ?string $model = RtvOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?string $navigationLabel = 'Returns to Vendor';

    protected static ?int $navigationSort = 52;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Return Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('vendor_id')
                        ->label('Vendor')
                        ->options(function () {
                            $companyId = filament()->getTenant()?->id ?? auth()->user()?->current_company_id;
                            return Vendor::query()
                                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                                ->pluck('name', 'id');
                        })
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('goods_receipt_id')
                        ->label('Goods Receipt')
                        ->options(function () {
                            $companyId = filament()->getTenant()?->id ?? auth()->user()?->current_company_id;
                            return GoodsReceipt::query()
                                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                                ->where('status', 'confirmed')
                                ->get()
                                ->mapWithKeys(fn ($g) => [$g->id => $g->grn_number]);
                        })
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('rtv_number')
                        ->disabled()
                        ->placeholder('Auto-generated'),

                    Forms\Components\DatePicker::make('return_date')
                        ->required()
                        ->default(now()),

                    Forms\Components\Select::make('status')
                        ->options([
                            'draft'     => 'Draft',
                            'submitted' => 'Submitted',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('draft'),

                    Forms\Components\Toggle::make('debit_note_raised')
                        ->default(false)
                        ->label('Debit Note Raised'),
                ]),

            Section::make('Return Reason')
                ->schema([
                    Forms\Components\Textarea::make('reason')
                        ->required()
                        ->rows(4),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('rtv_number')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('vendor.name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('return_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                Tables\Columns\IconColumn::make('debit_note_raised')
                    ->boolean()
                    ->label('Debit Note'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'submitted' => 'Submitted',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('vendor')
                    ->relationship('vendor', 'name'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (RtvOrder $record) => $record->status === 'draft'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRtvOrders::route('/'),
            'create' => Pages\CreateRtvOrder::route('/create'),
            'edit'   => Pages\EditRtvOrder::route('/{record}/edit'),
        ];
    }
}