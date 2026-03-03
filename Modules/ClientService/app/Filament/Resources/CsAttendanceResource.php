<?php

namespace Modules\ClientService\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\ClientService\Filament\Resources\CsAttendanceResource\Pages;
use Modules\ClientService\Models\CsAttendance;

class CsAttendanceResource extends Resource
{
    protected static ?string $model = CsAttendance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Client Services';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Attendance';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Employee')->schema([
                Grid::make(3)->schema([
                    Select::make('company_id')
                        ->label('Company')
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('employee_id')
                        ->label('Employee')
                        ->relationship('employee', 'full_name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('department_id')
                        ->label('Department')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
                DatePicker::make('attendance_date')->required()->default(today()),
            ]),

            Section::make('Times & Status')->schema([
                Grid::make(2)->schema([
                    TimePicker::make('check_in_time')->label('Check In'),
                    TimePicker::make('check_out_time')->label('Check Out'),
                ]),
                Grid::make(3)->schema([
                    Select::make('status')
                        ->options(CsAttendance::STATUSES)
                        ->default('present')
                        ->required(),
                    TextInput::make('late_minutes')->numeric()->label('Late (min)')->nullable(),
                    TextInput::make('overtime_minutes')->numeric()->label('Overtime (min)')->nullable(),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')->label('Employee')->searchable()->sortable(),
                TextColumn::make('department.name')->label('Department'),
                TextColumn::make('attendance_date')->date()->sortable(),
                TextColumn::make('check_in_time')->label('In'),
                TextColumn::make('check_out_time')->label('Out'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'present'  => 'success',
                        'absent'   => 'danger',
                        'late'     => 'warning',
                        'half_day' => 'info',
                        'leave'    => 'gray',
                        'holiday'  => 'purple',
                        default    => 'gray',
                    }),
                TextColumn::make('late_minutes')->label('Late (min)')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('overtime_minutes')->label('OT (min)')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->filters([
                SelectFilter::make('status')->options(CsAttendance::STATUSES),
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name'),
                Filter::make('today')
                    ->label('Today Only')
                    ->query(fn ($query) => $query->whereDate('attendance_date', today())),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCsAttendances::route('/'),
            'create' => Pages\CreateCsAttendance::route('/create'),
            'edit'   => Pages\EditCsAttendance::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee.full_name'];
    }
}
