<?php

namespace App\Filament\Admin\Resources\Companies;

use App\Models\Company;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-building-office-2';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Core';
    }

    public static function getNavigationLabel(): string
    {
        return 'Companies';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', str($state)->slug()) : null),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
//                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->dehydrated(true),
                Forms\Components\Select::make('type')
                    ->options([
                        'consult' => 'Consult',
                        'hostel' => 'Hostel',
                        'farm' => 'Farm',
                        'construction' => 'Construction',
                        'water' => 'Water Manufacturing',
                        'paper' => 'Paper Manufacturing',
                        'hr' => 'HR',
                        'procurement' => 'Procurement',
                        'fleet' => 'Fleet',
                        'finance' => 'Finance',
                    ])
                    ->required(),
                Forms\Components\Select::make('parent_company_id')
                    ->label('Parent Company')
                    ->relationship('parentCompany', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Select a parent company if this is a subsidiary or branch.'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                // Tenant registration section
                Forms\Components\Select::make('users')
                    ->label('Tenant Users')
                    ->relationship('users', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->helperText('Select users who should be registered as tenants of this company. These users will have access to this company as tenants.')
                    ->afterStateUpdated(function ($state, $set) {
                        // This ensures the tenant relationship is properly handled
                        $set('tenant_users', $state);
                    })
                    ->saveRelationshipsUsing(function (Model $record, $state) {
                        // This ensures the tenant relationship is properly synced
                        $record->users()->sync($state);
                    }),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('parentCompany.name')
                    ->label('Parent Company')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
//                    ->url(fn (Company $record) => route('companies.switch', [
//                        'slug' => $record->slug,
//                        // After switching, send the user to a useful landing page.
//                        // You can change this to any module index, e.g. '/hostels' or '/finance'.
//                        'to' => '/farms',
//                    ]))
                    ->openUrlInNewTab(false),
                EditAction::make(),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
