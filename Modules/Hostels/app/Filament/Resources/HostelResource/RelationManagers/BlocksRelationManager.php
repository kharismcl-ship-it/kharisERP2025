<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class BlocksRelationManager extends RelationManager
{
    protected static string $relationship = 'blocks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('name')
                    ->options([
                        'Block A' => 'Block A',
                        'Block B' => 'Block B',
                        'Block C' => 'Block C',
                        'Block D' => 'Block D',
                        'Block E' => 'Block E',
                        'Block F' => 'Block F',
                        'Block G' => 'Block G',
                        'Block H' => 'Block H',
                    ])
                    ->searchable()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where(
                            'hostel_id',
                            $this->getOwnerRecord()->id,
                        ),
                    )
                    ->required(),

                Select::make('gender_option')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'both' => 'Both',
                    ])
                    ->required(),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->default('This is a block of the hostel')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description')
                    ->limit(50),
                TextColumn::make('floors_count')
                    ->label('Floors')
                    ->counts([
                        'floors as floors_count' => fn (Builder $query) => $query->where('hostel_id', $this->getOwnerRecord()->id),
                    ]),
                TextColumn::make('rooms_count')
                    ->label('Rooms')
                    ->counts([
                        'rooms as rooms_count' => fn (Builder $query) => $query->where('hostel_id', $this->getOwnerRecord()->id),
                    ]),
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
