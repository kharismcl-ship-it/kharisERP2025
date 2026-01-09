<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Filament\Resources\HostelStaffAttendanceResource\Pages\CreateHostelStaffAttendance;
use Modules\Hostels\Filament\Resources\HostelStaffAttendanceResource\Pages\EditHostelStaffAttendance;
use Modules\Hostels\Filament\Resources\HostelStaffAttendanceResource\Pages\ListHostelStaffAttendances;
use Modules\Hostels\Models\HostelStaffAttendance;

class HostelStaffAttendanceResource extends Resource
{
    protected static ?string $model = HostelStaffAttendance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|\UnitEnum|null $navigationGroup = 'Staff Management';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attendance Information')
                    ->schema([
                        Select::make('hostel_id')
                            ->relationship('hostel', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('employee_id')
                            ->relationship('employee', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        DateTimePicker::make('attendance_date')
                            ->required()
                            ->default(now()),
                    ])->columns(3),

                Section::make('Time Tracking')
                    ->schema([
                        TimePicker::make('clock_in_time')
                            ->nullable(),
                        TimePicker::make('clock_out_time')
                            ->nullable(),
                        TextInput::make('hours_worked')
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->maxValue(24),
                        Select::make('status')
                            ->options([
                                'present' => 'Present',
                                'absent' => 'Absent',
                                'late' => 'Late',
                                'early_departure' => 'Early Departure',
                                'half_day' => 'Half Day',
                            ])
                            ->default('present')
                            ->required(),
                    ])->columns(2),

                Section::make('Approval')
                    ->schema([
                        Toggle::make('is_approved')
                            ->default(false),
                        Textarea::make('notes')
                            ->maxLength(65535),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee.full_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('attendance_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('clock_in_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('clock_out_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('hours_worked')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'early_departure' => 'warning',
                        'half_day' => 'info',
                    ])
                    ->sortable(),
                IconColumn::make('is_approved')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHostelStaffAttendances::route('/'),
            'create' => CreateHostelStaffAttendance::route('/create'),
            'edit' => EditHostelStaffAttendance::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
