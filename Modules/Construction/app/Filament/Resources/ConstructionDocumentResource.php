<?php

namespace Modules\Construction\Filament\Resources;

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
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Construction\Filament\Resources\ConstructionDocumentResource\Pages;
use Modules\Construction\Models\ConstructionDocument;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\ProjectPhase;

class ConstructionDocumentResource extends Resource
{
    protected static ?string $model = ConstructionDocument::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = 'Documents';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document Details')->schema([
                Grid::make(2)->schema([
                    Select::make('construction_project_id')
                        ->label('Project')
                        ->options(fn () => ConstructionProject::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(),
                    Select::make('project_phase_id')
                        ->label('Phase')
                        ->options(fn () => ProjectPhase::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->nullable(),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('title')->required()->maxLength(255),
                    Select::make('document_type')
                        ->options(array_combine(
                            ConstructionDocument::DOCUMENT_TYPES,
                            array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ConstructionDocument::DOCUMENT_TYPES))
                        ))
                        ->default('other')
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('version')->maxLength(50),
                    TagsInput::make('tags')->placeholder('Add tag...'),
                ]),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                FileUpload::make('file_paths')
                    ->label('Files')
                    ->multiple()
                    ->disk('public')
                    ->directory('construction-documents')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'video/mp4',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/acad',
                        'image/vnd.dwg',
                    ])
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->limit(40),
                TextColumn::make('project.name')->label('Project')->searchable(),
                TextColumn::make('phase.name')->label('Phase')->placeholder('—'),
                TextColumn::make('document_type')->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('version')->placeholder('—'),
                TextColumn::make('created_at')->label('Uploaded')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('document_type')
                    ->options(array_combine(
                        ConstructionDocument::DOCUMENT_TYPES,
                        array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ConstructionDocument::DOCUMENT_TYPES))
                    )),
                SelectFilter::make('construction_project_id')
                    ->label('Project')
                    ->relationship('project', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListConstructionDocuments::route('/'),
            'create' => Pages\CreateConstructionDocument::route('/create'),
            'view'   => Pages\ViewConstructionDocument::route('/{record}'),
            'edit'   => Pages\EditConstructionDocument::route('/{record}/edit'),
        ];
    }
}
