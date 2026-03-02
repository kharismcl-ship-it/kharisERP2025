<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\PosSessionResource\Pages;
use Modules\Sales\Models\PosSession;

class PosSessionResource extends Resource
{
    protected static ?string $model = PosSession::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-clock';
    protected static string|\UnitEnum|null   $navigationGroup = 'POS';
    protected static ?int                    $navigationSort  = 41;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Session')->columns(2)->schema([
                Select::make('terminal_id')
                    ->label('Terminal')
                    ->relationship('terminal', 'name')
                    ->required()->searchable()->preload(),
                Select::make('cashier_id')
                    ->label('Cashier')
                    ->relationship('cashier', 'name')
                    ->required()->searchable()->preload(),
                DateTimePicker::make('opened_at')->required(),
                DateTimePicker::make('closed_at'),
                TextInput::make('opening_float')->numeric()->prefix('GHS')->default(0),
                TextInput::make('closing_cash')->numeric()->prefix('GHS'),
                Select::make('status')
                    ->options(['open' => 'Open', 'closed' => 'Closed'])
                    ->default('open'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('terminal.name'),
                TextColumn::make('cashier.name'),
                TextColumn::make('opened_at')->dateTime()->sortable(),
                TextColumn::make('closed_at')->dateTime(),
                TextColumn::make('opening_float')->money('GHS'),
                TextColumn::make('closing_cash')->money('GHS'),
                TextColumn::make('cash_variance')->money('GHS'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state) => $state === 'open' ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('status')->options(['open' => 'Open', 'closed' => 'Closed']),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosSessions::route('/'),
            'create' => Pages\CreatePosSession::route('/create'),
            'edit'   => Pages\EditPosSession::route('/{record}/edit'),
        ];
    }
}