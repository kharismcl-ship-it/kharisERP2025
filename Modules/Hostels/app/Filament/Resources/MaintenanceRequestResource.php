<?php

namespace Modules\Hostels\Filament\Resources;

    use BackedEnum;
    use Filament\Actions\BulkActionGroup;
    use Filament\Actions\DeleteAction;
    use Filament\Actions\DeleteBulkAction;
    use Filament\Actions\EditAction;
    use Filament\Resources\Resource;
    use Filament\Schemas\Schema;
    use Filament\Support\Icons\Heroicon;
    use Filament\Tables\Table;
    use Modules\Hostels\Filament\Resources\MaintenanceRequestResource\Pages;
    use Modules\Hostels\Models\MaintenanceRequest;

    class MaintenanceRequestResource extends Resource {
        protected static ?string $model = MaintenanceRequest::class;

        protected static ?string $slug = 'maintenance-requests';

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

        PUBLIC static function form(Schema $schema): Schema
        {
        return $schema
        ->components([//
        ]);
        }

        PUBLIC static function table(Table $table): Table
        {
        return $table
        ->columns([
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
        'index' => Pages\ListMaintenanceRequests::route('/'),
'create' => Pages\CreateMaintenanceRequest::route('/create'),
'edit' => Pages\EditMaintenanceRequest::route('/{record}/edit'),
        ];
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return [];
        }
    }
