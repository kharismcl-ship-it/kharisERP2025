<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Clusters\CropsCluster;
use Modules\Farms\Filament\Resources\HarvestRecordResource\Pages;
use Modules\Farms\Models\HarvestRecord;

class HarvestRecordResource extends Resource
{
    protected static ?string $model = HarvestRecord::class;

    protected static ?string $cluster = CropsCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Harvest Records';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Harvest Details')
                ->columns(3)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('crop_cycle_id')
                        ->label('Crop Cycle (optional)')
                        ->relationship('cropCycle', 'crop_name')
                        ->searchable()
                        ->nullable(),

                    DatePicker::make('harvest_date')
                        ->required()
                        ->default(now()),
                ]),

            Section::make('Quantity & Revenue')
                ->columns(3)
                ->schema([
                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->step(0.001)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, $set, $get) =>
                            $set('total_revenue', round((float) $state * (float) ($get('unit_price') ?? 0), 2))
                        ),

                    TextInput::make('unit')
                        ->label('Unit (kg / bags / tonnes / crates)')
                        ->required()
                        ->maxLength(50),

                    TextInput::make('unit_price')
                        ->label('Unit Price (GHS)')
                        ->numeric()
                        ->step(0.0001)
                        ->prefix('GHS')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, $set, $get) =>
                            $set('total_revenue', round((float) ($get('quantity') ?? 0) * (float) $state, 2))
                        ),

                    TextInput::make('total_revenue')
                        ->label('Total Revenue (GHS)')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01),
                ]),

            Section::make('Storage & Buyer')
                ->columns(2)
                ->schema([
                    TextInput::make('buyer_name')->maxLength(255)->placeholder('Leave blank if stored'),
                    TextInput::make('storage_location')->maxLength(255)->placeholder('e.g. Silo A, Cold Room 2'),
                ]),

            Section::make('Notes & Attachments')
                ->collapsible()
                ->schema([
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                    FileUpload::make('attachments')
                        ->multiple()
                        ->directory('harvest-records')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Harvest Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('farm.name')->label('Farm'),
                    TextEntry::make('cropCycle.crop_name')->label('Crop Cycle')->placeholder('—'),
                    TextEntry::make('harvest_date')->date(),
                ]),

            Section::make('Quantity & Revenue')
                ->columns(4)
                ->schema([
                    TextEntry::make('quantity')->numeric(3),
                    TextEntry::make('unit'),
                    TextEntry::make('unit_price')->money('GHS')->label('Unit Price'),
                    TextEntry::make('total_revenue')->money('GHS')->label('Total Revenue')->weight('bold'),
                ]),

            Section::make('Storage & Buyer')
                ->columns(2)
                ->schema([
                    TextEntry::make('buyer_name')->placeholder('—'),
                    TextEntry::make('storage_location')->placeholder('—'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable()->searchable(),
                TextColumn::make('cropCycle.crop_name')->label('Crop Cycle')->placeholder('—')->toggleable(),
                TextColumn::make('harvest_date')->date()->sortable(),
                TextColumn::make('quantity')->numeric(3)->suffix(fn ($record) => ' ' . $record->unit),
                TextColumn::make('unit_price')->money('GHS')->label('Unit Price')->toggleable(),
                TextColumn::make('total_revenue')->money('GHS')->label('Revenue')->sortable(),
                TextColumn::make('buyer_name')->placeholder('—')->toggleable(),
                TextColumn::make('storage_location')->placeholder('—')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('harvest_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHarvestRecords::route('/'),
            'create' => Pages\CreateHarvestRecord::route('/create'),
            'view'   => Pages\ViewHarvestRecord::route('/{record}'),
            'edit'   => Pages\EditHarvestRecord::route('/{record}/edit'),
        ];
    }
}
