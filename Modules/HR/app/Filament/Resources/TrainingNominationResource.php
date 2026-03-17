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
use Modules\HR\Filament\Clusters\HrLearningCluster;
use Modules\HR\Filament\Resources\TrainingNominationResource\Pages;
use Modules\HR\Models\TrainingNomination;

class TrainingNominationResource extends Resource
{
    protected static ?string $cluster = HrLearningCluster::class;
    protected static ?string $model = TrainingNomination::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?int $navigationSort = 72;

    protected static ?string $navigationLabel = 'Training Nominations';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Nomination Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('training_program_id')
                            ->label('Training Program')
                            ->relationship('trainingProgram', 'title')
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->relationship('employee', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('status')
                            ->options(TrainingNomination::STATUSES)
                            ->required()->default('nominated')->native(false),
                        Forms\Components\DatePicker::make('completion_date')
                            ->label('Completion Date')->native(false),
                        Forms\Components\TextInput::make('score')
                            ->numeric()->minValue(0)->maxValue(100)->suffix('/100'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('trainingProgram.title')
                    ->label('Program')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'completed'  => 'success',
                        'attended'   => 'info',
                        'confirmed'  => 'primary',
                        'nominated'  => 'warning',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => TrainingNomination::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('completion_date')
                    ->date()->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->suffix('/100')->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TrainingNomination::STATUSES),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTrainingNominations::route('/'),
            'create' => Pages\CreateTrainingNomination::route('/create'),
            'edit'   => Pages\EditTrainingNomination::route('/{record}/edit'),
        ];
    }
}
