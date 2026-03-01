<?php

namespace Modules\Fleet\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Fleet\Filament\Resources\MaintenanceRecordResource\Pages;
use Modules\Fleet\Models\MaintenanceRecord;
use Modules\Fleet\Models\Vehicle;

class MaintenanceRecordResource extends Resource
{
    protected static ?string $model = MaintenanceRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string|\UnitEnum|null $navigationGroup = 'Fleet';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Grid::make(2)->schema([
                    Select::make('vehicle_id')
                        ->label('Vehicle')
                        ->relationship('vehicle', 'name')
                        ->searchable()
                        ->required(),
                    Select::make('type')
                        ->options(array_combine(
                            MaintenanceRecord::TYPES,
                            array_map('ucwords', array_map(fn ($t) => str_replace('_', ' ', $t), MaintenanceRecord::TYPES))
                        ))
                        ->required(),
                ]),
                Textarea::make('description')->required()->columnSpanFull(),
                Grid::make(3)->schema([
                    DatePicker::make('service_date')->required(),
                    TextInput::make('mileage_at_service')->label('Mileage at Service')->numeric()->step(0.01),
                    TextInput::make('cost')->numeric()->prefix('GHS')->step(0.01)->default(0),
                ]),
                Grid::make(3)->schema([
                    DatePicker::make('next_service_date')->label('Next Service Date'),
                    TextInput::make('next_service_mileage')->label('Next Service Mileage')->numeric()->step(0.01),
                    Select::make('status')
                        ->options([
                            'scheduled'   => 'Scheduled',
                            'in_progress' => 'In Progress',
                            'completed'   => 'Completed',
                        ])
                        ->default('completed'),
                ]),
                TextInput::make('service_provider')->label('Service Provider / Garage')->maxLength(255),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.name')->label('Vehicle')->searchable()->sortable(),
                TextColumn::make('vehicle.plate')->label('Plate'),
                TextColumn::make('service_date')->date()->sortable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('description')->limit(40),
                TextColumn::make('cost')->money('GHS')->sortable(),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'scheduled'   => 'info',
                        default       => 'gray',
                    }),
                TextColumn::make('service_provider')->label('Provider'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(array_combine(
                        MaintenanceRecord::TYPES,
                        array_map('ucwords', array_map(fn ($t) => str_replace('_', ' ', $t), MaintenanceRecord::TYPES))
                    )),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled'   => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'completed'   => 'Completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('service_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMaintenanceRecords::route('/'),
            'create' => Pages\CreateMaintenanceRecord::route('/create'),
            'edit'   => Pages\EditMaintenanceRecord::route('/{record}/edit'),
        ];
    }
}
