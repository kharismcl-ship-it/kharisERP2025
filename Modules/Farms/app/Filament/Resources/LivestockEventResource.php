<?php

namespace Modules\Farms\Filament\Resources;

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
use Modules\Farms\Filament\Clusters\LivestockCluster;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\LivestockEventResource\Pages;
use Modules\Farms\Models\LivestockEvent;

class LivestockEventResource extends Resource
{
    protected static ?string $model = LivestockEvent::class;

    protected static ?string $cluster = LivestockCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 12;

    protected static ?string $navigationLabel = 'Livestock Events';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Event Details')
                ->columns(3)
                ->schema([
                    Select::make('livestock_batch_id')
                        ->label('Livestock Batch')
                        ->relationship('livestockBatch', 'batch_reference')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('event_type')
                        ->options(array_combine(
                            LivestockEvent::EVENT_TYPES,
                            array_map('ucfirst', LivestockEvent::EVENT_TYPES)
                        ))
                        ->required(),

                    DatePicker::make('event_date')->required()->default(now()),

                    TextInput::make('count')
                        ->label('Animals Involved')
                        ->integer()
                        ->default(1)
                        ->required(),

                    TextInput::make('source_or_destination')
                        ->label('Supplier / Buyer / Destination')
                        ->maxLength(255),
                ]),

            Section::make('Financial')
                ->columns(2)
                ->schema([
                    TextInput::make('unit_cost')
                        ->label('Unit Price (GHS)')
                        ->numeric()
                        ->step(0.0001)
                        ->prefix('GHS')
                        ->helperText('Purchase or sale price per animal — total auto-calculated on save'),
                    TextInput::make('total_value')
                        ->label('Total Value (GHS)')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('GHS'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('description')->rows(2)->columnSpanFull(),
                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('livestockBatch.batch_reference')->label('Batch')->searchable(),

                TextColumn::make('event_type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'birth', 'purchase', 'transfer_in' => 'success',
                        'death'                             => 'danger',
                        'sale', 'transfer_out'              => 'warning',
                        default                             => 'gray',
                    }),

                TextColumn::make('event_date')->date()->sortable(),
                TextColumn::make('count')->label('Count'),
                TextColumn::make('total_value')->money('GHS')->label('Value'),
                TextColumn::make('source_or_destination')->label('From/To')->limit(25)->toggleable(),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('event_type')
                    ->options(array_combine(
                        LivestockEvent::EVENT_TYPES,
                        array_map('ucfirst', LivestockEvent::EVENT_TYPES)
                    )),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('event_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLivestockEvents::route('/'),
            'create' => Pages\CreateLivestockEvent::route('/create'),
            'view'   => Pages\ViewLivestockEvent::route('/{record}'),
            'edit'   => Pages\EditLivestockEvent::route('/{record}/edit'),
        ];
    }
}