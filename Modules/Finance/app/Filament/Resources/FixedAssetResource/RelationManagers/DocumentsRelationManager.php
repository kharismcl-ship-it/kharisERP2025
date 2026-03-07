<?php

namespace Modules\Finance\Filament\Resources\FixedAssetResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Modules\Finance\Models\FixedAssetDocument;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documents & Attachments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Document Title')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g. Purchase Invoice #INV-0042')
                ->columnSpanFull(),

            Select::make('document_type')
                ->label('Document Type')
                ->options(FixedAssetDocument::DOCUMENT_TYPES)
                ->default('other')
                ->required(),

            FileUpload::make('file_path')
                ->label('File')
                ->directory('fixed-asset-documents')
                ->required()
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/jpeg', 'image/png', 'image/webp',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
                ->maxSize(20480), // 20 MB

            Textarea::make('notes')
                ->rows(2)
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('document_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => FixedAssetDocument::DOCUMENT_TYPES[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'contract'   => 'primary',
                        'invoice'    => 'success',
                        'warranty'   => 'warning',
                        'insurance'  => 'info',
                        'inspection' => 'danger',
                        default      => 'gray',
                    }),

                TextColumn::make('file_name')
                    ->label('File')
                    ->formatStateUsing(fn ($state, $record) => $state ?: basename($record->file_path))
                    ->placeholder('—'),

                TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state, $record) => $record->formattedFileSize())
                    ->placeholder('—'),

                TextColumn::make('uploadedBy.name')
                    ->label('Uploaded By')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Uploaded At')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['uploaded_by_user_id'] = Auth::id();
                        if (! empty($data['file_path'])) {
                            $data['file_name'] = basename($data['file_path']);
                        }
                        return $data;
                    }),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}