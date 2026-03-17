<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Filament\Clusters\HrRelationsCluster;
use Modules\HR\Models\Employee;
use Modules\HR\Models\ProbationReview;

class ProbationReviewResource extends Resource
{
    protected static ?string $model = ProbationReview::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Probation Reviews';

    protected static ?string $cluster = HrRelationsCluster::class;

    protected static ?int $navigationSort = 25;

    protected static ?string $slug = 'probation-reviews';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', Filament::getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        $companyId = Filament::getTenant()?->id;

        return $schema->components([
            Section::make('Probation Details')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->options(Employee::where('company_id', $companyId)
                                ->where('employment_status', 'probation')
                                ->get()
                                ->pluck('full_name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('reviewer_employee_id')
                            ->label('Reviewer (Manager)')
                            ->options(Employee::where('company_id', $companyId)
                                ->whereIn('employment_status', ['active', 'probation'])
                                ->get()
                                ->pluck('full_name', 'id'))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\DatePicker::make('probation_start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('probation_end_date')
                            ->required(),
                        Forms\Components\TextInput::make('probation_months')
                            ->label('Probation Duration (months)')
                            ->numeric()
                            ->default(3)
                            ->required(),
                        Forms\Components\DatePicker::make('review_date')
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->options(ProbationReview::STATUSES)
                            ->default('pending')
                            ->required(),
                        Forms\Components\Select::make('reviewer_recommendation')
                            ->label('Recommendation')
                            ->options(ProbationReview::RECOMMENDATIONS)
                            ->nullable(),
                    ]),
                ]),

            Section::make('Review Assessment')
                ->schema([
                    Forms\Components\Textarea::make('performance_summary')
                        ->label('Performance Summary')
                        ->rows(3)
                        ->nullable(),
                    Forms\Components\Textarea::make('strengths')
                        ->rows(2)
                        ->nullable(),
                    Forms\Components\Textarea::make('areas_for_improvement')
                        ->rows(2)
                        ->nullable(),
                    Forms\Components\Select::make('overall_rating')
                        ->label('Overall Rating (1–5)')
                        ->options([1 => '1 – Poor', 2 => '2 – Below Average', 3 => '3 – Meets Expectations', 4 => '4 – Exceeds Expectations', 5 => '5 – Outstanding'])
                        ->nullable(),
                ]),

            Section::make('Extension / Decision')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('extension_months')
                        ->label('Extension (months)')
                        ->numeric()
                        ->nullable(),
                    Forms\Components\DatePicker::make('extended_end_date')
                        ->nullable(),
                    Forms\Components\Textarea::make('hr_decision_notes')
                        ->label('HR Decision Notes')
                        ->rows(2)
                        ->columnSpanFull()
                        ->nullable(),
                    Forms\Components\Toggle::make('employee_notified')
                        ->label('Employee Notified')
                        ->default(false),
                    Forms\Components\DateTimePicker::make('notified_at')
                        ->nullable(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Probation Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('employee.full_name')->label('Employee'),
                    TextEntry::make('reviewer.full_name')->label('Reviewer')->placeholder('—'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'passed'    => 'success',
                            'extended'  => 'warning',
                            'failed'    => 'danger',
                            'in_review' => 'info',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ProbationReview::STATUSES[$state] ?? ucfirst($state)),
                    TextEntry::make('probation_start_date')->date(),
                    TextEntry::make('probation_end_date')->date(),
                    TextEntry::make('overall_rating')->label('Rating')->suffix('/5')->placeholder('—'),
                ]),
            Section::make('Assessment')
                ->schema([
                    TextEntry::make('performance_summary')->placeholder('—'),
                    TextEntry::make('strengths')->placeholder('—'),
                    TextEntry::make('areas_for_improvement')->placeholder('—'),
                    TextEntry::make('reviewer_recommendation')
                        ->label('Recommendation')
                        ->formatStateUsing(fn ($state) => ProbationReview::RECOMMENDATIONS[$state] ?? ucfirst((string)$state))
                        ->placeholder('—'),
                    TextEntry::make('hr_decision_notes')->placeholder('—'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('probation_start_date')
                    ->label('Start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('probation_end_date')
                    ->label('End')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewer_recommendation')
                    ->label('Recommendation')
                    ->formatStateUsing(fn ($state) => ProbationReview::RECOMMENDATIONS[$state] ?? ucfirst((string)$state))
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('overall_rating')
                    ->label('Rating')
                    ->suffix('/5')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'passed'    => 'success',
                        'extended'  => 'warning',
                        'failed'    => 'danger',
                        'in_review' => 'info',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ProbationReview::STATUSES[$state] ?? ucfirst($state)),
            ])
            ->defaultSort('probation_end_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ProbationReview::STATUSES),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\HR\Filament\Resources\ProbationReviewResource\Pages\ListProbationReviews::route('/'),
            'create' => \Modules\HR\Filament\Resources\ProbationReviewResource\Pages\CreateProbationReview::route('/create'),
            'edit'   => \Modules\HR\Filament\Resources\ProbationReviewResource\Pages\EditProbationReview::route('/{record}/edit'),
            'view'   => \Modules\HR\Filament\Resources\ProbationReviewResource\Pages\ViewProbationReview::route('/{record}'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id']  = Filament::getTenant()?->id;
        $data['created_by']  = auth()->id();
        return $data;
    }
}
