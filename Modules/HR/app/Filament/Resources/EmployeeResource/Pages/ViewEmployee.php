<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\Pages;

use App\Models\Role;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Modules\CommunicationCentre\Filament\Components\NotificationPreferenceDisplay;
use Modules\HR\Filament\Resources\EmployeeResource;
use Modules\HR\Models\Employee;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Employee Details')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        TextEntry::make('employee_code')
                            ->label('Employee ID')
                            ->weight('bold'),

                        TextEntry::make('employment_type')
                            ->label('Offer Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'full_time' => 'success',
                                'part_time' => 'warning',
                                'contract' => 'danger',
                                'intern' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                        TextEntry::make('employment_status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'probation' => 'warning',
                                'suspended', 'terminated', 'resigned' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => ucfirst($state)),

                        ImageEntry::make('employee_photo')
                            ->label('Employee Photo')
                            ->alignCenter()
                            ->disk('public')
                            ->alignCenter()
                            ->extraAttributes([
                                'class' => 'rounded-lg shadow-md border-2 border-gray-200',
                            ])
                            ->columnSpanFull(),
                        TextEntry::make('full_name')
                            ->label('Full Name')
                            ->weight('bold')
                            ->size(TextSize::Large)
                            ->columnSpanFull(),
                        TextEntry::make('gender')
                            ->label('Gender')
                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                        TextEntry::make('dob')
                            ->label('Date of Birth')
                            ->date(),
                        TextEntry::make('marital_status')
                            ->label('Marital Status')
                            ->formatStateUsing(fn ($state) => ucfirst($state)),

                        RepeatableEntry::make('next_of_kin')
                            ->label('Next Of Kin')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Name'),
                                TextEntry::make('relationship')
                                    ->label('Relationship')
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                TextEntry::make('phone_no')
                                    ->label('Phone'),
                            ])
                            ->columns([
                                'default' => 2,
                                'md' => 3,
                                'xl' => 3,
                            ])
                            ->columnSpanFull(),

                        Fieldset::make('Identification')
                            ->schema([
                                TextEntry::make('national_id_type')
                                    ->label('ID Type')
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'national_id' => 'Ghana Card',
                                        'passport' => 'Passport',
                                        'driver_license' => 'Driver License',
                                        default => ucfirst($state),
                                    }),
                                TextEntry::make('national_id_number')
                                    ->label('ID Number'),
                                ImageEntry::make('national_id_photos')
                                    ->label('National ID Photo')
                                    ->disk('public')
                                    ->alignCenter()
                                    ->extraAttributes([
                                        'class' => 'rounded-lg shadow-md border-2 border-gray-200',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columns([
                                'default' => 2,
                                'sm' => 2,
                                'md' => 2,
                                'xl' => 3,
                            ])
                            ->columnSpanFull(),

                    ]),

                Section::make('Contact Information')
                    ->description('Employee\'s Contact Details')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'md' => 2,
                        'xl' => 2,
                    ])
                    ->schema([
                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->columnSpanFull(),
                        TextEntry::make('phone')
                            ->label('Company Phone')
                            ->icon('heroicon-m-phone'),
                        TextEntry::make('alt_phone')
                            ->label('Alternate Phone')
                            ->icon('heroicon-m-phone'),
                        TextEntry::make('whatsapp_no')
                            ->label('WhatsApp')
                            ->icon('heroicon-m-chat-bubble-left'),
                        TextEntry::make('address')
                            ->label('Residential Address')
                            ->columnSpanFull(),
                        TextEntry::make('residential_gps')
                            ->label('Residence GhanaPost GPS'),

                        // Emergency Contacts
                        Fieldset::make('Emergency Contact')
                            ->schema([
                                TextEntry::make('emergency_contact_name')
                                    ->label('Contact Name'),
                                TextEntry::make('emergency_contact_phone')
                                    ->label('Contact Phone'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),

                // Notification Preferences Display Component
                NotificationPreferenceDisplay::make('employee', Employee::class),

                Section::make('Employment Details')
                    ->description('Employee\'s Employment Details')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'md' => 2,
                        'xl' => 2,
                    ])
                    ->schema([
                        TextEntry::make('company.name')
                            ->label('Company'),
                        TextEntry::make('department.name')
                            ->label('Department'),
                        TextEntry::make('jobPosition.title')
                            ->label('Job Position'),
                        TextEntry::make('hire_date')
                            ->label('Hire Date')
                            ->date(),
                        TextEntry::make('manager.full_name')
                            ->label('Reports To')
                            ->placeholder('Not assigned'),
                    ])
                    ->collapsible(),

                Section::make('Bank Account Information')
                    ->description('Employee\'s Bank Account Details')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'md' => 2,
                        'xl' => 2,
                    ])
                    ->schema([
                        TextEntry::make('bank_account_holder_name')
                            ->label('Account Holder'),
                        TextEntry::make('bank_name')
                            ->label('Bank Name'),
                        TextEntry::make('bank_account_no')
                            ->label('Account Number'),
                        TextEntry::make('bank_branch')
                            ->label('Branch'),
                        TextEntry::make('bank_sort_code')
                            ->label('Sort Code'),
                    ])
                    ->collapsible(),

                Section::make('System Access')
                    ->description('Sytem Access For Employee')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'md' => 2,
                        'xl' => 4,
                    ])
                    ->schema([
                        IconEntry::make('user_id')
                            ->label('Has User Account')
                            ->boolean()
                            ->getStateUsing(fn ($record) => ! is_null($record->user_id)),
                        IconEntry::make('system_access_requested')
                            ->label('Access Requested')
                            ->boolean(),
                        TextEntry::make('system_access_approved_at')
                            ->label('Access Approved')
                            ->dateTime()
                            ->placeholder('Not approved'),
                        TextEntry::make('user.name')
                            ->label('Username')
                            ->placeholder('No user account'),
                    ])->columnSpanFull(),

            ]);
    }

    protected function getHeaderActions(): array
    {
        $roleForm = fn (Employee $record): array => [
            Select::make('role_id')
                ->label('Assign Role')
                ->options(function () use ($record): array {
                    $teamKey = config('permission.column_names.team_foreign_key', 'company_id');

                    return Role::where($teamKey, $record->company_id)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->mapWithKeys(fn ($name, $id) => [$id => str($name)->headline()->toString()])
                        ->all();
                })
                ->searchable()
                ->helperText('Select a role for this employee within their company. You can change it later from the Users section.'),
        ];

        return [
            EditAction::make()
                ->label('Edit Employee'),

            Action::make('requestSystemAccess')
                ->label('Request System Access')
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
                ->form(fn (Employee $record): array => $roleForm($record))
                ->action(fn (Employee $record, array $data) => $record->approveSystemAccess(null, $data['role_id'] ?? null))
                ->modalHeading('Approve System Access')
                ->modalDescription('A user account will be created with a random password. Optionally assign a role now — no need to visit the Users section afterwards.')
                ->modalSubmitActionLabel('Approve & Create Account')
                ->visible(fn (Employee $record) => $record->system_access_requested && ! $record->user_id),

            Action::make('createUserAccount')
                ->label('Create Account')
                ->icon('heroicon-o-user-plus')
                ->color('primary')
                ->form(fn (Employee $record): array => $roleForm($record))
                ->action(fn (Employee $record, array $data) => $record->createUserAccount(null, $data['role_id'] ?? null))
                ->modalHeading('Create User Account')
                ->modalDescription('Creates a user account and grants access to the company-admin panel. Optionally assign a role now — no need to visit the Users section afterwards.')
                ->modalSubmitActionLabel('Create Account')
                ->visible(fn (Employee $record) => ! $record->user_id),
        ];
    }
}
