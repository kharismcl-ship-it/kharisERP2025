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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\CropActivityResource\Pages;
use Modules\Farms\Models\CropActivity;

class CropActivityResource extends Resource
{
    protected static ?string $model = CropActivity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Crop Activities';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Activity Details')
                ->columns(2)
                ->schema([
                    Select::make('crop_cycle_id')
                        ->label('Crop Cycle')
                        ->relationship('cropCycle', 'crop_name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('activity_type')
                        ->options(array_combine(
                            CropActivity::ACTIVITY_TYPES,
                            array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), CropActivity::ACTIVITY_TYPES))
                        ))
                        ->required(),

                    DatePicker::make('activity_date')->required(),
                ]),

            Section::make('Labour & Cost')
                ->columns(3)
                ->schema([
                    TextInput::make('duration_hours')->label('Duration (hrs)')->numeric()->step(0.5),
                    TextInput::make('labour_count')->label('Workers')->numeric()->minValue(1)->default(1),
                    TextInput::make('cost')->numeric()->prefix('GHS')->step(0.01),
                    Textarea::make('description')->rows(2)->columnSpanFull(),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([Textarea::make('notes')->rows(2)->columnSpanFull()]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('activity_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->color('primary'),

                TextColumn::make('activity_date')->date('d M Y')->sortable(),
                TextColumn::make('cropCycle.crop_name')->label('Crop')->searchable(),
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('duration_hours')->label('Hours')->numeric(decimalPlaces: 1),
                TextColumn::make('labour_count')->label('Workers'),
                TextColumn::make('cost')->money('GHS'),
                TextColumn::make('description')->limit(40)->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('activity_type')
                    ->options(array_combine(
                        CropActivity::ACTIVITY_TYPES,
                        array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), CropActivity::ACTIVITY_TYPES))
                    )),
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('activity_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCropActivities::route('/'),
            'create' => Pages\CreateCropActivity::route('/create'),
            'view'   => Pages\ViewCropActivity::route('/{record}'),
            'edit'   => Pages\EditCropActivity::route('/{record}/edit'),
        ];
    }
}
