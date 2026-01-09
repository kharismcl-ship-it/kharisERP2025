<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class FloorsRelationManager extends RelationManager
{
    protected static string $relationship = 'floors';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hostel_block_id')
                    ->relationship(
                        name: 'hostelBlock',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('hostel_id', $this->getOwnerRecord()->id),
                    )
                    ->searchable()
                    ->preload()
                    ->label('Block')
                    ->required(),

                Select::make('name')
                    ->label('Name')
                    ->options([
                        '1st Floor' => '1st Floor',
                        '2nd Floor' => '2nd Floor',
                        '3rd Floor' => '3rd Floor',
                        '4th Floor' => '4th Floor',
                        '5th Floor' => '5th Floor',
                        '6th Floor' => '6th Floor',
                        '7th Floor' => '7th Floor',
                        '8th Floor' => '8th Floor',
                        '9th Floor' => '9th Floor',
                        '10th Floor' => '10th Floor',
                    ])
                    ->searchable()
                    ->required()
                    ->rules([
                        function (callable $get) {
                            return Rule::unique('hostel_floors', 'name')
                                ->where('hostel_id', $this->getOwnerRecord()->id)
                                ->where('hostel_block_id', $get('hostel_block_id'))
                                ->ignore($this->id());
                        },
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $level = (int) filter_var($state, FILTER_SANITIZE_NUMBER_INT);
                            $set('level', $level);
                        }
                    }),

                TextInput::make('level')
                    ->label('Level')
                    ->numeric()
                    ->required()
                    ->readonly(fn ($get) => (bool) $get('name')),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('level'),
                TextColumn::make('hostelBlock.name')
                    ->label('Block'),
                TextColumn::make('rooms_count')
                    ->label('Rooms')
                    ->counts('rooms'),
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
