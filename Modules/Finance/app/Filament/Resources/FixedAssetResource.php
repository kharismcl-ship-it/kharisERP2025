<?php

namespace Modules\Finance\Filament\Resources;

use EduardoRibeiroDev\FilamentLeaflet\Fields\MapPicker;
use EduardoRibeiroDev\FilamentLeaflet\Infolists\MapEntry;
use EduardoRibeiroDev\FilamentLeaflet\Tables\MapColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\FixedAssetResource\Pages;
use Modules\Finance\Filament\Resources\FixedAssetResource\RelationManagers;
use Modules\Finance\Models\FixedAsset;

class FixedAssetResource extends Resource
{
    protected static ?string $model = FixedAsset::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 49;

    protected static ?string $navigationLabel = 'Fixed Assets';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['asset_code', 'name', 'serial_number', 'location'];
    }

    // ── FORM ──────────────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Asset Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->preload()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('asset_code')
                            ->maxLength(50)
                            ->placeholder('e.g. AST-0001')
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('serial_number')
                            ->maxLength(100),

                        Forms\Components\Select::make('status')
                            ->options(FixedAsset::STATUSES)
                            ->default('active')
                            ->required(),

                        Forms\Components\Select::make('custodian_employee_id')
                            ->label('Custodian (Responsible Employee)')
                            ->relationship('custodian', 'full_name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),

                        FileUpload::make('photo')
                            ->label('Asset Photo')
                            ->image()
                            ->directory('fixed-assets/photos')
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ]),

                Section::make('Financial Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('acquisition_date')->required(),
                        Forms\Components\DatePicker::make('depreciation_start_date')->required(),
                        Forms\Components\TextInput::make('cost')
                            ->required()
                            ->numeric()
                            ->prefix('GHS'),
                        Forms\Components\TextInput::make('residual_value')
                            ->numeric()
                            ->prefix('GHS')
                            ->default(0),
                        Forms\Components\TextInput::make('accumulated_depreciation')
                            ->numeric()
                            ->prefix('GHS')
                            ->default(0)
                            ->label('Accumulated Depreciation'),
                        Forms\Components\TextInput::make('disposal_amount')
                            ->numeric()
                            ->prefix('GHS')
                            ->label('Disposal Amount')
                            ->visible(fn ($get) => in_array($get('status'), ['disposed', 'written_off'])),
                        Forms\Components\DatePicker::make('disposal_date')
                            ->label('Disposal Date')
                            ->visible(fn ($get) => in_array($get('status'), ['disposed', 'written_off'])),
                    ]),

                Section::make('Warranty')
                    ->icon('heroicon-o-shield-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('warranty_expiry_date')
                            ->label('Warranty Expiry Date')
                            ->nullable(),
                        Forms\Components\TextInput::make('warranty_vendor')
                            ->label('Warranty Vendor / Supplier')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('warranty_reference')
                            ->label('Warranty Certificate / Reference No.')
                            ->maxLength(100)
                            ->nullable(),
                    ]),

                Section::make('Insurance')
                    ->icon('heroicon-o-document-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('insurance_policy_number')
                            ->label('Policy Number')
                            ->maxLength(100)
                            ->nullable(),
                        Forms\Components\TextInput::make('insurance_provider')
                            ->label('Insurance Provider')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('insurance_value')
                            ->label('Insured Value (GHS)')
                            ->numeric()
                            ->prefix('GHS')
                            ->nullable(),
                        Forms\Components\DatePicker::make('insurance_expiry_date')
                            ->label('Policy Expiry Date')
                            ->nullable(),
                    ]),

                Section::make('Location & Map')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('location')
                            ->label('Address / Location Description')
                            ->maxLength(255)
                            ->placeholder('e.g. Head Office, 2nd Floor, Block A')
                            ->columnSpanFull(),

                        MapPicker::make('map')
                            ->label('Pin Location')
                            ->latitudeFieldName('latitude')
                            ->longitudeFieldName('longitude')
                            ->center(5.6037, -0.1870)
                            ->height(400)
                            ->zoom(12)
                            ->fullscreenControl()
                            ->searchControl()
                            ->scaleControl()
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->numeric()
                                    ->readOnly()
                                    ->label('Latitude')
                                    ->placeholder('Auto-filled by map pin'),
                                Forms\Components\TextInput::make('longitude')
                                    ->numeric()
                                    ->readOnly()
                                    ->label('Longitude')
                                    ->placeholder('Auto-filled by map pin'),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    // ── INFOLIST ──────────────────────────────────────────────────────────────

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Asset Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('asset_code')->weight('bold')->label('Asset Code'),
                    TextEntry::make('name')->weight('bold'),
                    TextEntry::make('category.name')->label('Category'),
                    TextEntry::make('serial_number')->label('Serial No.')->placeholder('—'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state) => match ($state) {
                            'active'      => 'success',
                            'disposed'    => 'gray',
                            'written_off' => 'danger',
                            default       => 'gray',
                        }),
                    TextEntry::make('custodian.full_name')
                        ->label('Custodian')
                        ->icon('heroicon-o-user')
                        ->placeholder('—'),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),

                    ImageEntry::make('photo')
                        ->label('Asset Photo')
                        ->disk('public')
                        ->height(200)
                        ->columnSpanFull()
                        ->hidden(fn ($record) => ! $record->photo),
                ]),

            Section::make('Financial Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('acquisition_date')->date()->label('Acquired On'),
                    TextEntry::make('depreciation_start_date')->date()->label('Depreciation Start'),
                    TextEntry::make('disposal_date')->date()->label('Disposal Date')->placeholder('—'),
                    TextEntry::make('cost')->money('GHS'),
                    TextEntry::make('residual_value')->money('GHS')->label('Residual Value'),
                    TextEntry::make('accumulated_depreciation')->money('GHS')->label('Accum. Depreciation'),
                    TextEntry::make('net_book_value')
                        ->label('Net Book Value')
                        ->money('GHS')
                        ->weight('bold')
                        ->color('primary')
                        ->state(fn ($record) => $record->netBookValue()),
                    TextEntry::make('disposal_amount')->money('GHS')->label('Disposal Amount')->placeholder('—'),
                ]),

            Section::make('Warranty')
                ->icon('heroicon-o-shield-check')
                ->collapsible()
                ->columns(3)
                ->schema([
                    TextEntry::make('warranty_expiry_date')->date()->label('Warranty Expires')->placeholder('—'),
                    TextEntry::make('warranty_vendor')->label('Warranty Vendor')->placeholder('—'),
                    TextEntry::make('warranty_reference')->label('Certificate / Ref.')->placeholder('—'),
                ]),

            Section::make('Insurance')
                ->icon('heroicon-o-document-check')
                ->collapsible()
                ->columns(3)
                ->schema([
                    TextEntry::make('insurance_policy_number')->label('Policy Number')->placeholder('—'),
                    TextEntry::make('insurance_provider')->label('Insurance Provider')->placeholder('—'),
                    TextEntry::make('insurance_value')->money('GHS')->label('Insured Value')->placeholder('—'),
                    TextEntry::make('insurance_expiry_date')->date()->label('Policy Expires')->placeholder('—'),
                ]),

            Section::make('Location & Map')
                ->icon('heroicon-o-map-pin')
                ->collapsible()
                ->columns(2)
                ->schema([
                    TextEntry::make('location')->label('Address')->placeholder('—')->columnSpanFull(),

                    MapEntry::make('map')
                        ->label('Asset Location')
                        ->latitudeFieldName('latitude')
                        ->longitudeFieldName('longitude')
                        ->center(5.6037, -0.1870)
                        ->height(400)
                        ->zoom(14)
                        ->static()
                        ->fullscreenControl()
                        ->scaleControl()
                        ->columnSpanFull(),

                    TextEntry::make('latitude')->placeholder('—'),
                    TextEntry::make('longitude')->placeholder('—'),
                ]),
        ]);
    }

    // ── TABLE ─────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->label('Category'),
                Tables\Columns\TextColumn::make('custodian.full_name')
                    ->label('Custodian')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cost')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_book_value')
                    ->label('Net Book Value')
                    ->money('GHS')
                    ->state(fn ($record) => $record->netBookValue()),
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active'      => 'success',
                        'disposed'    => 'gray',
                        'written_off' => 'danger',
                        default       => 'gray',
                    }),
                Tables\Columns\TextColumn::make('warranty_expiry_date')
                    ->date()
                    ->label('Warranty Exp.')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('insurance_expiry_date')
                    ->date()
                    ->label('Insurance Exp.')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                MapColumn::make('latitude')
                    ->label('Map Preview')
                    ->latitudeFieldName('latitude')
                    ->longitudeFieldName('longitude')
                    ->height(80)
                    ->zoom(13)
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('acquisition_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(FixedAsset::STATUSES),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                Tables\Filters\SelectFilter::make('custodian_employee_id')
                    ->relationship('custodian', 'full_name')
                    ->label('Custodian'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    // ── RELATIONS ─────────────────────────────────────────────────────────────

    public static function getRelations(): array
    {
        return [
            RelationManagers\DepreciationRunsRelationManager::class,
            RelationManagers\MaintenanceRecordsRelationManager::class,
            RelationManagers\TransfersRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    // ── PAGES ─────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFixedAssets::route('/'),
            'create' => Pages\CreateFixedAsset::route('/create'),
            'view'   => Pages\ViewFixedAsset::route('/{record}'),
            'edit'   => Pages\EditFixedAsset::route('/{record}/edit'),
        ];
    }
}