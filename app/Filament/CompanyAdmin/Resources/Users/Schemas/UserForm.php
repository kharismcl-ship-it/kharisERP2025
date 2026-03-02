<?php

namespace App\Filament\CompanyAdmin\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->confirmed()
                            ->maxLength(255),

                        TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(false),

                        Toggle::make('email_verified')
                            ->label('Mark Email as Verified')
                            ->dehydrated(false)
                            ->afterStateHydrated(fn ($state, $record, $set) =>
                                $set('email_verified', (bool) $record?->email_verified_at)
                            )
                            ->columnSpanFull(),
                    ]),

                Section::make('Company Role')
                    ->description('Assign a role for this user within the current company.')
                    ->schema([
                        Select::make('roles')
                            ->label('Role')
                            ->options(function (): array {
                                $tenant = Filament::getTenant();
                                if (! $tenant) {
                                    return [];
                                }

                                $teamKey = config('permission.column_names.team_foreign_key', 'company_id');

                                return Role::where($teamKey, $tenant->getKey())
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->mapWithKeys(fn ($name, $id) => [
                                        $id => str($name)->headline()->toString(),
                                    ])
                                    ->all();
                            })
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Only roles belonging to the current company are shown.')
                            ->saveRelationshipsUsing(function ($record, $state) {
                                // Assign roles scoped to the current tenant
                                $tenant  = Filament::getTenant();
                                $teamKey = config('permission.column_names.team_foreign_key', 'company_id');

                                // Remove existing company-scoped roles first
                                $existingRoleIds = Role::where($teamKey, $tenant->getKey())
                                    ->pluck('id');

                                $record->roles()->detach($existingRoleIds);

                                // Attach the newly selected roles
                                if (! empty($state)) {
                                    foreach ($state as $roleId) {
                                        DB::table(
                                            config('permission.table_names.model_has_roles')
                                        )->updateOrInsert([
                                            'role_id'    => $roleId,
                                            'model_type' => get_class($record),
                                            'model_id'   => $record->getKey(),
                                            $teamKey     => $tenant->getKey(),
                                        ], [
                                            'role_id'    => $roleId,
                                            'model_type' => get_class($record),
                                            'model_id'   => $record->getKey(),
                                            $teamKey     => $tenant->getKey(),
                                        ]);
                                    }
                                }
                            })
                            ->afterStateHydrated(function ($record, $set) {
                                if (! $record) {
                                    return;
                                }

                                $tenant  = Filament::getTenant();
                                $teamKey = config('permission.column_names.team_foreign_key', 'company_id');
                                $tables  = config('permission.table_names');

                                // Fetch role IDs assigned to this user within the current company
                                $roleIds = \Illuminate\Support\Facades\DB::table($tables['model_has_roles'])
                                    ->join($tables['roles'], $tables['roles'] . '.id', '=', $tables['model_has_roles'] . '.role_id')
                                    ->where($tables['model_has_roles'] . '.model_type', get_class($record))
                                    ->where($tables['model_has_roles'] . '.model_id', $record->getKey())
                                    ->where($tables['model_has_roles'] . '.' . $teamKey, $tenant?->getKey())
                                    ->pluck($tables['roles'] . '.id')
                                    ->all();

                                $set('roles', $roleIds);
                            }),
                    ]),
            ]);
    }
}
