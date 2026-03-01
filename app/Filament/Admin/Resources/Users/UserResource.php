<?php

namespace App\Filament\Admin\Resources\Users;

use App\Models\Company;
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
                    ->schema([

                        // Using Select Component
                        // Forms\Components\Select::make('roles_company_id')
                        //     ->label('Company')
                        //     ->options(Company::query()->pluck('name', 'id'))
                        //     ->reactive()
                        //     ->required(),

                        // Forms\Components\Select::make('roles')
                        //     ->label('Roles')
                        //     ->relationship('roles', 'name', fn (Builder $query, Get $get) =>
                        //         $get('roles_company_id')
                        //             ? $query->where('company_id', $get('roles_company_id'))
                        //             : $query
                        //     )
                        //     ->multiple()
                        //     ->required(),

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
