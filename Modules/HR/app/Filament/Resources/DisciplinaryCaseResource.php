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
use Modules\HR\Filament\Resources\DisciplinaryCaseResource\Pages;
use Modules\HR\Models\DisciplinaryCase;

class DisciplinaryCaseResource extends Resource
{
    protected static ?string $model = DisciplinaryCase::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|\UnitEnum|null $navigationGroup = 'Employee Relations';

    protected static ?int $navigationSort = 60;

    protected static ?string $navigationLabel = 'Disciplinary Cases';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Case Information')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->relationship('employee', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('type')
                            ->options(DisciplinaryCase::TYPES)
                            ->required()->native(false),
                        Forms\Components\Select::make('status')
                            ->options(DisciplinaryCase::STATUSES)
                            ->required()->native(false),
                        Forms\Components\DatePicker::make('incident_date')->required()->native(false),
                        Forms\Components\Select::make('handled_by_employee_id')
                            ->label('Handled By')
                            ->relationship('handledBy', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->nullable(),
                    ]),

                Section::make('Incident & Action')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Textarea::make('incident_description')
                            ->required()->rows(4)->columnSpanFull(),
                        Forms\Components\Textarea::make('action_taken')
                            ->rows(3)->columnSpanFull(),
                    ]),

                Section::make('Resolution')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('resolution_date')->native(false)->nullable(),
                        Forms\Components\Textarea::make('resolution_notes')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('incident_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->getStateUsing(fn ($r) => $r->employee->first_name . ' ' . $r->employee->last_name)
                    ->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verbal_warning'  => 'gray',
                        'written_warning' => 'warning',
                        'final_warning'   => 'danger',
                        'suspension'      => 'danger',
                        'termination'     => 'danger',
                        default           => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => DisciplinaryCase::TYPES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'         => 'danger',
                        'under_review' => 'warning',
                        'resolved'     => 'success',
                        'appealed'     => 'info',
                        'closed'       => 'gray',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => DisciplinaryCase::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('incident_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('handledBy.full_name')
                    ->label('Handled By')
                    ->getStateUsing(fn ($r) => $r->handledBy ? $r->handledBy->first_name . ' ' . $r->handledBy->last_name : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('type')->options(DisciplinaryCase::TYPES),
                Tables\Filters\SelectFilter::make('status')->options(DisciplinaryCase::STATUSES),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('resolve')
                        ->label('Resolve Case')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (DisciplinaryCase $r) => in_array($r->status, ['open', 'under_review']))
                        ->action(function (DisciplinaryCase $record) {
                            $record->update(['status' => 'resolved', 'resolution_date' => now()]);
                            Notification::make()->title('Case resolved')->success()->send();
                        }),
                    Action::make('close')
                        ->label('Close Case')
                        ->icon('heroicon-o-lock-closed')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->visible(fn (DisciplinaryCase $r) => $r->status === 'resolved')
                        ->action(function (DisciplinaryCase $record) {
                            $record->update(['status' => 'closed']);
                            Notification::make()->title('Case closed')->send();
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDisciplinaryCases::route('/'),
            'create' => Pages\CreateDisciplinaryCase::route('/create'),
            'view'   => Pages\ViewDisciplinaryCase::route('/{record}'),
            'edit'   => Pages\EditDisciplinaryCase::route('/{record}/edit'),
        ];
    }
}