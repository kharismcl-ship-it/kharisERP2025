<?php

namespace Modules\Hostels\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Filament\Resources\HostelPayrollResource\Pages\ManageHostelPayroll;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelPayroll;

class HostelPayrollResource extends Resource
{
    protected static ?string $model = HostelPayroll::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $navigationLabel = 'Payroll Sync';

    protected static ?string $modelLabel = 'Payroll Sync';

    protected static ?string $pluralModelLabel = 'Payroll Sync';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Payroll Period')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->default(now()->subMonth()->startOfMonth()),

                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->default(now()->subMonth()->endOfMonth()),

                        Select::make('hostel_id')
                            ->label('Specific Hostel')
                            ->options(Hostel::all()->pluck('name', 'id'))
                            ->nullable()
                            ->searchable(),
                    ])->columns(3),

                Section::make('Sync Options')
                    ->schema([
                        Toggle::make('sync_attendance')
                            ->label('Sync Attendance to HR')
                            ->default(true)
                            ->helperText('Sync approved attendance records to HR system'),

                        Toggle::make('calculate_payroll')
                            ->label('Calculate Payroll')
                            ->default(true)
                            ->helperText('Calculate payroll based on attendance and role rates'),

                        Toggle::make('export_to_hr')
                            ->label('Export to HR System')
                            ->default(false)
                            ->helperText('Export calculated payroll data to HR employee salaries'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        // This resource doesn't use a table since it's for processing only
        return $table;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageHostelPayroll::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Return empty query since this resource doesn't use a model
        return \Illuminate\Database\Query\Builder::query();
    }
}
