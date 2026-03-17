<?php

namespace Modules\ManufacturingWater\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\ManufacturingWater\Filament\Resources\MwDistributionRecordResource\Pages;
use Modules\ManufacturingWater\Models\MwDistributionRecord;

class MwDistributionRecordResource extends Resource
{
    protected static ?string $model = MwDistributionRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Water';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Grid::make(3)->schema([
                    Select::make('plant_id')
                        ->label('Plant')
                        ->relationship('plant', 'name')
                        ->searchable()
                        ->required(),
                    DatePicker::make('distribution_date')->required()->default(now()),
                    TextInput::make('distribution_reference')->label('Reference')->maxLength(50)->helperText('Auto-generated if blank'),
                ]),
                TextInput::make('destination')->required()->maxLength(255)->columnSpanFull(),
                Grid::make(3)->schema([
                    TextInput::make('volume_liters')->label('Volume (Litres)')->numeric()->step(0.01)->required(),
                    TextInput::make('unit_price')->label('Unit Price (GHS)')->numeric()->prefix('GHS')->step(0.0001),
                    TextInput::make('total_amount')->label('Total Amount (GHS)')->numeric()->prefix('GHS')->step(0.01)->disabled(),
                ]),
                TextInput::make('vehicle_info')->label('Vehicle Info')->maxLength(255),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                TextEntry::make('distribution_reference')->label('Reference'),
                TextEntry::make('plant.name')->label('Plant'),
                TextEntry::make('distribution_date')->date(),
                TextEntry::make('destination')->columnSpanFull(),
                TextEntry::make('volume_liters')->label('Volume (L)'),
                TextEntry::make('unit_price')->label('Unit Price')->money('GHS')->placeholder('—'),
                TextEntry::make('total_amount')->label('Total Amount')->money('GHS'),
                TextEntry::make('vehicle_info')->label('Vehicle Info')->placeholder('—'),
                TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('distribution_reference')->label('Reference')->searchable(),
                TextColumn::make('plant.name')->label('Plant')->searchable(),
                TextColumn::make('distribution_date')->date()->sortable(),
                TextColumn::make('destination')->searchable(),
                TextColumn::make('volume_liters')->label('Volume (L)')->numeric(decimalPlaces: 2),
                TextColumn::make('total_amount')->money('GHS')->sortable(),
                TextColumn::make('vehicle_info')->label('Vehicle'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plant_id')
                    ->label('Plant')
                    ->relationship('plant', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ])
            ->defaultSort('distribution_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMwDistributionRecords::route('/'),
            'create' => Pages\CreateMwDistributionRecord::route('/create'),
            'view'   => Pages\ViewMwDistributionRecord::route('/{record}'),
            'edit'   => Pages\EditMwDistributionRecord::route('/{record}/edit'),
        ];
    }
}
