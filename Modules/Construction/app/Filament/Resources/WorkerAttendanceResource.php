<?php

namespace Modules\Construction\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Modules\Construction\Filament\Resources\WorkerAttendanceResource\Pages;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\ConstructionWorker;
use Modules\Construction\Models\WorkerAttendance;

class WorkerAttendanceResource extends Resource
{
    protected static ?string $model = WorkerAttendance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Attendance';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attendance Details')->schema([
                Grid::make(2)->schema([
                    Select::make('construction_worker_id')
                        ->label('Worker')
                        ->options(fn () => ConstructionWorker::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(),
                    Select::make('construction_project_id')
                        ->label('Project')
                        ->options(fn () => ConstructionProject::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    DatePicker::make('date')->required()->default(now()),
                    Select::make('attendance_status')
                        ->options(array_combine(
                            WorkerAttendance::ATTENDANCE_STATUSES,
                            array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), WorkerAttendance::ATTENDANCE_STATUSES))
                        ))
                        ->default('present')
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    TimePicker::make('check_in_time')->label('Check In'),
                    TimePicker::make('check_out_time')->label('Check Out'),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('hours_worked')->numeric()->readOnly()->label('Hours Worked'),
                    TextInput::make('per_diem_amount')->numeric()->prefix('GHS')->readOnly()->label('Per Diem'),
                ]),
                Grid::make(2)->schema([
                    Toggle::make('is_approved')->label('Approved'),
                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('worker.name')->label('Worker')->searchable()->sortable(),
                TextColumn::make('project.name')->label('Project')->searchable(),
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('check_in_time')->label('In')->placeholder('—'),
                TextColumn::make('check_out_time')->label('Out')->placeholder('—'),
                TextColumn::make('hours_worked')->label('Hours')->placeholder('—')->sortable(),
                TextColumn::make('per_diem_amount')->label('Per Diem')->money('GHS')->placeholder('—'),
                TextColumn::make('attendance_status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present'  => 'success',
                        'half_day' => 'warning',
                        'excused'  => 'info',
                        'absent'   => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                IconColumn::make('is_approved')->label('Approved')->boolean(),
            ])
            ->filters([
                SelectFilter::make('construction_project_id')
                    ->label('Project')
                    ->relationship('project', 'name'),
                SelectFilter::make('attendance_status')
                    ->label('Status')
                    ->options(array_combine(
                        WorkerAttendance::ATTENDANCE_STATUSES,
                        array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), WorkerAttendance::ATTENDANCE_STATUSES))
                    )),
                TernaryFilter::make('is_approved')->label('Approved'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_approved' => true]))
                        ->requiresConfirmation(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWorkerAttendances::route('/'),
            'create' => Pages\CreateWorkerAttendance::route('/create'),
            'view'   => Pages\ViewWorkerAttendance::route('/{record}'),
            'edit'   => Pages\EditWorkerAttendance::route('/{record}/edit'),
        ];
    }
}
