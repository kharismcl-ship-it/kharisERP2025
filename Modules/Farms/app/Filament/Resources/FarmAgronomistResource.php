<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmAgronomistResource\Pages;
use Modules\Farms\Filament\Resources\FarmAgronomistResource\RelationManagers\AgronomistVisitsRelationManager;
use Modules\Farms\Models\FarmAgronomist;

class FarmAgronomistResource extends Resource
{
    protected static ?string $model = FarmAgronomist::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Agronomists';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Agronomist Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('title')->label('Title / Role')->maxLength(255)->placeholder('e.g. Agricultural Extension Officer'),
                    TextInput::make('organization')->maxLength(255)->placeholder('e.g. MoFA District Office'),
                    TextInput::make('specialization')->maxLength(255)->placeholder('e.g. Crops, Livestock, Soil'),
                    TextInput::make('phone')->tel()->maxLength(30),
                    TextInput::make('email')->email()->maxLength(255),
                    Toggle::make('is_active')->default(true)->inline(false),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Agronomist Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('title')->placeholder('—'),
                    TextEntry::make('organization')->placeholder('—'),
                    TextEntry::make('specialization')->placeholder('—'),
                    TextEntry::make('phone')->placeholder('—'),
                    TextEntry::make('email')->placeholder('—'),
                    IconEntry::make('is_active')->boolean()->label('Active'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('title')->placeholder('—')->toggleable(),
                TextColumn::make('organization')->placeholder('—')->searchable(),
                TextColumn::make('specialization')->placeholder('—')->toggleable(),
                TextColumn::make('phone')->placeholder('—')->toggleable(),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelationManagers(): array
    {
        return [
            AgronomistVisitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmAgronomists::route('/'),
            'create' => Pages\CreateFarmAgronomist::route('/create'),
            'edit'   => Pages\EditFarmAgronomist::route('/{record}/edit'),
        ];
    }
}