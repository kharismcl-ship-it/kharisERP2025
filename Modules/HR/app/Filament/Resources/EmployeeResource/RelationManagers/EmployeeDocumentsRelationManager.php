<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $label = 'Documents';

    protected static ?string $title = 'Employee Documents';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('document_type')
                    ->options([
                        'cv' => 'CV',
                        'id' => 'ID',
                        'certificate' => 'Certificate',
                        'contract' => 'Contract',
                        'other' => 'Other',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('file_path')
                    ->required()
                    ->preserveFilenames()
                    ->directory('employee-documents'),
                Forms\Components\Select::make('uploaded_by_user_id')
                    ->relationship('uploadedBy', 'name')
                    ->nullable(),
                Forms\Components\Textarea::make('description')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('document_type')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('file_path')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('uploadedBy.name')
                    ->label('Uploaded By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('document_type')
                    ->options([
                        'cv' => 'CV',
                        'id' => 'ID',
                        'certificate' => 'Certificate',
                        'contract' => 'Contract',
                        'other' => 'Other',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
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
