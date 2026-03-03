<?php

namespace Modules\ITSupport\Filament\Resources\ItTrainingSessionResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItTrainingAttendeesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendees';

    protected static ?string $title = 'Attendees';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('employee_id')
                ->label('Employee')
                ->relationship('employee', 'full_name')
                ->searchable()
                ->preload()
                ->required(),
            Grid::make(2)->schema([
                Toggle::make('attended')->label('Attended')->default(false)->inline(false),
                TextInput::make('rating')->numeric()->minValue(1)->maxValue(5)->nullable()->label('Rating (1-5)'),
            ]),
            Textarea::make('feedback')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')->label('Employee')->searchable(),
                IconColumn::make('attended')->boolean(),
                TextColumn::make('rating')->label('Rating'),
                TextColumn::make('feedback')->limit(50)->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
