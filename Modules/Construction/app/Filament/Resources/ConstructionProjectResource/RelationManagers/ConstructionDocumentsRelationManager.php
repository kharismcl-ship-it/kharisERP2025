<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Construction\Models\ConstructionDocument;

class ConstructionDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documents';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
            Select::make('document_type')
                ->options(array_combine(
                    ConstructionDocument::DOCUMENT_TYPES,
                    array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ConstructionDocument::DOCUMENT_TYPES))
                ))
                ->default('other')
                ->required(),
            TextInput::make('version')->maxLength(50),
            TagsInput::make('tags'),
            FileUpload::make('file_paths')
                ->multiple()
                ->disk('public')
                ->directory('construction-documents')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->limit(40),
                TextColumn::make('document_type')->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('version')->placeholder('—'),
                TextColumn::make('created_at')->label('Uploaded')->date()->sortable(),
            ])
            ->headerActions([\Filament\Tables\Actions\CreateAction::make()])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ])])
            ->defaultSort('created_at', 'desc');
    }
}
