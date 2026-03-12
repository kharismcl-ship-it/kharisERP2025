<?php

namespace Modules\Hostels\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Hostels\Filament\Resources\HostelMovieResource\Pages;
use Modules\Hostels\Models\HostelMovie;

class HostelMovieResource extends Resource
{
    protected static ?string $model = HostelMovie::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedFilm;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name'),
                Forms\Components\Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->nullable()
                    ->helperText('Leave empty to make globally available to all hostels'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('genre')
                    ->maxLength(100),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('thumbnail')
                    ->image()
                    ->directory('hostel-movies/thumbnails'),
                Forms\Components\TextInput::make('video_url')
                    ->url()
                    ->maxLength(500)
                    ->helperText('YouTube/streaming URL'),
                Forms\Components\FileUpload::make('video_file')
                    ->directory('hostel-movies/files')
                    ->acceptedFileTypes(['video/mp4', 'video/webm']),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('GHS')
                    ->default(0),
                Forms\Components\TextInput::make('duration_minutes')
                    ->numeric()
                    ->label('Duration (minutes)'),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
                Forms\Components\Toggle::make('requires_payment')
                    ->default(true),
                Forms\Components\Toggle::make('is_globally_available')
                    ->label('Available to All Hostels')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hostel.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('genre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->numeric()
                    ->sortable()
                    ->label('Duration (min)'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('requires_payment')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('requires_payment'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHostelMovies::route('/'),
            'create' => Pages\CreateHostelMovie::route('/create'),
            'edit'   => Pages\EditHostelMovie::route('/{record}/edit'),
        ];
    }
}
