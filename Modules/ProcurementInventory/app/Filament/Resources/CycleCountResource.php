<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\CycleCountResource\Pages;
use Modules\ProcurementInventory\Filament\Resources\CycleCountResource\RelationManagers;
use Modules\ProcurementInventory\Models\CycleCount;
use Modules\ProcurementInventory\Models\CycleCountLine;
use Modules\ProcurementInventory\Models\StockLevel;
use Modules\ProcurementInventory\Services\CycleCountService;

class CycleCountResource extends Resource
{
    protected static ?string $model = CycleCount::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 66;

    protected static ?string $label = 'Cycle Count';

    protected static ?string $pluralLabel = 'Cycle Counts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Count Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        ->searchable(),

                    Forms\Components\Select::make('warehouse_id')
                        ->relationship('warehouse', 'name')
                        ->nullable()
                        ->searchable()
                        ->placeholder('All Warehouses'),

                    Forms\Components\Select::make('count_type')
                        ->options([
                            'full'    => 'Full Count',
                            'partial' => 'Partial Count',
                            'abc_a'   => 'ABC Class A',
                            'abc_b'   => 'ABC Class B',
                            'abc_c'   => 'ABC Class C',
                        ])
                        ->default('full')
                        ->required(),

                    Forms\Components\DatePicker::make('scheduled_date')
                        ->default(now())
                        ->required(),

                    Forms\Components\TextInput::make('variance_threshold_pct')
                        ->label('Variance Threshold (%)')
                        ->numeric()
                        ->default(5.00)
                        ->suffix('%'),

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
                Tables\Columns\TextColumn::make('count_number')
                    ->searchable()
                    ->weight('bold')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->placeholder('All'),

                Tables\Columns\TextColumn::make('count_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'full'    => 'Full',
                        'partial' => 'Partial',
                        'abc_a'   => 'ABC-A',
                        'abc_b'   => 'ABC-B',
                        'abc_c'   => 'ABC-C',
                        default   => $state,
                    }),

                Tables\Columns\TextColumn::make('scheduled_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('counted_date')
                    ->date()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled'   => 'gray',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('lines_count')
                    ->counts('lines')
                    ->label('Items'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled'   => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'completed'   => 'Completed',
                        'cancelled'   => 'Cancelled',
                    ]),
            ])
            ->actions([
                Action::make('start_count')
                    ->label('Start Count')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->visible(fn (CycleCount $record) => $record->status === 'scheduled')
                    ->requiresConfirmation()
                    ->action(function (CycleCount $record) {
                        // Load items into lines from current stock levels
                        $stockLevels = StockLevel::where('company_id', $record->company_id)
                            ->when($record->warehouse_id, fn ($q) => $q->where('warehouse_id', $record->warehouse_id))
                            ->get();

                        foreach ($stockLevels as $sl) {
                            CycleCountLine::firstOrCreate(
                                ['count_id' => $record->id, 'item_id' => $sl->item_id],
                                [
                                    'warehouse_id'    => $sl->warehouse_id,
                                    'system_quantity' => (float) $sl->quantity_on_hand,
                                    'status'          => 'pending',
                                ]
                            );
                        }

                        $record->update(['status' => 'in_progress']);
                        Notification::make()->title('Count started — enter counted quantities')->success()->send();
                    }),

                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (CycleCount $record) => in_array($record->status, ['scheduled'])),

                Action::make('apply_adjustments')
                    ->label('Apply Adjustments')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('success')
                    ->visible(fn (CycleCount $record) => $record->status === 'completed')
                    ->requiresConfirmation()
                    ->action(function (CycleCount $record) {
                        app(CycleCountService::class)->applyAdjustments($record);
                        Notification::make()->title('Stock adjustments applied')->success()->send();
                    }),

                DeleteAction::make()
                    ->visible(fn (CycleCount $record) => $record->status === 'scheduled'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CycleCountLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCycleCounts::route('/'),
            'create' => Pages\CreateCycleCount::route('/create'),
            'view'   => Pages\ViewCycleCount::route('/{record}'),
            'edit'   => Pages\EditCycleCount::route('/{record}/edit'),
        ];
    }
}