<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Filament\Resources\HostelChargeResource\Pages;
use Modules\Hostels\Models\HostelCharge;

class HostelChargeResource extends Resource
{
    protected static ?string $model = HostelCharge::class;

    protected static ?string $slug = 'hostel-charges';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->required(),

                TextInput::make('name')
                    ->required(),

                Select::make('charge_type')
                    ->options([
                        'recurring' => 'Re-occurring',
                        'one_time' => 'One-time',
                    ])
                    ->required(),

                TextInput::make('amount')
                    ->required()
                    ->numeric(),

                Checkbox::make('is_active'),

                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->state(fn (?HostelCharge $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->state(fn (?HostelCharge $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('charge_type')
                    ->sortable()
                    ->badge(fn (HostelCharge $record): string => $record->charge_type === 'recurring' ? 'success' : 'danger')
                    ->formatStateUsing(fn (HostelCharge $record): string => $record->charge_type === 'recurring' ? 'Re-occurring' : 'One-time'),

                TextColumn::make('amount'),

                TextColumn::make('is_active')
                    ->formatStateUsing(fn (HostelCharge $record): string => $record->is_active ? 'Yes' : 'No')
                    ->badge(fn (HostelCharge $record): string => $record->is_active ? 'success' : 'danger')
                    ->label('Is Active'),
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
            'index' => Pages\ListHostelCharges::route('/'),
            'create' => Pages\CreateHostelCharge::route('/create'),
            'edit' => Pages\EditHostelCharge::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['company']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'company.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->company) {
            $details['Company'] = $record->company->name;
        }

        return $details;
    }
}
