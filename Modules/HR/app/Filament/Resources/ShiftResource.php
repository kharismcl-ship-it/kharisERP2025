<?php

namespace Modules\HR\Filament\Resources;

use Modules\HR\Filament\Clusters\HrSetupCluster;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\ShiftResource\Pages;
use Modules\HR\Filament\Resources\ShiftResource\RelationManagers\ShiftAssignmentsRelationManager;
use Modules\HR\Models\Shift;

class ShiftResource extends Resource
{
    protected static ?string $cluster = HrSetupCluster::class;
    protected static ?string $model = Shift::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;


    protected static ?int $navigationSort = 54;

    protected static ?string $navigationLabel = 'Work Shifts';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Shift Details')
                    ->description('Define working hours and days for this shift')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload(),
                        Forms\Components\TextInput::make('name')
                            ->maxLength(100)
                            ->placeholder('e.g. Morning Shift, Night Shift'),
                        Forms\Components\TimePicker::make('start_time')->seconds(false),
                        Forms\Components\TimePicker::make('end_time')->seconds(false),
                        Forms\Components\TextInput::make('break_duration_minutes')
                            ->label('Break Duration (minutes)')
                            ->numeric()->default(0),
                        Forms\Components\CheckboxList::make('days_of_week')
                            ->label('Working Days')
                            ->options([
                                0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday',
                                3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday',
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Status & Notes')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')->default(true)->inline(false),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->weight('bold')
                    ->description(fn (Shift $record) => $record->day_names),
                Tables\Columns\TextColumn::make('start_time')->label('Start')->sortable(),
                Tables\Columns\TextColumn::make('end_time')->label('End')->sortable(),
                Tables\Columns\TextColumn::make('break_duration_minutes')
                    ->label('Break')
                    ->suffix(' min')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('assignments_count')
                    ->label('Assigned')
                    ->counts('assignments')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            ShiftAssignmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShift::route('/create'),
            'view'   => Pages\ViewShift::route('/{record}'),
            'edit'   => Pages\EditShift::route('/{record}/edit'),
        ];
    }
}