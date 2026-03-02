<?php

namespace App\Filament\CompanyAdmin\Resources\Users;

use App\Filament\CompanyAdmin\Resources\Users\Pages\CreateUser;
use App\Filament\CompanyAdmin\Resources\Users\Pages\EditUser;
use App\Filament\CompanyAdmin\Resources\Users\Pages\ListUsers;
use App\Filament\CompanyAdmin\Resources\Users\Pages\ViewUser;
use App\Filament\CompanyAdmin\Resources\Users\Schemas\UserForm;
use App\Filament\CompanyAdmin\Resources\Users\Schemas\UserInfolist;
use App\Filament\CompanyAdmin\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    /**
     * User belongs to companies via a BelongsToMany, not a BelongsTo.
     * Tell Filament to use the 'companies' pivot relationship for tenant
     * ownership checks instead of the default 'company' (which doesn't exist).
     */
    protected static ?string $tenantOwnershipRelationshipName = 'companies';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Core';
    }

    public static function getNavigationLabel(): string
    {
        return 'Users';
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
