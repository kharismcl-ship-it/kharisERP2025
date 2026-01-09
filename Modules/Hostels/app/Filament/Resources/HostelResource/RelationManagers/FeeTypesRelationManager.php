<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FeeTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'feeTypes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),

                TextInput::make('code')
                    ->required(),

                TextInput::make('default_amount')
                    ->numeric()
                    ->required(),

                Select::make('billing_cycle')
                    ->options([
                        'one_time' => 'One Time',
                        'per_semester' => 'Per Semester',
                        'per_year' => 'Per Year',
                        'per_night' => 'Per Night',
                    ])
                    ->required(),

                Toggle::make('is_mandatory')
                    ->default(false),

                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('code'),
                TextColumn::make('default_amount')
                    ->numeric()
                    ->money('GHS'),
                TextColumn::make('billing_cycle')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'one_time' => 'gray',
                        'per_semester' => 'info',
                        'per_year' => 'success',
                        'per_night' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('is_mandatory')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1' => 'Yes',
                        '0' => 'No',
                        default => $state,
                    }),
                TextColumn::make('is_active')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1' => 'Yes',
                        '0' => 'No',
                        default => $state,
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
