<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\RelationManagers;

use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomFieldValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'customFieldValues';

    protected static ?string $title = 'Custom Field Values';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('value')
                    ->label('Value')
                    ->maxLength(1000)
                    ->nullable(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customField.field_label')
                    ->label('Field')
                    ->sortable(),
                TextColumn::make('customField.field_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state ?? '')),
                TextColumn::make('value')
                    ->label('Value')
                    ->placeholder('(not set)')
                    ->limit(60),
            ])
            ->actions([EditAction::make()])
            ->headerActions([])
            ->bulkActions([]);
    }
}
