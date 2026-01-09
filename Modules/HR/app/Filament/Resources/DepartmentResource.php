<?php

namespace Modules\HR\Filament\Resources;

    use BackedEnum;
    use Filament\Actions\BulkActionGroup;
    use Filament\Actions\DeleteAction;
    use Filament\Actions\DeleteBulkAction;
    use Filament\Actions\EditAction;
    use Filament\Resources\Resource;
    use Filament\Schemas\Schema;
    use Filament\Support\Icons\Heroicon;
    use Filament\Tables\Table;
    use Modules\HR\Filament\Resources\DepartmentResource\Pages;
    use Modules\HR\Models\Department;

    class DepartmentResource extends Resource {
        protected static ?string $model = Department::class;

        protected static ?string $slug = 'departments';

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
        'index' => Pages\ListDepartments::route('/'),
'create' => Pages\CreateDepartment::route('/create'),
'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return [];
        }
    }
