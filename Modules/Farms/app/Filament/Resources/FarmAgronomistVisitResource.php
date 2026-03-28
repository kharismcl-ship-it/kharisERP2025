<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmAgronomistVisitResource\Pages;
use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\FarmAgronomistVisit;

class FarmAgronomistVisitResource extends Resource
{
    protected static ?string $model = FarmAgronomistVisit::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|\UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Agronomist Visits';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Visit Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_agronomist_id')
                        ->label('Agronomist')
                        ->relationship('agronomist', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required(),

                    Select::make('crop_cycle_id')
                        ->label('Crop Cycle (optional)')
                        ->options(fn (Get $get): array => CropCycle::where('farm_id', $get('farm_id'))
                            ->get()
                            ->mapWithKeys(fn ($c) => [$c->id => "{$c->crop_name} ({$c->season})"])
                            ->toArray())
                        ->searchable()
                        ->nullable(),

                    DatePicker::make('visit_date')->required()->default(today()),

                    Select::make('visit_type')
                        ->options([
                            'routine'           => 'Routine',
                            'problem_diagnosis' => 'Problem Diagnosis',
                            'compliance_audit'  => 'Compliance Audit',
                            'training'          => 'Training',
                            'other'             => 'Other',
                        ])
                        ->required(),

                    Select::make('status')
                        ->options([
                            'scheduled' => 'Scheduled',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('scheduled')
                        ->required(),
                ]),

            Section::make('Observations & Recommendations')
                ->schema([
                    Textarea::make('observations')->rows(3)->columnSpanFull(),
                    Textarea::make('recommendations')->rows(3)->columnSpanFull(),
                ]),

            Section::make('Follow-up')
                ->columns(2)
                ->schema([
                    Toggle::make('follow_up_required')->default(false)->live()->inline(false),
                    DatePicker::make('follow_up_date')
                        ->visible(fn (Get $get): bool => (bool) $get('follow_up_required')),
                ]),

            Section::make('Attachments')
                ->schema([
                    FileUpload::make('attachments')
                        ->multiple()
                        ->directory('agronomist-visits')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable()->searchable(),
                TextColumn::make('agronomist.name')->label('Agronomist')->sortable()->searchable(),
                TextColumn::make('visit_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
                TextColumn::make('visit_date')->date()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'scheduled' => 'info',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                IconColumn::make('follow_up_required')->boolean()->label('Follow-up'),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('status')->options([
                    'scheduled' => 'Scheduled',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('visit_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmAgronomistVisits::route('/'),
            'create' => Pages\CreateFarmAgronomistVisit::route('/create'),
            'edit'   => Pages\EditFarmAgronomistVisit::route('/{record}/edit'),
        ];
    }
}