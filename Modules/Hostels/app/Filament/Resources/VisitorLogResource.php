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
    use Modules\Hostels\Filament\Resources\VisitorLogResource\Pages;
    use Modules\Hostels\Models\VisitorLog;

    class VisitorLogResource extends Resource {
        protected static ?string $model = VisitorLog::class;

        protected static ?string $slug = 'visitor-logs';

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
        'index' => Pages\ListVisitorLogs::route('/'),
'create' => Pages\CreateVisitorLog::route('/create'),
'edit' => Pages\EditVisitorLog::route('/{record}/edit'),
        ];
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return [];
        }
    }
