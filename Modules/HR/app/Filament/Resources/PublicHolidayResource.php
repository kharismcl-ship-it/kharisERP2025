<?php

namespace Modules\HR\Filament\Resources;

use Modules\HR\Filament\Clusters\HrSetupCluster;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\PublicHolidayResource\Pages;
use Modules\HR\Models\PublicHoliday;

class PublicHolidayResource extends Resource
{
    protected static ?string $cluster = HrSetupCluster::class;
    protected static ?string $model = PublicHoliday::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;


    protected static ?int $navigationSort = 55;

    protected static ?string $navigationLabel = 'Public Holidays';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Holiday Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload(),
                        Forms\Components\TextInput::make('name')->maxLength(150),
                        Forms\Components\DatePicker::make('date')->native(false),
                        Forms\Components\Toggle::make('is_recurring_annually')
                            ->label('Recurring Annually')
                            ->default(true)->inline(false),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\IconColumn::make('is_recurring_annually')
                    ->label('Recurring')->boolean(),
                Tables\Columns\TextColumn::make('description')->limit(50)->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\TernaryFilter::make('is_recurring_annually')->label('Recurring'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPublicHolidays::route('/'),
            'create' => Pages\CreatePublicHoliday::route('/create'),
            'view'   => Pages\ViewPublicHoliday::route('/{record}'),
            'edit'   => Pages\EditPublicHoliday::route('/{record}/edit'),
        ];
    }
}