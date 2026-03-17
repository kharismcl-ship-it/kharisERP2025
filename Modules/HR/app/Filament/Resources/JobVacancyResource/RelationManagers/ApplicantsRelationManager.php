<?php

namespace Modules\HR\Filament\Resources\JobVacancyResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\HR\Events\NewEmployeeOnboarded;
use Modules\HR\Models\Applicant;
use Modules\HR\Models\Employee;

class ApplicantsRelationManager extends RelationManager
{
    protected static string $relationship = 'applicants';

    protected static ?string $title = 'Applicants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('first_name')->required()->maxLength(100),
                Forms\Components\TextInput::make('last_name')->required()->maxLength(100),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('phone')->tel()->nullable(),
                Forms\Components\Select::make('status')
                    ->options(Applicant::STATUSES)
                    ->required()->native(false),
                Forms\Components\Select::make('source')
                    ->options(Applicant::SOURCES)
                    ->required()->native(false),
                Forms\Components\DatePicker::make('applied_date')->native(false),
                Forms\Components\FileUpload::make('resume_path')
                    ->label('Resume / CV')->disk('public')->directory('hr/resumes')->nullable(),
                Forms\Components\FileUpload::make('cover_letter_path')
                    ->label('Cover Letter')->disk('public')->directory('hr/cover-letters')->nullable(),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(fn (Applicant $r) => $r->first_name . ' ' . $r->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->placeholder('—'),
                Tables\Columns\TextColumn::make('source')
                    ->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => Applicant::SOURCES[$state] ?? $state),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'applied'             => 'gray',
                        'shortlisted'         => 'info',
                        'interview_scheduled' => 'warning',
                        'interviewed'         => 'primary',
                        'offered'             => 'success',
                        'hired'               => 'success',
                        'rejected'            => 'danger',
                        'withdrawn'           => 'gray',
                        default               => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => Applicant::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('applied_date')->date(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('shortlist')
                    ->label('Shortlist')
                    ->icon('heroicon-o-star')
                    ->color('info')
                    ->visible(fn (Applicant $r) => $r->status === 'applied')
                    ->action(function (Applicant $record) {
                        $record->update(['status' => 'shortlisted']);
                        Notification::make()->title('Applicant shortlisted')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Applicant $r) => !in_array($r->status, ['hired', 'rejected', 'withdrawn']))
                    ->action(function (Applicant $record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()->title('Applicant rejected')->danger()->send();
                    }),
                Action::make('sendOfferLetter')
                    ->label('Send Offer Letter')
                    ->icon('heroicon-o-envelope')
                    ->color('success')
                    ->visible(fn (Applicant $r) => in_array($r->status, ['shortlisted', 'interviewed']) && $r->email)
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Proposed Start Date')
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('salary')
                            ->label('Offered Salary (per month)')
                            ->numeric()
                            ->required(),
                        Forms\Components\Textarea::make('additional_notes')
                            ->label('Additional Notes')
                            ->rows(3)
                            ->placeholder('Any conditions, benefits, or details to include...'),
                    ])
                    ->action(function (Applicant $record, array $data) {
                        $vacancy = $record->jobVacancy;
                        $position = $vacancy?->jobPosition?->title ?? $vacancy?->title ?? 'the position';
                        $company  = $vacancy?->company?->name ?? 'our organisation';
                        $startDate = \Carbon\Carbon::parse($data['start_date'])->format('F j, Y');
                        $salary    = number_format((float) $data['salary'], 2);

                        $body = "Dear {$record->first_name} {$record->last_name},\n\n"
                            . "We are pleased to offer you the position of **{$position}** at {$company}.\n\n"
                            . "**Proposed Start Date:** {$startDate}\n"
                            . "**Monthly Salary:** GHS {$salary}\n\n"
                            . ($data['additional_notes'] ? "{$data['additional_notes']}\n\n" : '')
                            . "Please confirm your acceptance by replying to this email.\n\n"
                            . "Congratulations and we look forward to welcoming you to the team!\n\n"
                            . "HR Department\n{$company}";

                        try {
                            app(CommunicationService::class)->sendRawEmail(
                                $record->email,
                                $record->first_name . ' ' . $record->last_name,
                                "Offer Letter — {$position} at {$company}",
                                $body,
                            );
                            $record->update(['status' => 'offered']);
                            Notification::make()->title('Offer letter sent to ' . $record->email)->success()->send();
                        } catch (\Throwable $e) {
                            Log::warning('SendOfferLetter failed', ['applicant_id' => $record->id, 'error' => $e->getMessage()]);
                            Notification::make()->title('Failed to send offer letter — check email configuration')->danger()->send();
                        }
                    }),
                Action::make('convertToEmployee')
                    ->label('Convert to Employee')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Convert Applicant to Employee')
                    ->modalDescription('This will create a new Employee record pre-filled with the applicant\'s details. You can edit the full profile afterwards.')
                    ->visible(fn (Applicant $r) => in_array($r->status, ['offered', 'hired']))
                    ->action(function (Applicant $record) {
                        $vacancy = $record->jobVacancy;
                        $companyId = $vacancy?->company_id ?? app('current_company_id');

                        $nameParts = explode(' ', trim($record->first_name . ' ' . $record->last_name), 2);
                        $employee = Employee::create([
                            'company_id'         => $companyId,
                            'first_name'         => $record->first_name,
                            'last_name'          => $record->last_name ?? '',
                            'email'              => $record->email,
                            'phone'              => $record->phone,
                            'department_id'      => $vacancy?->department_id,
                            'job_position_id'    => $vacancy?->job_position_id,
                            'employment_type'    => $vacancy?->employment_type ?? 'full_time',
                            'employment_status'  => 'active',
                            'hire_date'          => now()->toDateString(),
                        ]);

                        $record->update(['status' => 'hired']);

                        event(new NewEmployeeOnboarded($employee));

                        Notification::make()
                            ->title('Employee record created')
                            ->body("Employee #{$employee->employee_code} created from applicant. Complete the profile in HR Records > Employees.")
                            ->success()
                            ->send();
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}