<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Modules\Requisition\Models\RequisitionActivity;

class RequisitionAttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Documents & Attachments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('label')
                ->label('Document Type / Label')
                ->placeholder('e.g. Vendor Invoice, Technical Spec, Quote')
                ->maxLength(255)
                ->nullable()
                ->columnSpanFull(),

            FileUpload::make('file_path')
                ->label('File')
                ->directory('requisition-attachments')
                ->required()
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/jpeg', 'image/png', 'image/webp',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
                ->maxSize(10240)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Document')
                    ->default('—')
                    ->searchable(),
                TextColumn::make('file_name')
                    ->label('File')
                    ->default(fn ($record) => basename($record->file_path)),
                TextColumn::make('uploadedBy.name')
                    ->label('Uploaded By'),
                TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 1) . ' KB' : '—'),
                TextColumn::make('created_at')->dateTime()->label('Uploaded At'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['uploaded_by_user_id'] = Auth::id();
                        if (isset($data['file_path'])) {
                            $data['file_name'] = basename($data['file_path']);
                        }
                        return $data;
                    })
                    ->after(function ($record) {
                        RequisitionActivity::log(
                            $this->getOwnerRecord(),
                            'attachment_uploaded',
                            'Document uploaded: ' . ($record->label ?: basename($record->file_path)),
                        );
                    }),
            ])
            ->actions([
                DeleteAction::make()
                    ->after(function ($record) {
                        RequisitionActivity::log(
                            $this->getOwnerRecord(),
                            'attachment_removed',
                            'Document removed: ' . ($record->label ?: basename($record->file_path)),
                        );
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}