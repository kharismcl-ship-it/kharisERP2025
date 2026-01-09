<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\EmployeeResource\Pages;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\AttendanceRecordsRelationManager;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\CompanyAssignmentsRelationManager;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\EmployeeDocumentsRelationManager;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\EmploymentContractsRelationManager;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\LeaveRequestsRelationManager;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\PerformanceReviewsRelationManager;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\SalariesRelationManager;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\SubordinatesRelationManager;
use Modules\HR\Models\Employee;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('employee_code')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('other_names')
                            ->label('Other Names')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Last Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->label('Gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('dob')
                            ->label('Date of Birth')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('alt_phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('national_id_number')
                            ->label('National ID Number')
                            ->maxLength(255),
                        Forms\Components\Select::make('marital_status')
                            ->label('Marital Status')
                            ->options([
                                'single' => 'Single',
                                'married' => 'Married',
                                'divorced' => 'Divorced',
                                'widowed' => 'Widowed',
                            ]),
                        Forms\Components\Textarea::make('address')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Employment Information')
                    ->schema([
                        Forms\Components\DatePicker::make('hire_date'),
                        Forms\Components\Select::make('employment_type')
                            ->options([
                                'full_time' => 'Full Time',
                                'part_time' => 'Part Time',
                                'contract' => 'Contract',
                                'intern' => 'Intern',
                            ])
                            ->required(),
                        Forms\Components\Select::make('employment_status')
                            ->label('Employment Status')
                            ->options([
                                'active' => 'Active',
                                'probation' => 'Probation',
                                'suspended' => 'Suspended',
                                'terminated' => 'Terminated',
                                'resigned' => 'Resigned',
                            ])
                            ->required(),
                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('job_position_id')
                            ->relationship('jobPosition', 'title')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('reporting_to_employee_id')
                            ->label('Reporting To')
                            ->relationship('manager', 'full_name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                Section::make('Emergency Contact')
                    ->schema([
                        Forms\Components\TextInput::make('emergency_contact_name')
                            ->label('Emergency Contact Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('emergency_contact_phone')
                            ->label('Emergency Contact Phone')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('employee_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employment_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employment_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jobPosition.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('department')
                    ->relationship('department', 'name'),
                Tables\Filters\SelectFilter::make('employment_status')
                    ->label('Employment Status')
                    ->options([
                        'active' => 'Active',
                        'probation' => 'Probation',
                        'suspended' => 'Suspended',
                        'terminated' => 'Terminated',
                        'resigned' => 'Resigned',
                    ]),
                Tables\Filters\SelectFilter::make('employment_type')
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'intern' => 'Intern',
                    ]),
            ])
            ->actions([
                EditAction::make(),
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
            AttendanceRecordsRelationManager::class,
            LeaveRequestsRelationManager::class,
            EmploymentContractsRelationManager::class,
            EmployeeDocumentsRelationManager::class,
            PerformanceReviewsRelationManager::class,
            SalariesRelationManager::class,
            SubordinatesRelationManager::class,
            CompanyAssignmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
