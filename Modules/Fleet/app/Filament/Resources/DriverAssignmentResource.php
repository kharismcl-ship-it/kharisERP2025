<?php

namespace Modules\Fleet\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Fleet\Filament\Resources\DriverAssignmentResource\Pages;
use Modules\Fleet\Models\DriverAssignment;

class DriverAssignmentResource extends Resource
{
    protected static ?string $model = DriverAssignment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static string|\UnitEnum|null $navigationGroup = 'Fleet';

    protected static ?int $navigationSort = 6;

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
                    Select::make('user_id')
                        ->label('Driver (User)')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->nullable(),
                ]),
                Grid::make(3)->schema([
                    DatePicker::make('assigned_from')->required(),
                    DatePicker::make('assigned_until')->label('Assigned Until (leave blank = ongoing)')->nullable(),
                    Toggle::make('is_primary')->label('Primary Driver')->default(true)->inline(false),
                ]),
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
                TextColumn::make('user.name')->label('Driver')->searchable(),
                TextColumn::make('assigned_from')->date()->sortable(),
                TextColumn::make('assigned_until')->date()->label('Until')->default('Ongoing'),
                IconColumn::make('is_primary')->label('Primary')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Vehicle')
                    ->relationship('vehicle', 'name'),
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
            ->defaultSort('assigned_from', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDriverAssignments::route('/'),
            'create' => Pages\CreateDriverAssignment::route('/create'),
            'edit'   => Pages\EditDriverAssignment::route('/{record}/edit'),
        ];
    }
}