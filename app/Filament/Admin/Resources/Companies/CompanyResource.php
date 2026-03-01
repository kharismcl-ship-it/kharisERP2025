<?php

namespace App\Filament\Admin\Resources\Companies;

use App\Enums\Countries;
use App\Models\Company;
use Dotswan\MapPicker\Fields\Map;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

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
                Section::make('Company Details')
                    ->description('Add company details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', str($state)->slug()) : null)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->readOnly()
                            ->required()
//                    ->alphaDash()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->dehydrated(true),
                        Forms\Components\Select::make('type')
                            ->options([
                                'main' => 'Main Company',
                                'subsidiary' => 'Subsidiary',

                            ])
                            ->live(onBlur: true)
                            ->required(),

                        Forms\Components\Select::make('parent_company_id')
                            ->label('Select Mother Company')
                            ->relationship('parentCompany', 'name')
                            ->searchable()
                            ->preload()
                            ->live(onBlur: true)
                            ->required(fn (Get $get) => $get('type') === 'subsidiary')
                            ->visible(fn (Get $get) => $get('type') === 'subsidiary')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('company_service_type')
                            ->label('Company Primary Service Type')
                            ->options([
                                'general' => 'General Business',
                                'hostel' => 'Hostel',
                                'farm' => 'Farm',
                                'manufacturing' => 'Manufacturing',
                            ])
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('company_service_description')
                            ->label('Company Service Description')
                            ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('company_logo')
                            ->label('Company Logo')
                            ->image()
                            ->disk('public')
                            ->directory('company-logos')
                            ->maxSize(1024 * 10) // 10MB
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),

                Section::make('Company Location Details')
                    ->description('Add company location details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_country')
                            ->label('Country')
                            ->options(Countries::options())
                            ->required(),
                        Forms\Components\TextInput::make('company_city')
                            ->label('City')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_address')
                            ->label('Company Address')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Map::make('company_location')
                            ->label('Location')
                            ->columnSpanFull()
                            // Basic Configuration
                            ->defaultLocation(latitude: 5.6037, longitude: -0.1870)
                            ->draggable(true) // drag to move marker
                            ->zoom(15)
                            ->minZoom(0)
                            ->maxZoom(28)
                            ->tilesUrl('https://tile.openstreetmap.de/{z}/{x}/{y}.png')
                            ->detectRetina(true)

                            // Marker Configuration
                            ->showMarker(true)
                            ->markerColor('#3b82f6')
                            ->markerIconAnchor([18, 36])

                            // Controls
                            ->showFullscreenControl(true)
                            ->showZoomControl(true)

                            // Location Features
                            ->showMyLocationButton(true)

                            // State Management
                            ->afterStateUpdated(function (Set $set, ?array $state): void {
                                $set('company_latitude', $state['lat']);
                                $set('company_longitude', $state['lng']);
                            })
                            ->afterStateHydrated(function ($state, $record, Set $set): void {
                                $set('location', [
                                    'lat' => $record->company_latitude ?? null,
                                    'lng' => $record->company_longitude ?? null,
                                ]);
                            }),

                        Forms\Components\TextInput::make('company_latitude')
                            ->readOnly(),

                        Forms\Components\TextInput::make('company_longitude')
                            ->readOnly(),

                        Forms\Components\TextInput::make('company_ghanapostgps')
                            ->label('Ghana Post GPS')
                            ->maxLength(255),

                        PhoneInput::make('company_phone')
                            ->label('Company Phone')
                            ->required(),
                        Forms\Components\TextInput::make('company_email')
                            ->label('Company Email')
                            ->prefixIcon('heroicon-m-envelope')
                            ->email()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('company_website')
                            ->label('Company Website')
                            ->prefix('https://')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('slug')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'main' => 'info',
                        'subsidiary' => 'success',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('company_service_type')
                    ->label('Service Type')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state).' Services'),
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
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('open')
                        ->label('Open')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('primary')
//                        ->url(fn (Company $record) => route('companies.switch', [
//                            'slug' => $record->slug,
//                            // After switching, send the user to a useful landing page.
//                        // You can change this to any module index, e.g. '/hostels' or '/finance'.
//                        'to' => '/farms',
//                    ]))
                        ->openUrlInNewTab(false),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
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
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
