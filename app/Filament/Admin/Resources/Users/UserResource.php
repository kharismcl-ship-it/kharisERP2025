<?php

namespace App\Filament\Admin\Resources\Users;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Core';
    }

    public static function getNavigationLabel(): string
    {
        return 'Users';
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Fieldset::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->same('password_confirmation'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->dehydrated(false),
                        // Using Select Component

                    ])->columns(2),

                Section::make('Membership')
                    ->description('Assign this user to companies and grant them a role within each.')
                    ->schema([

                        Forms\Components\Select::make('companies')
                            ->label('Company Assignments')
                            ->multiple()
                            ->relationship('companies', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Which companies can this user access in the tenant switcher?')
                            ->saveRelationshipsUsing(function ($record, $state) {
                                // Sync with is_active = true so canAccessPanel() passes
                                $syncData = collect($state ?? [])
                                    ->mapWithKeys(fn ($id) => [(int) $id => [
                                        'is_active'   => true,
                                        'assigned_at' => now(),
                                    ]])
                                    ->all();
                                $record->companies()->sync($syncData);
                            }),

                        Forms\Components\Toggle::make('is_global_super_admin')
                            ->label('Global Super Admin')
                            ->helperText('Grants unrestricted access to all panels and all companies. Bypasses all role checks.')
                            ->disabled(fn ($record) => $record && $record->getKey() === auth()->id())
                            ->hintIcon(fn ($record) => ($record && $record->getKey() === auth()->id()) ? 'heroicon-o-lock-closed' : null)
                            ->hintIconTooltip('Cannot remove your own super-admin role.')
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($record, $set) {
                                if (! $record) {
                                    return;
                                }
                                $teamKey  = config('permission.column_names.team_foreign_key', 'company_id');
                                $tables   = config('permission.table_names');
                                // Check roles.company_id IS NULL (the role's own column), NOT the pivot column
                                // because model_has_roles.company_id is NOT NULL (part of PRIMARY KEY).
                                $isGlobal = DB::table($tables['model_has_roles'])
                                    ->join($tables['roles'], $tables['roles'] . '.id', '=', $tables['model_has_roles'] . '.role_id')
                                    ->where($tables['model_has_roles'] . '.model_type', get_class($record))
                                    ->where($tables['model_has_roles'] . '.model_id', $record->getKey())
                                    ->where($tables['roles'] . '.name', 'super_admin')
                                    ->whereNull($tables['roles'] . '.' . $teamKey)
                                    ->exists();
                                $set('is_global_super_admin', $isGlobal);
                            })
                            ->saveRelationshipsUsing(function ($record, bool $state) {
                                $teamKey = config('permission.column_names.team_foreign_key', 'company_id');
                                $tables  = config('permission.table_names');

                                $globalRole = DB::table($tables['roles'])
                                    ->where('name', 'super_admin')
                                    ->where('guard_name', 'web')
                                    ->whereNull($teamKey)
                                    ->first();

                                if ($state) {
                                    if (! $globalRole) {
                                        $globalRoleId = DB::table($tables['roles'])->insertGetId([
                                            'name'       => 'super_admin',
                                            'guard_name' => 'web',
                                            $teamKey     => null,
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ]);
                                    } else {
                                        $globalRoleId = $globalRole->id;
                                    }

                                    // model_has_roles.company_id is NOT NULL (part of PRIMARY KEY),
                                    // so we cannot insert with NULL. Assign the global role for every
                                    // existing company. isGlobalSuperAdmin() detects this by checking
                                    // roles.company_id IS NULL (the role's own column).
                                    $companyIds = DB::table('companies')->pluck('id');
                                    foreach ($companyIds as $companyId) {
                                        DB::table($tables['model_has_roles'])->insertOrIgnore([
                                            'role_id'    => $globalRoleId,
                                            'model_type' => get_class($record),
                                            'model_id'   => $record->getKey(),
                                            $teamKey     => $companyId,
                                        ]);
                                    }

                                    // Invalidate cache so EnsureGlobalSuperAdminRole propagates immediately
                                    \Illuminate\Support\Facades\Cache::forget("super_admin_synced_{$record->getKey()}");
                                } else {
                                    // Remove ALL super_admin role assignments (global + company-scoped) for this user.
                                    // model_has_roles.company_id is NOT NULL so ->whereNull() would match nothing.
                                    $superAdminRoleIds = DB::table($tables['roles'])
                                        ->where('name', 'super_admin')
                                        ->pluck('id');

                                    DB::table($tables['model_has_roles'])
                                        ->where('model_type', get_class($record))
                                        ->where('model_id', $record->getKey())
                                        ->whereIn('role_id', $superAdminRoleIds)
                                        ->delete();

                                    \Illuminate\Support\Facades\Cache::forget("super_admin_synced_{$record->getKey()}");
                                }
                            }),

                        Forms\Components\Repeater::make('role_assignments')
                            ->label('Per-Company Role Assignments')
                            ->helperText('Assign a role for this user within each company. Only roles belonging to the selected company are shown.')
                            ->schema([
                                Forms\Components\Select::make('company_id')
                                    ->label('Company')
                                    ->options(Company::orderBy('name')->pluck('name', 'id'))
                                    ->required()
                                    ->live()
                                    ->distinct(),

                                Forms\Components\Select::make('role_id')
                                    ->label('Role')
                                    ->options(function (Get $get): array {
                                        $companyId = $get('company_id');
                                        if (! $companyId) {
                                            return [];
                                        }
                                        $teamKey = config('permission.column_names.team_foreign_key', 'company_id');
                                        // Include company-specific roles AND global (company_id = NULL) roles
                                        return Role::where(function ($q) use ($teamKey, $companyId) {
                                                $q->where($teamKey, $companyId)
                                                  ->orWhereNull($teamKey);
                                            })
                                            ->where('name', '!=', 'super_admin')
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->mapWithKeys(fn ($name, $id) => [
                                                $id => str($name)->headline()->toString(),
                                            ])
                                            ->all();
                                    })
                                    ->required()
                                    ->live(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add role assignment')
                            ->reorderable(false)
                            ->afterStateHydrated(function ($record, $set) {
                                if (! $record) {
                                    return;
                                }
                                $teamKey = config('permission.column_names.team_foreign_key', 'company_id');
                                $tables  = config('permission.table_names');

                                // Exclude super_admin rows — those are managed by the toggle above.
                                $superAdminRoleIds = DB::table($tables['roles'])
                                    ->where('name', 'super_admin')
                                    ->pluck('id');

                                $assignments = DB::table($tables['model_has_roles'])
                                    ->join($tables['roles'], $tables['roles'] . '.id', '=', $tables['model_has_roles'] . '.role_id')
                                    ->where($tables['model_has_roles'] . '.model_type', get_class($record))
                                    ->where($tables['model_has_roles'] . '.model_id', $record->getKey())
                                    ->whereNotNull($tables['model_has_roles'] . '.' . $teamKey)
                                    ->whereNotIn($tables['model_has_roles'] . '.role_id', $superAdminRoleIds)
                                    ->select(
                                        $tables['model_has_roles'] . '.' . $teamKey . ' as company_id',
                                        $tables['roles'] . '.id as role_id'
                                    )
                                    ->get()
                                    ->map(fn ($row) => [
                                        'company_id' => $row->company_id,
                                        'role_id'    => $row->role_id,
                                    ])
                                    ->toArray();

                                $set('role_assignments', $assignments);
                            })
                            ->saveRelationshipsUsing(function ($record, ?array $state) {
                                $teamKey = config('permission.column_names.team_foreign_key', 'company_id');
                                $tables  = config('permission.table_names');

                                // Remove company-scoped role assignments — but NOT super_admin rows,
                                // which are managed exclusively by the Global Super Admin toggle above.
                                $superAdminRoleIds = DB::table($tables['roles'])
                                    ->where('name', 'super_admin')
                                    ->pluck('id');

                                DB::table($tables['model_has_roles'])
                                    ->where('model_type', get_class($record))
                                    ->where('model_id', $record->getKey())
                                    ->whereNotNull($teamKey)
                                    ->whereNotIn('role_id', $superAdminRoleIds)
                                    ->delete();

                                // Re-insert from the repeater state
                                foreach ($state ?? [] as $row) {
                                    if (empty($row['company_id']) || empty($row['role_id'])) {
                                        continue;
                                    }

                                    $roleId  = (int) $row['role_id'];
                                    $companyId = (int) $row['company_id'];

                                    // If the selected role is global (company_id = NULL), ensure a
                                    // company-scoped copy exists so Spatie's team-mode check passes
                                    $role = Role::find($roleId);
                                    if ($role && is_null($role->$teamKey)) {
                                        $scopedRole = Role::firstOrCreate(
                                            ['name' => $role->name, 'guard_name' => $role->guard_name, $teamKey => $companyId],
                                            ['name' => $role->name, 'guard_name' => $role->guard_name, $teamKey => $companyId]
                                        );
                                        // Mirror permissions from the global role to the scoped copy
                                        $scopedRole->syncPermissions($role->permissions);
                                        $roleId = $scopedRole->id;
                                    }

                                    DB::table($tables['model_has_roles'])->updateOrInsert(
                                        [
                                            'role_id'    => $roleId,
                                            'model_type' => get_class($record),
                                            'model_id'   => $record->getKey(),
                                            $teamKey     => $companyId,
                                        ],
                                        [
                                            'role_id'    => $roleId,
                                            'model_type' => get_class($record),
                                            'model_id'   => $record->getKey(),
                                            $teamKey     => $companyId,
                                        ]
                                    );
                                }
                            }),

                    ]),

                Section::make('Notification Preferences')
                    ->description(fn ($record) => $record && $record->employee
                        ? 'Preferences managed through employee record'
                        : 'Set system notification preferences'
                    )
                    ->schema([
                        Forms\Components\Placeholder::make('preference_note')
                            ->content(fn ($record) => $record && $record->employee
                                ? "This user is linked to an employee. Operational notifications (leave, payroll, HR) will use the employee's preferences. System notifications will use these preferences."
                                : 'Set notification preferences for system-level notifications'
                            )
                            ->columnSpanFull(),
                        \Modules\CommunicationCentre\Filament\Components\NotificationPreferenceForm::make('App\\Models\\User')
                            ->hidden(fn ($record) => $record && $record->employee),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('currentCompany.name')->label('Current Company')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
