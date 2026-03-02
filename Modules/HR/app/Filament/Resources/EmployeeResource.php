<?php

namespace Modules\HR\Filament\Resources;

use App\Models\Company;
use App\Models\Scopes\TenantScope;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\CommunicationCentre\Filament\Components\NotificationPreferenceForm;
use Modules\HR\Models\Department;
use Modules\HR\Models\JobPosition;
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
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Core HR';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Add Employye\'s Basic Information Details')
                    ->collapsible()
                    ->schema([

                        Forms\Components\TextInput::make('employee_code')
                            ->label('Employee Code')
                            ->readOnly()
                            ->columnSpanFull(),
                        FileUpload::make('employee_photo')
                            ->label('Employee Photo')
                            ->image()
                            ->disk('public')
                            ->directory('employee-photo')
                            ->maxSize(1024 * 10)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('other_names')
                            ->label('Middle Name')
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

                        Select::make('national_id_type')
                            ->label('National ID Type')
                            ->options([
                                'national_id' => 'Ghana Card ID',
                                'passport' => 'Passport',
                                'driver_license' => 'Driver License',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\TextInput::make('national_id_number')
                            ->label('National ID No.')
                            ->visible(fn (callable $get) => filled($get('national_id_type')))
                            ->maxLength(255)
                            ->columnSpanFull(),
                        FileUpload::make('national_id_photos')
                            ->label('Upload National ID Front/Back')
                            ->image()
                            ->disk('public')
                            ->directory('employee-national-ids')
                            ->multiple()
                            ->maxSize(1024 * 20)
                            ->visible(fn (callable $get) => filled($get('national_id_type')))
                            ->columnSpanFull(),

                        Forms\Components\Select::make('marital_status')
                            ->label('Marital Status')
                            ->options([
                                'single' => 'Single',
                                'married' => 'Married',
                                'divorced' => 'Divorced',
                                'widowed' => 'Widowed',
                            ])
                            ->required()
                            ->reactive()
                            ->columnSpanFull(),

                        Repeater::make('next_of_kin')
                            ->label('Next of Kin')
                            ->collapsible()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->columnSpanFull(),
                                Select::make('relationship')
                                    ->options([
                                        'spouse' => 'Spouse',
                                        'child' => 'Child',
                                        'guardian' => 'Guardian',
                                        'family' => 'Family',

                                    ]),
                                PhoneInput::make('phone_no')
                                    ->label('Phone')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->visible(fn (callable $get) => filled($get('marital_status')))
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => $state['name'].' ('.$state['relationship'].')') ?? null,
                    ])
                    ->columns(2),

                Section::make('Contact Information')
                    ->description('Add Employee\'s Contact Details')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Residential Address')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('residential_gps')
                            ->label('GhanaPost GPS (Residence)'),
                        PhoneInput::make('phone')
                            ->label('Phone')
                            // ->displayNumberFormat(PhoneInputNumberType::E164)
                            ->inputNumberFormat(PhoneInputNumberType::E164)
                            ->dehydrateStateUsing(function ($state) {
                                if (! is_string($state)) {
                                    return $state;
                                }

                                return ltrim($state, '+');
                            })
                            ->required(),
                        PhoneInput::make('alt_phone')
                            ->label('Alternate Phone No.')
                            ->inputNumberFormat(PhoneInputNumberType::E164)
                            ->dehydrateStateUsing(function ($state) {
                                if (! is_string($state)) {
                                    return $state;
                                }

                                return ltrim($state, '+');
                            }),
                        PhoneInput::make('whatsapp_no')
                            ->label('WhatsApp No.')
                            ->inputNumberFormat(PhoneInputNumberType::E164)
                            ->dehydrateStateUsing(function ($state) {
                                if (! is_string($state)) {
                                    return $state;
                                }

                                return ltrim($state, '+');
                            }),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->prefixIcon('heroicon-o-envelope')
                            ->columnSpanFull(),

                        Fieldset::make('emergency_contact')
                            ->label('Emergency Contact')
                            ->columns('2')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                               ->label('Full Name'),
                                PhoneInput::make('emerg_phone_no')
                               ->label('Phone')
                               ->inputNumberFormat(PhoneInputNumberType::E164)
                               ->dehydrateStateUsing(function ($state) {
                                   if (! is_string($state)) {
                                       return $state;
                                   }

                                   return ltrim($state, '+');
                               }),
                            ])->columnSpanFull(),

                    ])
                    ->columns(2),

                // Notification Preferences
                NotificationPreferenceForm::make('employee'),

                // Account Section
                Section::make('Bank Account Information')
                    ->description('Add Employee\'s Bank Details')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('bank_account_holder_name')
                            ->label('Bank Account Holder Name')
                            ->placeholder('Eg. Bridget Agyemang')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Bank Name')
                            ->placeholder('Eg. Fidelity Bank')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('bank_account_no')
                            ->label('Bank Account No.')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('bank_branch')
                            ->label('Bank Branch')
                            ->required(),
                        Forms\Components\TextInput::make('bank_sort_code')
                            ->label('Bank Sort Code')
                            ->required(),

                    ])
                    ->columns(2),

                Section::make('Employment Information')
                    ->description('Add Employee\'s Employment Details')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->options(function (): array {
                                $tenant = Filament::getTenant();
                                if (! $tenant) {
                                    return Company::withoutGlobalScope(TenantScope::class)
                                        ->orderBy('name')->pluck('name', 'id')->all();
                                }

                                return Company::withoutGlobalScope(TenantScope::class)
                                    ->whereIn('id', $tenant->selfAndDescendantIds())
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            })
                            ->default(fn (): ?int => Filament::getTenant()?->getKey())
                            ->required()
                            ->searchable()
                            ->live(),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('hire_date'),

                        Forms\Components\Select::make('employment_type')
                            ->options([
                                'full_time' => 'Full Time',
                                'part_time' => 'Part Time',
                                'contract'  => 'Contract',
                                'intern'    => 'Intern',
                            ])
                            ->required(),

                        Forms\Components\Select::make('employment_status')
                            ->label('Employment Status')
                            ->options([
                                'active'     => 'Active',
                                'probation'  => 'Probation',
                                'suspended'  => 'Suspended',
                                'terminated' => 'Terminated',
                                'resigned'   => 'Resigned',
                            ])
                            ->required(),

                        Forms\Components\Select::make('department_id')
                            ->label('Department')
                            ->options(function (Get $get): array {
                                $companyId = $get('company_id');
                                if (! $companyId) {
                                    return [];
                                }

                                return Department::withoutGlobalScope(TenantScope::class)
                                    ->where('company_id', $companyId)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            })
                            ->searchable()
                            ->live(),

                        Forms\Components\Select::make('job_position_id')
                            ->label('Job Position')
                            ->options(function (Get $get): array {
                                $companyId = $get('company_id');
                                if (! $companyId) {
                                    return [];
                                }

                                return JobPosition::withoutGlobalScope(TenantScope::class)
                                    ->where('company_id', $companyId)
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                                    ->all();
                            })
                            ->searchable()
                            ->live(),

                        Forms\Components\Select::make('reporting_to_employee_id')
                            ->label('Reporting To')
                            ->options(function (Get $get): array {
                                $companyId = $get('company_id');
                                if (! $companyId) {
                                    return [];
                                }

                                return Employee::withoutGlobalScope(TenantScope::class)
                                    ->where('company_id', $companyId)
                                    ->orderBy('first_name')
                                    ->get()
                                    ->pluck('full_name', 'id')
                                    ->all();
                            })
                            ->searchable()
                            ->live(),
                    ])
                    ->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('employee_code')
                    ->label('Employee No.')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->weight('bold')
                    ->color(fn ($record): string => match ($record->employment_status) {
                        'active' => 'success',
                        'probation' => 'warning',
                        'suspended' => 'danger',
                        'terminated' => 'info',
                        'resigned' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\ImageColumn::make('employee_photo')
                    ->label('Photo')
                    ->visibility('public')
                    ->defaultImageUrl(url('storage/employee-photo/placeholder-kharis.png'))
                    ->circular(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name'])
                    ->weight('bold')
                    ->color(fn ($record): string => match ($record->employment_status) {
                        'active' => 'success',
                        'probation' => 'warning',
                        'suspended' => 'danger',
                        'terminated' => 'info',
                        'resigned' => 'gray',
                        default => 'gray',
                    })
                    ->description(fn ($record) => $record->employee_code),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color(fn ($record): string => match ($record->employment_status) {
                        'active' => 'success',
                        'probation' => 'warning',
                        'suspended' => 'danger',
                        'terminated' => 'info',
                        'resigned' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->label('Hire Date')
                    ->date()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('jobPosition.title')
                    ->label('Job Position')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->department->name),

                Tables\Columns\TextColumn::make('employment_type')
                    ->label('Offer Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'full_time' => 'success',
                        'part_time' => 'warning',
                        'contract' => 'danger',
                        'intern' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('employment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'probation' => 'warning',
                        'suspended', 'terminated', 'resigned' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('system_access_requested')
                    ->label('Access Requested')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\IconColumn::make('user_id')
                    ->label('Has Account')
                    ->boolean()
                    ->getStateUsing(fn ($record) => ! is_null($record->user_id))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make(),
                    Action::make('requestSystemAccess')
                        ->label('Request Access')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->action(fn (Employee $record) => $record->requestSystemAccess())
                        ->requiresConfirmation()
                        ->modalHeading('Request System Access')
                        ->modalDescription('Are you sure you want to request system access for this employee? They will need admin approval.')
                        ->visible(fn (Employee $record) => ! $record->user_id && ! $record->system_access_requested),
                    Action::make('approveSystemAccess')
                        ->label('Approve Access')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Employee $record) => $record->approveSystemAccess())
                        ->requiresConfirmation()
                        ->modalHeading('Approve System Access')
                        ->modalDescription('Are you sure you want to approve system access for this employee? A user account will be created with a random password.')
                        ->visible(fn (Employee $record) => $record->system_access_requested && ! $record->user_id),
                    Action::make('denySystemAccess')
                        ->label('Deny Access')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (Employee $record) => $record->denySystemAccess())
                        ->requiresConfirmation()
                        ->modalHeading('Deny System Access')
                        ->modalDescription('Are you sure you want to deny system access for this employee?')
                        ->visible(fn (Employee $record) => $record->system_access_requested && ! $record->user_id),
                    Action::make('createUserAccount')
                        ->label('Create Account')
                        ->icon('heroicon-o-user-plus')
                        ->color('primary')
                        ->action(fn (Employee $record) => $record->createUserAccount())
                        ->requiresConfirmation()
                        ->modalHeading('Create User Account')
                        ->modalDescription('Are you sure you want to create a user account for this employee? This bypasses the approval workflow.')
                        ->visible(fn (Employee $record) => ! $record->user_id),
                ]),
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
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
