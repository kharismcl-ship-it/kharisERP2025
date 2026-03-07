<?php

namespace Modules\ClientService\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\ClientService\Filament\Resources\CsVisitorProfileResource\Pages;
use Modules\ClientService\Filament\Resources\CsVisitorProfileResource\RelationManagers\VisitLogRelationManager;
use Modules\ClientService\Models\CsVisitorProfile;

class CsVisitorProfileResource extends Resource
{
    protected static ?string $model = CsVisitorProfile::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected static string|\UnitEnum|null $navigationGroup = 'Client Services';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Visitor Profiles';

    protected static ?string $modelLabel = 'Visitor Profile';

    protected static ?string $pluralModelLabel = 'Visitor Profiles';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->searchable()->sortable(),
                TextColumn::make('phone')->searchable()->placeholder('—'),
                TextColumn::make('email')->searchable()->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('organization')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')->label('Company')->sortable(),
                TextColumn::make('visits_count')
                    ->label('Visits')
                    ->counts('visits')
                    ->sortable(),
                TextColumn::make('updated_at')->label('Last Updated')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('full_name')
            ->actions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            VisitLogRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCsVisitorProfiles::route('/'),
            'view'  => Pages\ViewCsVisitorProfile::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'phone', 'email'];
    }
}