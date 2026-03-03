<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Construction\Models\ConstructionWorker;
use Modules\Construction\Models\Contractor;

class WorkersRelationManager extends RelationManager
{
    protected static string $relationship = 'workers';

    protected static ?string $title = 'Workers';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            Select::make('category')
                ->options(array_combine(
                    ConstructionWorker::CATEGORIES,
                    array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ConstructionWorker::CATEGORIES))
                ))
                ->default('day_labour')
                ->required(),
            TextInput::make('trade')->maxLength(255),
            TextInput::make('phone')->tel()->maxLength(20),
            TextInput::make('daily_rate')->numeric()->prefix('GHS')->default(0),
            Select::make('status')
                ->options(array_combine(
                    ConstructionWorker::STATUSES,
                    array_map('ucfirst', ConstructionWorker::STATUSES)
                ))
                ->default('active')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('category')
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->badge(),
                TextColumn::make('trade')->placeholder('—'),
                TextColumn::make('phone')->placeholder('—'),
                TextColumn::make('daily_rate')->money('GHS'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'inactive'  => 'gray',
                        'suspended' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->headerActions([\Filament\Tables\Actions\CreateAction::make()])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }
}
