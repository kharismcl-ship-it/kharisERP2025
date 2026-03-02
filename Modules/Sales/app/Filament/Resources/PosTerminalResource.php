<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\PosTerminalResource\Pages;
use Modules\Sales\Models\PosTerminal;

class PosTerminalResource extends Resource
{
    protected static ?string $model = PosTerminal::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-computer-desktop';
    protected static string|\UnitEnum|null   $navigationGroup = 'POS';
    protected static ?int                    $navigationSort  = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Terminal')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(100),
                TextInput::make('location')->maxLength(200),
                Toggle::make('is_active')->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('location'),
                ToggleColumn::make('is_active'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosTerminals::route('/'),
            'create' => Pages\CreatePosTerminal::route('/create'),
            'edit'   => Pages\EditPosTerminal::route('/{record}/edit'),
        ];
    }
}