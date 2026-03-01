<?php

namespace Modules\Farms\Filament\Resources\CropCycleResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Farms\Models\CropActivity;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('activity_type')
                ->options(array_combine(
                    CropActivity::ACTIVITY_TYPES,
                    array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), CropActivity::ACTIVITY_TYPES))
                ))
                ->required(),
            DatePicker::make('activity_date')->required(),
            TextInput::make('duration_hours')->label('Duration (hrs)')->numeric()->step(0.5),
            TextInput::make('labour_count')->label('Workers')->numeric()->minValue(1)->default(1),
            TextInput::make('cost')->numeric()->prefix('GHS')->step(0.01),
            Textarea::make('description')->rows(2)->columnSpanFull(),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('activity_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->color('primary'),
                TextColumn::make('activity_date')->date('d M Y')->sortable(),
                TextColumn::make('duration_hours')->label('Hours')->numeric(decimalPlaces: 1)->placeholder('—'),
                TextColumn::make('labour_count')->label('Workers'),
                TextColumn::make('cost')->money('GHS')->placeholder('—'),
                TextColumn::make('description')->limit(40)->placeholder('—'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('activity_date', 'desc');
    }
}
