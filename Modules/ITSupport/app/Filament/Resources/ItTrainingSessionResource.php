<?php

namespace Modules\ITSupport\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\ITSupport\Filament\Resources\ItTrainingSessionResource\Pages;
use Modules\ITSupport\Filament\Resources\ItTrainingSessionResource\RelationManagers;
use Modules\ITSupport\Models\ItTrainingSession;

class ItTrainingSessionResource extends Resource
{
    protected static ?string $model = ItTrainingSession::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'IT Support';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Training Sessions';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Session Details')->schema([
                Grid::make(2)->schema([
                    Select::make('company_id')
                        ->label('Company')
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('title')->required()->maxLength(255),
                ]),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                Grid::make(3)->schema([
                    Select::make('session_type')
                        ->options(ItTrainingSession::SESSION_TYPES)
                        ->required(),
                    Select::make('trainer_employee_id')
                        ->label('Trainer')
                        ->relationship('trainerEmployee', 'full_name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Select::make('department_id')
                        ->label('Department')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
            ]),

            Section::make('Schedule')->schema([
                Grid::make(2)->schema([
                    DateTimePicker::make('scheduled_at')->required(),
                    TextInput::make('duration_minutes')->label('Duration (min)')->numeric()->nullable(),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('location')->nullable(),
                    TextInput::make('max_attendees')->label('Max Attendees')->numeric()->nullable(),
                    Select::make('status')
                        ->options(ItTrainingSession::STATUSES)
                        ->default('planned')
                        ->required(),
                ]),
            ]),

            Section::make('Materials & Notes')->schema([
                FileUpload::make('materials_path')
                    ->label('Training Materials')
                    ->directory('it-training-materials')
                    ->nullable(),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->limit(40),
                TextColumn::make('session_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ItTrainingSession::SESSION_TYPES[$state] ?? $state),
                TextColumn::make('trainerEmployee.full_name')->label('Trainer'),
                TextColumn::make('scheduled_at')->dateTime()->sortable(),
                TextColumn::make('duration_minutes')->label('Duration (min)'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'planned'   => 'info',
                        'ongoing'   => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('attendees_count')
                    ->counts('attendees')
                    ->label('Attendees'),
            ])
            ->defaultSort('scheduled_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(ItTrainingSession::STATUSES),
                SelectFilter::make('session_type')->options(ItTrainingSession::SESSION_TYPES),
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItTrainingAttendeesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListItTrainingSessions::route('/'),
            'create' => Pages\CreateItTrainingSession::route('/create'),
            'view'   => Pages\ViewItTrainingSession::route('/{record}'),
            'edit'   => Pages\EditItTrainingSession::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
