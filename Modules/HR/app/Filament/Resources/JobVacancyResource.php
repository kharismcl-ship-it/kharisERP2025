<?php

namespace Modules\HR\Filament\Resources;

use Modules\HR\Filament\Clusters\HrRecruitmentCluster;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\JobVacancyResource\Pages;
use Modules\HR\Filament\Resources\JobVacancyResource\RelationManagers\ApplicantsRelationManager;
use Modules\HR\Models\JobVacancy;

class JobVacancyResource extends Resource
{
    protected static ?string $cluster = HrRecruitmentCluster::class;
    protected static ?string $model = JobVacancy::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;


    protected static ?int $navigationSort = 57;

    protected static ?string $navigationLabel = 'Job Vacancies';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Vacancy Details')
                    ->description('Define the open position and requirements')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload()->required(),
                        Forms\Components\TextInput::make('title')->required()->maxLength(150),
                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()->preload()->nullable(),
                        Forms\Components\Select::make('job_position_id')
                            ->relationship('jobPosition', 'title')
                            ->searchable()->preload()->nullable(),
                        Forms\Components\Select::make('employment_type')
                            ->options(JobVacancy::EMPLOYMENT_TYPES)
                            ->required()->native(false),
                        Forms\Components\Select::make('status')
                            ->options(JobVacancy::STATUSES)
                            ->required()->native(false),
                        Forms\Components\TextInput::make('vacancies_count')
                            ->label('Number of Vacancies')
                            ->numeric()->default(1)->minValue(1),
                        Forms\Components\Select::make('posted_by_employee_id')
                            ->label('Posted By')
                            ->relationship('postedBy', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->nullable(),
                    ]),

                Section::make('Dates & Salary Range')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('posted_date')->native(false),
                        Forms\Components\DatePicker::make('closing_date')->native(false),
                        Forms\Components\TextInput::make('salary_min')
                            ->label('Min. Salary')->numeric()->prefix('GHS'),
                        Forms\Components\TextInput::make('salary_max')
                            ->label('Max. Salary')->numeric()->prefix('GHS'),
                    ]),

                Section::make('Job Description & Requirements')
                    ->collapsible()
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Job Description')->columnSpanFull(),
                        Forms\Components\RichEditor::make('requirements')
                            ->label('Requirements / Qualifications')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()->weight('bold')
                    ->description(fn (JobVacancy $r) => $r->department?->name . ' — ' . $r->jobPosition?->title),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('employment_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'full_time'  => 'success',
                        'part_time'  => 'warning',
                        'contract'   => 'info',
                        'internship' => 'gray',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => JobVacancy::EMPLOYMENT_TYPES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'   => 'success',
                        'draft'  => 'gray',
                        'closed' => 'warning',
                        'filled' => 'info',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('applicants_count')
                    ->label('Applicants')
                    ->counts('applicants')
                    ->alignCenter()
                    ->badge()->color('primary'),
                Tables\Columns\TextColumn::make('vacancies_count')->label('Openings')->alignCenter(),
                Tables\Columns\TextColumn::make('closing_date')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('status')->options(JobVacancy::STATUSES),
                Tables\Filters\SelectFilter::make('employment_type')->options(JobVacancy::EMPLOYMENT_TYPES),
                Tables\Filters\SelectFilter::make('department')->relationship('department', 'name'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('open')
                        ->label('Open Vacancy')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (JobVacancy $r) => in_array($r->status, ['draft', 'closed']))
                        ->action(function (JobVacancy $record) {
                            $record->update(['status' => 'open', 'posted_date' => now()]);
                            Notification::make()->title('Vacancy opened')->success()->send();
                        }),
                    Action::make('close')
                        ->label('Close Vacancy')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn (JobVacancy $r) => $r->status === 'open')
                        ->action(function (JobVacancy $record) {
                            $record->update(['status' => 'closed']);
                            Notification::make()->title('Vacancy closed')->warning()->send();
                        }),
                    Action::make('markFilled')
                        ->label('Mark as Filled')
                        ->icon('heroicon-o-check-badge')
                        ->color('info')
                        ->requiresConfirmation()
                        ->visible(fn (JobVacancy $r) => $r->status !== 'filled')
                        ->action(function (JobVacancy $record) {
                            $record->update(['status' => 'filled']);
                            Notification::make()->title('Vacancy marked as filled')->success()->send();
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            ApplicantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJobVacancies::route('/'),
            'create' => Pages\CreateJobVacancy::route('/create'),
            'view'   => Pages\ViewJobVacancy::route('/{record}'),
            'edit'   => Pages\EditJobVacancy::route('/{record}/edit'),
        ];
    }
}