<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Facades\Filament;
use Modules\Farms\Filament\Resources\FarmPostHarvestResource\Pages;
use Modules\Farms\Models\FarmPostHarvestRecord;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\HarvestRecord;
use Modules\Farms\Models\FarmStorageLocation;
use Modules\Farms\Models\FarmProduceLot;
use Modules\Farms\Models\FarmWorker;

class FarmPostHarvestResource extends Resource
{
    protected static ?string $model = FarmPostHarvestRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm Operations';

    protected static ?string $navigationLabel = 'Post-Harvest Records';

    protected static ?int $navigationSort = 28;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Post-Harvest Record')
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
                        ->required(),

                    Select::make('farm_storage_location_id')
                        ->label('Storage Location')
                        ->options(fn () => FarmStorageLocation::where('company_id', Filament::getTenant()?->id)
                            ->active()->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    Select::make('farm_produce_lot_id')
                        ->label('Produce Lot')
                        ->options(fn () => FarmProduceLot::where('company_id', Filament::getTenant()?->id)
                            ->pluck('lot_number', 'id'))
                        ->searchable()
                        ->nullable(),

                    Select::make('record_type')
                        ->options([
                            'grading'      => 'Grading',
                            'storage_in'   => 'Storage In',
                            'storage_out'  => 'Storage Out',
                            'loss_record'  => 'Loss Record',
                            'treatment'    => 'Treatment',
                            'quality_test' => 'Quality Test',
                        ])
                        ->required()
                        ->live(),

                    DatePicker::make('record_date')->required()->default(now()),

                    Select::make('recorded_by_worker_id')
                        ->label('Recorded By')
                        ->options(fn () => FarmWorker::where('company_id', Filament::getTenant()?->id)
                            ->where('is_active', true)->pluck('name', 'id'))
                        ->nullable(),
                ]),

            Section::make('Grading Quantities')
                ->columns(4)
                ->collapsible()
                ->schema([
                    TextInput::make('grade_a_qty')->label('Grade A (kg)')->numeric()->step(0.01)->nullable(),
                    TextInput::make('grade_b_qty')->label('Grade B (kg)')->numeric()->step(0.01)->nullable(),
                    TextInput::make('grade_c_qty')->label('Grade C (kg)')->numeric()->step(0.01)->nullable(),
                    TextInput::make('reject_qty')->label('Reject (kg)')->numeric()->step(0.01)->nullable(),
                ]),

            Section::make('Loss / Treatment / Quality')
                ->columns(2)
                ->collapsible()
                ->schema([
                    TextInput::make('total_loss_qty')->label('Total Loss (kg)')->numeric()->step(0.01)->nullable(),
                    Select::make('loss_cause')->options([
                        'spoilage'   => 'Spoilage',
                        'pest_damage' => 'Pest Damage',
                        'moisture'   => 'Moisture',
                        'mechanical' => 'Mechanical',
                        'theft'      => 'Theft',
                        'unknown'    => 'Unknown',
                    ])->nullable(),
                    TextInput::make('treatment_type')->nullable()->maxLength(200),
                    TextInput::make('quality_test_type')->nullable()->maxLength(200),
                    TextInput::make('quality_test_result')->nullable()->maxLength(200),
                    Toggle::make('quality_test_passed')->label('Quality Test Passed'),
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
                TextColumn::make('farm.name')->sortable(),
                TextColumn::make('harvestRecord.harvest_date')->label('Harvest Date')->date()->placeholder('—'),
                TextColumn::make('record_type')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'grading'      => 'info',
                        'storage_in'   => 'success',
                        'storage_out'  => 'warning',
                        'loss_record'  => 'danger',
                        'treatment'    => 'gray',
                        'quality_test' => 'primary',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('grade_a_qty')->label('A (kg)')->numeric(2)->toggleable(),
                TextColumn::make('total_loss_qty')->label('Loss (kg)')->numeric(2)->toggleable(),
                IconColumn::make('quality_test_passed')->label('QC Passed')->boolean()->toggleable(),
                TextColumn::make('record_date')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('record_type')->options([
                    'grading' => 'Grading', 'storage_in' => 'Storage In', 'storage_out' => 'Storage Out',
                    'loss_record' => 'Loss Record', 'treatment' => 'Treatment', 'quality_test' => 'Quality Test',
                ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('record_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmPostHarvestRecords::route('/'),
            'create' => Pages\CreateFarmPostHarvestRecord::route('/create'),
            'edit'   => Pages\EditFarmPostHarvestRecord::route('/{record}/edit'),
        ];
    }
}