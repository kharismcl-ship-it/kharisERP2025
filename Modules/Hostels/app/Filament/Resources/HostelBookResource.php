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
use Modules\Hostels\Filament\Resources\HostelBookResource\Pages;
use Modules\Hostels\Models\HostelBook;

class HostelBookResource extends Resource
{
    protected static ?string $model = HostelBook::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 23;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name'),
                Forms\Components\Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->nullable()
                    ->helperText('Leave empty for global availability'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('author')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('isbn')
                    ->maxLength(20)
                    ->label('ISBN'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('cover_image')
                    ->image()
                    ->directory('hostel-books/covers'),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('GHS')
                    ->required()
                    ->default(0),
                Forms\Components\Select::make('book_type')
                    ->options([
                        'physical' => 'Physical Book',
                        'digital'  => 'Digital Download',
                    ])
                    ->required()
                    ->default('physical'),
                Forms\Components\FileUpload::make('digital_file')
                    ->directory('hostel-books/digital')
                    ->helperText('Required for digital books'),
                Forms\Components\TextInput::make('stock_qty')
                    ->numeric()
                    ->label('Stock Quantity')
                    ->default(0)
                    ->helperText('For physical books'),
                Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('author')
                    ->searchable(),
                Tables\Columns\TextColumn::make('book_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'physical' => 'info',
                        'digital'  => 'success',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_qty')
                    ->numeric()
                    ->sortable()
                    ->label('Stock'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('book_type')
                    ->options([
                        'physical' => 'Physical',
                        'digital'  => 'Digital',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active'),
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
            'index'  => Pages\ListHostelBooks::route('/'),
            'create' => Pages\CreateHostelBook::route('/create'),
            'edit'   => Pages\EditHostelBook::route('/{record}/edit'),
        ];
    }
}
