<?php

namespace Modules\Farms\Filament\Resources\FarmResource\RelationManagers;

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
use Modules\Farms\Models\SoilTestRecord;

class SoilTestRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'soilTestRecords';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('test_date')->required()->default(now()),
            TextInput::make('tested_by')->maxLength(150),
            TextInput::make('lab_reference')->maxLength(100),
            Select::make('texture')
                ->options(array_combine(
                    SoilTestRecord::TEXTURES,
                    array_map(fn ($t) => str_replace('_', ' ', ucfirst($t)), SoilTestRecord::TEXTURES)
                ))->nullable(),
            TextInput::make('ph_level')->label('pH')->numeric()->step(0.01),
            TextInput::make('nitrogen_pct')->label('N%')->numeric()->step(0.001),
            TextInput::make('phosphorus_ppm')->label('P ppm')->numeric()->step(0.001),
            TextInput::make('potassium_ppm')->label('K ppm')->numeric()->step(0.001),
            TextInput::make('organic_matter_pct')->label('OM%')->numeric()->step(0.01),
            Textarea::make('recommendations')->rows(3)->columnSpanFull(),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('test_date')->date()->sortable(),
                TextColumn::make('tested_by')->toggleable(),
                TextColumn::make('ph_level')->label('pH'),
                TextColumn::make('nitrogen_pct')->label('N%'),
                TextColumn::make('phosphorus_ppm')->label('P ppm'),
                TextColumn::make('potassium_ppm')->label('K ppm'),
                TextColumn::make('organic_matter_pct')->label('OM%'),
                TextColumn::make('texture')
                    ->badge()->color('info')
                    ->formatStateUsing(fn ($state) => $state ? str_replace('_', ' ', ucfirst($state)) : '—'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('test_date', 'desc');
    }
}