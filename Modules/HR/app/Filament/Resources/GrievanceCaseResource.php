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
use Modules\HR\Filament\Resources\GrievanceCaseResource\Pages;
use Modules\HR\Models\GrievanceCase;

class GrievanceCaseResource extends Resource
{
    protected static ?string $model = GrievanceCase::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|\UnitEnum|null $navigationGroup = 'Employee Relations';

    protected static ?int $navigationSort = 61;

    protected static ?string $navigationLabel = 'Grievance Cases';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Grievance Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee Filing Grievance')
                            ->relationship('employee', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\TextInput::make('grievance_type')
                            ->required()->maxLength(100)
                            ->placeholder('e.g. Harassment, Unfair Treatment, Pay Dispute'),
                        Forms\Components\Select::make('status')
                            ->options(GrievanceCase::STATUSES)
                            ->required()->native(false),
                        Forms\Components\DatePicker::make('filed_date')->required()->native(false),
                        Forms\Components\Toggle::make('is_anonymous')
                            ->label('Anonymous Grievance')->inline(false),
                        Forms\Components\Select::make('assigned_to_employee_id')
                            ->label('Assigned Investigator')
                            ->relationship('assignedTo', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->nullable(),
                    ]),

                Section::make('Description & Resolution')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Textarea::make('description')->required()->rows(4)->columnSpanFull(),
                        Forms\Components\DatePicker::make('resolution_date')->native(false)->nullable(),
                        Forms\Components\Textarea::make('resolution')->rows(3)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('filed_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Filed By')
                    ->getStateUsing(fn ($r) => $r->is_anonymous ? 'Anonymous' : ($r->employee->first_name . ' ' . $r->employee->last_name))
                    ->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('grievance_type')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'filed'               => 'gray',
                        'under_investigation' => 'warning',
                        'hearing_scheduled'   => 'info',
                        'resolved'            => 'success',
                        'closed'              => 'gray',
                        'escalated'           => 'danger',
                        default               => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => GrievanceCase::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('filed_date')->date()->sortable(),
                Tables\Columns\IconColumn::make('is_anonymous')->label('Anon.')->boolean(),
                Tables\Columns\TextColumn::make('assignedTo.full_name')
                    ->label('Investigator')
                    ->getStateUsing(fn ($r) => $r->assignedTo ? $r->assignedTo->first_name . ' ' . $r->assignedTo->last_name : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('status')->options(GrievanceCase::STATUSES),
                Tables\Filters\TernaryFilter::make('is_anonymous')->label('Anonymous'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('investigate')
                        ->label('Start Investigation')
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('warning')
                        ->visible(fn (GrievanceCase $r) => $r->status === 'filed')
                        ->action(function (GrievanceCase $record) {
                            $record->update(['status' => 'under_investigation']);
                            Notification::make()->title('Investigation started')->warning()->send();
                        }),
                    Action::make('resolve')
                        ->label('Resolve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (GrievanceCase $r) => in_array($r->status, ['under_investigation', 'hearing_scheduled']))
                        ->action(function (GrievanceCase $record) {
                            $record->update(['status' => 'resolved', 'resolution_date' => now()]);
                            Notification::make()->title('Grievance resolved')->success()->send();
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGrievanceCases::route('/'),
            'create' => Pages\CreateGrievanceCase::route('/create'),
            'edit'   => Pages\EditGrievanceCase::route('/{record}/edit'),
        ];
    }
}
