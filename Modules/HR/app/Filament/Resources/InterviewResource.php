<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
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
use Modules\HR\Filament\Clusters\HrRecruitmentCluster;
use Modules\HR\Filament\Resources\InterviewResource\Pages;
use Modules\HR\Models\Interview;

class InterviewResource extends Resource
{
    protected static ?string $cluster = HrRecruitmentCluster::class;
    protected static ?string $model = Interview::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?int $navigationSort = 59;

    protected static ?string $navigationLabel = 'Interviews';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Interview Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('applicant_id')
                            ->label('Applicant')
                            ->relationship('applicant', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('interview_type')
                            ->label('Interview Type')
                            ->options(Interview::TYPES)
                            ->required()->native(false),
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Scheduled At')
                            ->required()->native(false),
                        Forms\Components\TextInput::make('location')
                            ->label('Location / Meeting Link')
                            ->maxLength(255),
                        Forms\Components\Select::make('interviewer_employee_id')
                            ->label('Interviewer')
                            ->relationship('interviewer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->nullable(),
                        Forms\Components\Select::make('status')
                            ->options(Interview::STATUSES)
                            ->required()->default('scheduled')->native(false),
                    ]),

                Section::make('Outcome')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('result')
                            ->options(Interview::RESULTS)
                            ->native(false),
                        Forms\Components\TextInput::make('score')
                            ->numeric()->minValue(0)->maxValue(100)->suffix('/100'),
                        Forms\Components\Textarea::make('feedback')
                            ->rows(4)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('applicant.first_name')
                    ->label('Applicant')
                    ->getStateUsing(fn ($record) => $record->applicant?->first_name . ' ' . $record->applicant?->last_name)
                    ->searchable(['applicant.first_name', 'applicant.last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('interview_type')
                    ->formatStateUsing(fn ($state) => Interview::TYPES[$state] ?? ucfirst($state))
                    ->badge()->color('gray'),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('interviewer.full_name')
                    ->label('Interviewer')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'completed'  => 'success',
                        'cancelled'  => 'danger',
                        'no_show'    => 'danger',
                        'scheduled'  => 'warning',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => Interview::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('result')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'passed'  => 'success',
                        'failed'  => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state ? (Interview::RESULTS[$state] ?? ucfirst($state)) : '—'),
                Tables\Columns\TextColumn::make('score')
                    ->suffix('/100')->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('interview_type')->options(Interview::TYPES),
                Tables\Filters\SelectFilter::make('status')->options(Interview::STATUSES),
                Tables\Filters\SelectFilter::make('result')->options(Interview::RESULTS),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInterviews::route('/'),
            'create' => Pages\CreateInterview::route('/create'),
            'view'   => Pages\ViewInterview::route('/{record}'),
            'edit'   => Pages\EditInterview::route('/{record}/edit'),
        ];
    }
}
