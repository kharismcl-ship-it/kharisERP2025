<?php

namespace Modules\Fleet\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
            Section::make('Vehicle & Driver')
                ->description('Select the vehicle and link to an HR employee or system user')
                ->columns(2)
                ->schema([
                    Select::make('vehicle_id')
                        ->label('Vehicle')
                        ->relationship('vehicle', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('employee_id')
                        ->label('Driver (HR Employee)')
                        ->relationship('employee', 'full_name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->helperText('Link to the HR employee record for leave and document checks'),

                    Select::make('user_id')
                        ->label('System User Account')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->helperText('Required for system login access'),

                    Toggle::make('is_primary')
                        ->label('Primary Driver')
                        ->default(true)
                        ->inline(false)
                        ->helperText('Mark as the vehicle\'s primary assigned driver'),
                ]),

            Section::make('Assignment Period')
                ->description('Set the duration of this assignment')
                ->columns(2)
                ->schema([
                    DatePicker::make('assigned_from')
                        ->label('Assigned From')
                        ->required()
                        ->displayFormat('d M Y'),
                    DatePicker::make('assigned_until')
                        ->label('Assigned Until')
                        ->nullable()
                        ->displayFormat('d M Y')
                        ->helperText('Leave blank if the assignment is ongoing'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('notes')->rows(3)->columnSpanFull()->placeholder('Any additional remarks...'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.name')->label('Vehicle')->searchable()->sortable(),
                TextColumn::make('vehicle.plate')->label('Plate'),
                TextColumn::make('employee.full_name')->label('HR Employee')->searchable()->placeholder('—'),
                TextColumn::make('user.name')->label('System User')->placeholder('—'),
                TextColumn::make('assigned_from')->date()->sortable(),
                TextColumn::make('assigned_until')->date()->label('Until')->placeholder('Ongoing'),
                IconColumn::make('is_primary')->label('Primary')->boolean(),
            ])
            ->filters([
                SelectFilter::make('vehicle_id')
                    ->label('Vehicle')
                    ->relationship('vehicle', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('assigned_from', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDriverAssignments::route('/'),
            'create' => Pages\CreateDriverAssignment::route('/create'),
            'view'   => Pages\ViewDriverAssignment::route('/{record}'),
            'edit'   => Pages\EditDriverAssignment::route('/{record}/edit'),
        ];
    }
}
