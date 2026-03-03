<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmDocumentResource\Pages;
use Modules\Farms\Models\FarmDocument;

class FarmDocumentResource extends Resource
{
    protected static ?string $model = FarmDocument::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Documents';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Select::make('document_type')
                        ->label('Document Type')
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

                    TagsInput::make('tags')
                        ->placeholder('Add tags...'),

                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Link to Record')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema([
                    Select::make('documentable_type')
                        ->label('Record Type')
                        ->options([
                            'Modules\Farms\Models\CropCycle'       => 'Crop Cycle',
                            'Modules\Farms\Models\LivestockBatch'   => 'Livestock Batch',
                        ])
                        ->nullable()
                        ->live(),

                    TextInput::make('documentable_id')
                        ->label('Record ID')
                        ->numeric()
                        ->nullable(),
                ]),

            Section::make('File Upload')
                ->schema([
                    FileUpload::make('file_path')
                        ->label('File')
                        ->required()
                        ->acceptedFileTypes(['image/*', 'video/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                        ->directory(fn ($record) => 'farm-documents/' . ($record?->farm_id ?? 'general'))
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('document_type')->badge()->label('Type')
                    ->color(fn (string $state): string => match ($state) {
                        'photo'    => 'info',
                        'video'    => 'primary',
                        'document' => 'gray',
                        'report'   => 'warning',
                        'contract' => 'success',
                        default    => 'gray',
                    }),
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('mime_type')->label('Type')->placeholder('—'),
                TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 1) . ' KB' : '—'),
                TextColumn::make('created_at')->dateTime()->label('Uploaded')->sortable(),
            ])
            ->filters([
                SelectFilter::make('document_type')->label('Type')->options([
                    'photo'    => 'Photo',
                    'video'    => 'Video',
                    'document' => 'Document',
                    'report'   => 'Report',
                    'contract' => 'Contract',
                    'other'    => 'Other',
                ]),
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmDocuments::route('/'),
            'create' => Pages\CreateFarmDocument::route('/create'),
            'view'   => Pages\ViewFarmDocument::route('/{record}'),
            'edit'   => Pages\EditFarmDocument::route('/{record}/edit'),
        ];
    }
}
