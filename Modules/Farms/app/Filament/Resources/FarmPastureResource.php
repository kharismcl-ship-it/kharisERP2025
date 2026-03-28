<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Modules\Farms\Models\FarmPasture;
use Modules\Farms\Models\FarmGrazingEvent;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\LivestockBatch;
use Filament\Facades\Filament;

class FarmPastureResource extends Resource
{
    protected static ?string $model = FarmPasture::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static string|\UnitEnum|null $navigationGroup = 'Livestock';
    protected static ?string $navigationLabel = 'Pastures / Grazing';
    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        $companyId = Filament::getTenant()?->id;

        return $schema->components([
            Section::make()->schema([
                Grid::make(2)->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', $companyId)->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    TextInput::make('pasture_name')
                        ->required()
                        ->maxLength(255),
                    Select::make('pasture_type')
                        ->options([
                            'native_grass'     => 'Native Grass',
                            'improved_pasture' => 'Improved Pasture',
                            'fodder_crop'      => 'Fodder Crop',
                            'browse'           => 'Browse / Shrubland',
                        ])
                        ->nullable(),
                    TextInput::make('area_ha')
                        ->numeric()
                        ->suffix('ha'),
                    TextInput::make('current_foo_kg_ha')
                        ->numeric()
                        ->label('Current FOO (kg/ha)'),
                    TextInput::make('target_foo_kg_ha')
                        ->numeric()
                        ->label('Target FOO (kg/ha)')
                        ->helperText('Move mob when FOO drops below this'),
                    TextInput::make('carrying_capacity_au_ha')
                        ->numeric()
                        ->label('Carrying Capacity (AU/ha)'),
                    TextInput::make('rest_days_required')
                        ->numeric()
                        ->label('Rest Days Required'),
                    DatePicker::make('last_grazed_date'),
                    DatePicker::make('available_from_date'),
                    Toggle::make('is_occupied')
                        ->label('Currently Occupied'),
                    Toggle::make('is_active')
                        ->default(true),
                ]),
                Textarea::make('notes')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pasture_name')->searchable()->sortable(),
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('pasture_type')->badge(),
                TextColumn::make('area_ha')->suffix(' ha')->label('Area'),
                TextColumn::make('current_foo_kg_ha')
                    ->label('FOO (kg/ha)')
                    ->color(fn ($record): string => ($record && $record->isLowFoo()) ? 'danger' : 'success'),
                TextColumn::make('currentBatch.name')
                    ->label('Current Batch')
                    ->placeholder('—'),
                IconColumn::make('is_occupied')
                    ->boolean()
                    ->label('Occupied'),
                TextColumn::make('available_from_date')
                    ->date()
                    ->label('Available From')
                    ->placeholder('—'),
            ])
            ->actions([
                Action::make('move_in')
                    ->label('Move Mob In')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->visible(fn ($record): bool => !$record->is_occupied)
                    ->form([
                        Select::make('livestock_batch_id')
                            ->label('Livestock Batch')
                            ->options(fn () => LivestockBatch::where('company_id', Filament::getTenant()?->id)
                                ->where('status', 'active')
                                ->pluck('name', 'id'))
                            ->required(),
                        TextInput::make('foo_kg_ha')
                            ->label('FOO at Move-In (kg/ha)')
                            ->numeric(),
                    ])
                    ->action(function ($record, array $data): void {
                        FarmGrazingEvent::create([
                            'company_id'         => $record->company_id,
                            'farm_pasture_id'    => $record->id,
                            'livestock_batch_id' => $data['livestock_batch_id'],
                            'event_type'         => 'move_in',
                            'event_date'         => today(),
                            'foo_kg_ha'          => $data['foo_kg_ha'] ?? null,
                        ]);
                        $record->update([
                            'is_occupied'       => true,
                            'current_batch_id'  => $data['livestock_batch_id'],
                        ]);
                    }),
                Action::make('move_out')
                    ->label('Move Mob Out')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('warning')
                    ->visible(fn ($record): bool => $record->is_occupied)
                    ->form([
                        TextInput::make('days_in_paddock')
                            ->label('Days in Paddock')
                            ->numeric()
                            ->required(),
                        DatePicker::make('available_from_date')
                            ->label('Rest Until')
                            ->required(),
                    ])
                    ->action(function ($record, array $data): void {
                        FarmGrazingEvent::create([
                            'company_id'         => $record->company_id,
                            'farm_pasture_id'    => $record->id,
                            'livestock_batch_id' => $record->current_batch_id,
                            'event_type'         => 'move_out',
                            'event_date'         => today(),
                            'days_in_paddock'    => $data['days_in_paddock'],
                        ]);
                        $record->update([
                            'is_occupied'         => false,
                            'current_batch_id'    => null,
                            'last_grazed_date'    => today(),
                            'available_from_date' => $data['available_from_date'],
                        ]);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\Farms\Filament\Resources\FarmPastureResource\Pages\ListFarmPastures::route('/'),
            'create' => \Modules\Farms\Filament\Resources\FarmPastureResource\Pages\CreateFarmPasture::route('/create'),
            'edit'   => \Modules\Farms\Filament\Resources\FarmPastureResource\Pages\EditFarmPasture::route('/{record}/edit'),
        ];
    }
}