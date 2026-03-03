<?php

namespace Modules\Farms\Filament\Resources\FarmResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class FarmDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documents';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),

            Select::make('document_type')
                ->label('Type')
                ->options([
                    'photo'    => 'Photo',
                    'video'    => 'Video',
                    'document' => 'Document',
                    'report'   => 'Report',
                    'contract' => 'Contract',
                    'other'    => 'Other',
                ])
                ->default('document')
                ->required(),

            TagsInput::make('tags')->placeholder('Add tags...'),

            Textarea::make('description')->rows(2)->columnSpanFull(),

            FileUpload::make('file_path')
                ->label('File')
                ->required()
                ->acceptedFileTypes(['image/*', 'video/*', 'application/pdf'])
                ->directory('farm-documents')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('document_type')->badge()->color('info'),
                TextColumn::make('mime_type')->label('MIME')->placeholder('—'),
                TextColumn::make('created_at')->dateTime()->label('Uploaded'),
            ])
            ->filters([
                SelectFilter::make('document_type')->options([
                    'photo'    => 'Photo',
                    'video'    => 'Video',
                    'document' => 'Document',
                    'report'   => 'Report',
                    'contract' => 'Contract',
                    'other'    => 'Other',
                ]),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }
}
