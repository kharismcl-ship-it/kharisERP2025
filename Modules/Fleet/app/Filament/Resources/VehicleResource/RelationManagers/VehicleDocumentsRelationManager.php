<?php

namespace Modules\Fleet\Filament\Resources\VehicleResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Fleet\Models\VehicleDocument;

class VehicleDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->options(array_combine(
                    VehicleDocument::TYPES,
                    array_map('ucfirst', VehicleDocument::TYPES)
                ))
                ->required(),
            TextInput::make('document_number')->label('Document Number')->maxLength(100),
            DatePicker::make('issue_date')->label('Issue Date'),
            DatePicker::make('expiry_date')->label('Expiry Date'),
            FileUpload::make('file_path')->label('Upload Document')->directory('fleet/documents'),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->badge(),
                TextColumn::make('document_number')->label('Document No.'),
                TextColumn::make('issue_date')->date(),
                TextColumn::make('expiry_date')->date()->sortable()
                    ->color(fn ($record): ?string => $record?->is_expired ? 'danger' : ($record?->is_expiring_soon ? 'warning' : null)),
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
            ])
            ->defaultSort('expiry_date', 'asc');
    }
}
