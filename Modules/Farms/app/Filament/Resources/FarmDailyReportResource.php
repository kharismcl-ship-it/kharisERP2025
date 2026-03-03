<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmDailyReportResource\Pages;
use Modules\Farms\Models\FarmDailyReport;
use Modules\Farms\Models\FarmWorker;

class FarmDailyReportResource extends Resource
{
    protected static ?string $model = FarmDailyReport::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Daily Reports';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Report Details')
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
                        ->label('Reporter')
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

                    DatePicker::make('report_date')
                        ->required()
                        ->default(now()),

                    Select::make('status')
                        ->options([
                            'draft'     => 'Draft',
                            'submitted' => 'Submitted',
                            'reviewed'  => 'Reviewed',
                        ])
                        ->default('draft')
                        ->required(),
                ]),

            Section::make('Report Content')
                ->schema([
                    Textarea::make('summary')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('activities_done')
                        ->label('Activities Done')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),

                    Textarea::make('issues_noted')
                        ->label('Issues Noted')
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('recommendations')
                        ->rows(3)
                        ->columnSpanFull(),

                    \Filament\Forms\Components\TextInput::make('weather_observation')
                        ->label('Weather Observation')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),

            Section::make('Attachments')
                ->collapsible()
                ->schema([
                    FileUpload::make('attachments')
                        ->multiple()
                        ->image()
                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                        ->maxFiles(10)
                        ->directory(fn ($record) => 'farm-reports/' . ($record?->farm_id ?? 'general'))
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('report_date')->date()->sortable()->label('Date'),
                TextColumn::make('farm.name')->label('Farm')->searchable()->sortable(),
                TextColumn::make('farmWorker.name')
                    ->label('Reporter')
                    ->getStateUsing(fn ($record) => $record->farmWorker?->display_name ?? '—'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'warning',
                        'reviewed'  => 'success',
                        default     => 'gray',
                    }),
                TextColumn::make('summary')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('status')->options([
                    'draft'     => 'Draft',
                    'submitted' => 'Submitted',
                    'reviewed'  => 'Reviewed',
                ]),
                Filter::make('report_date')
                    ->form([
                        DatePicker::make('from')->label('From Date'),
                        DatePicker::make('to')->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('report_date', '>=', $data['from']))
                            ->when($data['to'], fn ($q) => $q->whereDate('report_date', '<=', $data['to']));
                    }),
            ])
            ->actions([
                Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->action(fn ($record) => $record->update(['status' => 'submitted'])),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('report_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmDailyReports::route('/'),
            'create' => Pages\CreateFarmDailyReport::route('/create'),
            'view'   => Pages\ViewFarmDailyReport::route('/{record}'),
            'edit'   => Pages\EditFarmDailyReport::route('/{record}/edit'),
        ];
    }
}
