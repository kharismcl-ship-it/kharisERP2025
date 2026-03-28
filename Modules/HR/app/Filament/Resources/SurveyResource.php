<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\Action;
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
use Modules\HR\Filament\Resources\SurveyResource\Pages;
use Modules\HR\Models\Survey;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;
    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';
    protected static ?int $navigationSort = 68;
    protected static ?string $navigationLabel = 'Engagement Surveys';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Survey Details')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('survey_type')
                    ->options([
                        'pulse'       => 'Pulse Survey',
                        'engagement'  => 'Annual Engagement',
                        'lifecycle'   => 'Lifecycle (30/60/90 day)',
                        'exit'        => 'Exit Survey',
                        'onboarding'  => 'Onboarding Feedback',
                    ])
                    ->required()->default('pulse'),
                Forms\Components\Select::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'closed' => 'Closed'])
                    ->required()->default('draft'),
                Forms\Components\Toggle::make('is_anonymous')->default(true)->inline(false),
                Forms\Components\DateTimePicker::make('starts_at'),
                Forms\Components\DateTimePicker::make('ends_at'),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
            ]),
            Section::make('Questions')
                ->schema([
                    Forms\Components\Repeater::make('questions')
                        ->relationship()
                        ->schema([
                            Forms\Components\TextInput::make('question')->required()->columnSpanFull(),
                            Forms\Components\Select::make('question_type')
                                ->options([
                                    'rating'          => 'Rating (1-5)',
                                    'text'            => 'Open Text',
                                    'multiple_choice' => 'Multiple Choice',
                                    'yes_no'          => 'Yes / No',
                                ])
                                ->required()->default('rating')->live(),
                            Forms\Components\Toggle::make('is_required')->default(true)->inline(false),
                            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                            Forms\Components\TagsInput::make('options')
                                ->label('Choices (one per tag)')
                                ->visible(fn ($get) => $get('question_type') === 'multiple_choice')
                                ->helperText('Press Enter after each option'),
                        ])
                        ->columns(2)
                        ->orderColumn('sort_order')
                        ->addActionLabel('Add Question')
                        ->reorderable('sort_order')
                        ->collapsible(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('survey_type')->label('Type')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pulse' => 'Pulse', 'engagement' => 'Engagement',
                        'lifecycle' => 'Lifecycle', 'exit' => 'Exit',
                        'onboarding' => 'Onboarding', default => $state,
                    })->badge()->color('info'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()->color(fn ($state) => match ($state) {
                        'draft' => 'gray', 'active' => 'success', 'closed' => 'danger', default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_anonymous')->boolean()->label('Anon'),
                Tables\Columns\TextColumn::make('responses_count')->label('Responses')
                    ->counts('responses')->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->label('Closes')->dateTime()->placeholder('—')->sortable(),
            ])
            ->actions([ActionGroup::make([ViewAction::make(), EditAction::make()])])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'edit'   => Pages\EditSurvey::route('/{record}/edit'),
            'view'   => Pages\ViewSurvey::route('/{record}'),
        ];
    }
}