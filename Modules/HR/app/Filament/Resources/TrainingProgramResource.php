<?php

namespace Modules\HR\Filament\Resources;

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
use Modules\HR\Filament\Resources\TrainingProgramResource\Pages;
use Modules\HR\Filament\Resources\TrainingProgramResource\RelationManagers\NominationsRelationManager;
use Modules\HR\Models\TrainingProgram;

class TrainingProgramResource extends Resource
{
    protected static ?string $model = TrainingProgram::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|\UnitEnum|null $navigationGroup = 'Learning & Development';

    protected static ?int $navigationSort = 58;

    protected static ?string $navigationLabel = 'Training Programs';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Program Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload()->required(),
                        Forms\Components\TextInput::make('title')->required()->maxLength(200),
                        Forms\Components\Select::make('type')
                            ->options(TrainingProgram::TYPES)
                            ->required()->native(false),
                        Forms\Components\Select::make('status')
                            ->options(TrainingProgram::STATUSES)
                            ->required()->native(false),
                        Forms\Components\TextInput::make('provider')
                            ->label('Training Provider')->maxLength(150)->nullable(),
                        Forms\Components\TextInput::make('max_participants')
                            ->label('Max Participants')->numeric()->nullable(),
                        Forms\Components\DatePicker::make('start_date')->native(false),
                        Forms\Components\DatePicker::make('end_date')->native(false),
                        Forms\Components\TextInput::make('cost')
                            ->numeric()->prefix('GHS')->nullable(),
                        Forms\Components\FileUpload::make('certificate_template_path')
                            ->label('Certificate Template')->disk('public')
                            ->directory('hr/training-templates')->nullable(),
                    ]),

                Section::make('Description')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('start_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()->weight('bold')
                    ->description(fn (TrainingProgram $r) => $r->provider),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'internal'   => 'primary',
                        'external'   => 'info',
                        'online'     => 'success',
                        'conference' => 'warning',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planned'   => 'gray',
                        'ongoing'   => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('nominations_count')
                    ->label('Nominated')
                    ->counts('nominations')
                    ->alignCenter()->badge()->color('primary'),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('cost')->money('GHS')->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('type')->options(TrainingProgram::TYPES),
                Tables\Filters\SelectFilter::make('status')->options(TrainingProgram::STATUSES),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('startProgram')
                        ->label('Start Program')
                        ->icon('heroicon-o-play')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn (TrainingProgram $r) => $r->status === 'planned')
                        ->action(function (TrainingProgram $record) {
                            $record->update(['status' => 'ongoing']);
                            Notification::make()->title('Training program started')->warning()->send();
                        }),
                    Action::make('complete')
                        ->label('Mark Complete')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (TrainingProgram $r) => $r->status === 'ongoing')
                        ->action(function (TrainingProgram $record) {
                            $record->update(['status' => 'completed']);
                            Notification::make()->title('Training program completed')->success()->send();
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            NominationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTrainingPrograms::route('/'),
            'create' => Pages\CreateTrainingProgram::route('/create'),
            'view'   => Pages\ViewTrainingProgram::route('/{record}'),
            'edit'   => Pages\EditTrainingProgram::route('/{record}/edit'),
        ];
    }
}