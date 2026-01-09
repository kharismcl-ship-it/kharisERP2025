<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Filament\Resources\HostelBlockResource\Pages;
use Modules\Hostels\Models\HostelBlock;

class HostelBlockResource extends Resource
{
    protected static ?string $model = HostelBlock::class;

    protected static ?string $slug = 'hostel-blocks';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->searchable()
                    ->required(),

                Select::make('name')
                    ->options([
                        'block_a' => 'Block A',
                        'block_b' => 'Block B',
                        'block_c' => 'Block C',
                        'block_d' => 'Block D',
                        'block_e' => 'Block E',
                        'block_f' => 'Block F',
                        'block_g' => 'Block G',
                        'block_h' => 'Block H',
                    ])
                    ->searchable()
                    ->required(),

                Select::make('gender_option')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'both' => 'Both',
                    ])
                    ->required(),

                Textarea::make('description')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('gender_option'),

                TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostelBlocks::route('/'),
            'create' => Pages\CreateHostelBlock::route('/create'),
            'edit' => Pages\EditHostelBlock::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['hostel']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'hostel.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->hostel) {
            $details['Hostel'] = $record->hostel->name;
        }

        return $details;
    }
}
