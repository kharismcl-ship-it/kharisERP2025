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
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;
use Modules\Farms\Filament\Resources\FarmProduceLotResource\Pages;
use Modules\Farms\Models\FarmProduceLot;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\HarvestRecord;
use Modules\Farms\Models\FarmProduceInventory;

class FarmProduceLotResource extends Resource
{
    protected static ?string $model = FarmProduceLot::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm Operations';

    protected static ?string $navigationLabel = 'Produce Lots';

    protected static ?int $navigationSort = 26;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lot Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', Filament::getTenant()?->id)->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('harvest_record_id')
                        ->label('Harvest Record')
                        ->options(fn () => HarvestRecord::where('company_id', Filament::getTenant()?->id)
                            ->with('farm')
                            ->get()
                            ->mapWithKeys(fn ($r) => [$r->id => $r->farm->name . ' — ' . $r->harvest_date?->format('d M Y')]))
                        ->searchable()
                        ->nullable(),

                    Select::make('produce_inventory_id')
                        ->label('Produce Type')
                        ->options(fn () => FarmProduceInventory::where('company_id', Filament::getTenant()?->id)
                            ->pluck('produce_name', 'id'))
                        ->searchable()
                        ->nullable(),

                    TextInput::make('lot_number')
                        ->label('Lot Number')
                        ->disabled()
                        ->dehydrated(true)
                        ->helperText('Auto-generated on save'),

                    TextInput::make('quantity_kg')->label('Quantity (kg)')->numeric()->step(0.01)->required(),
                    TextInput::make('unit')->default('kg')->maxLength(20),

                    DatePicker::make('harvest_date')->nullable(),
                    DatePicker::make('expiry_date')->nullable(),

                    TextInput::make('storage_location')->nullable()->maxLength(200),

                    Select::make('quality_grade')
                        ->options(['A' => 'Grade A', 'B' => 'Grade B', 'C' => 'Grade C', 'ungraded' => 'Ungraded'])
                        ->default('ungraded'),

                    TextInput::make('moisture_content_pct')->label('Moisture Content (%)')->numeric()->step(0.01)->nullable(),
                    TextInput::make('aflatoxin_ppb')->label('Aflatoxin (ppb)')->numeric()->step(0.0001)->nullable(),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lot_number')->sortable()->searchable()->copyable(),
                TextColumn::make('farm.name')->sortable(),
                TextColumn::make('produceInventory.produce_name')->label('Produce')->toggleable(),
                TextColumn::make('quantity_kg')->label('Qty (kg)')->numeric(2),
                TextColumn::make('quality_grade')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'A'       => 'success',
                        'B'       => 'warning',
                        'C'       => 'orange',
                        default   => 'gray',
                    }),
                TextColumn::make('harvest_date')->date()->placeholder('—'),
                TextColumn::make('expiry_date')->date()->placeholder('—'),
                IconColumn::make('is_recalled')->label('Recalled')->boolean(),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('quality_grade')->options([
                    'A' => 'Grade A', 'B' => 'Grade B', 'C' => 'Grade C', 'ungraded' => 'Ungraded',
                ]),
                TernaryFilter::make('is_recalled'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                TableAction::make('recall')
                    ->label('Recall')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->visible(fn (FarmProduceLot $record): bool => ! $record->is_recalled)
                    ->form([
                        Textarea::make('recall_reason')->required()->rows(3),
                    ])
                    ->action(fn (FarmProduceLot $record, array $data) => $record->update([
                        'is_recalled'   => true,
                        'recall_reason' => $data['recall_reason'],
                    ]))
                    ->requiresConfirmation(),

                TableAction::make('traceability')
                    ->label('Traceability')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('info')
                    ->modalContent(function (FarmProduceLot $record): HtmlString {
                        $chain = $record->traceabilityChain();
                        $html = '<div class="space-y-2 text-sm">';
                        $html .= '<p><strong>Lot:</strong> ' . e($record->lot_number) . '</p>';
                        $html .= '<p><strong>Farm:</strong> ' . e($chain['farm']?->name ?? '—') . '</p>';
                        $html .= '<p><strong>Harvest Date:</strong> ' . ($record->harvest_date?->format('d M Y') ?? '—') . '</p>';
                        $html .= '<p><strong>Crop Cycle:</strong> ' . e($chain['crop_cycle']?->name ?? '—') . '</p>';
                        $html .= '<p><strong>Inputs Applied:</strong> ' . $chain['inputs']->count() . '</p>';
                        $html .= '<p><strong>Orders Shipped In:</strong> ' . $chain['orders']->count() . '</p>';
                        $html .= '<p><strong>Recall Status:</strong> ' . ($record->is_recalled ? '<span class="text-red-600">RECALLED</span>' : 'OK') . '</p>';
                        $html .= '</div>';

                        return new HtmlString($html);
                    })
                    ->modalHeading('Lot Traceability Chain')
                    ->modalSubmitAction(false),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmProduceLots::route('/'),
            'create' => Pages\CreateFarmProduceLot::route('/create'),
            'edit'   => Pages\EditFarmProduceLot::route('/{record}/edit'),
            'view'   => Pages\ViewFarmProduceLot::route('/{record}'),
        ];
    }
}