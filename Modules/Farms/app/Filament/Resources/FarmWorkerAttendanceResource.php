<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Clusters\FarmOperationsCluster;
use Modules\Farms\Filament\Resources\FarmWorkerAttendanceResource\Pages;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmWorker;
use Modules\Farms\Models\FarmWorkerAttendance;

class FarmWorkerAttendanceResource extends Resource
{
    protected static ?string $model = FarmWorkerAttendance::class;

    protected static ?string $cluster = FarmOperationsCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Attendance';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attendance Record')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(),

                    Select::make('farm_worker_id')
                        ->label('Worker')
                        ->options(function ($get) {
                            $farmId = $get('farm_id');
                            if (! $farmId) {
                                return FarmWorker::pluck('name', 'id');
                            }
                            return FarmWorker::where('farm_id', $farmId)
                                ->get()
                                ->pluck('display_name', 'id');
                        })
                        ->searchable()
                        ->required(),

                    DatePicker::make('attendance_date')
                        ->required()
                        ->default(now()),

                    Select::make('status')
                        ->options([
                            'present'  => 'Present',
                            'absent'   => 'Absent',
                            'half_day' => 'Half Day',
                            'leave'    => 'Leave',
                        ])
                        ->default('present')
                        ->required(),

                    TextInput::make('hours_worked')
                        ->label('Hours Worked')
                        ->numeric()
                        ->step(0.25)
                        ->minValue(0)
                        ->maxValue(24),

                    TextInput::make('overtime_hours')
                        ->label('Overtime Hours')
                        ->numeric()
                        ->step(0.25)
                        ->minValue(0)
                        ->maxValue(12),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable()->searchable(),
                TextColumn::make('farmWorker.name')
                    ->label('Worker')
                    ->getStateUsing(fn ($record) => $record->farmWorker?->display_name ?? '—')
                    ->searchable(query: fn ($query, $search) =>
                        $query->whereHas('farmWorker', fn ($q) =>
                            $q->where('name', 'like', "%{$search}%")
                        )
                    ),
                TextColumn::make('attendance_date')->date()->sortable()->label('Date'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present'  => 'success',
                        'absent'   => 'danger',
                        'half_day' => 'warning',
                        'leave'    => 'info',
                        default    => 'gray',
                    }),
                TextColumn::make('hours_worked')->label('Hours')->numeric(decimalPlaces: 2)->placeholder('—'),
                TextColumn::make('overtime_hours')->label('Overtime')->numeric(decimalPlaces: 2)->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('status')->options([
                    'present'  => 'Present',
                    'absent'   => 'Absent',
                    'half_day' => 'Half Day',
                    'leave'    => 'Leave',
                ]),
                Filter::make('attendance_date')
                    ->form([
                        DatePicker::make('from')->label('From Date'),
                        DatePicker::make('to')->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('attendance_date', '>=', $data['from']))
                            ->when($data['to'], fn ($q) => $q->whereDate('attendance_date', '<=', $data['to']));
                    }),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('attendance_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmWorkerAttendances::route('/'),
            'create' => Pages\CreateFarmWorkerAttendance::route('/create'),
            'view'   => Pages\ViewFarmWorkerAttendance::route('/{record}'),
            'edit'   => Pages\EditFarmWorkerAttendance::route('/{record}/edit'),
        ];
    }
}
