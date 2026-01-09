<?php

namespace Modules\Hostels\Filament\Resources;

use App\Enums\Countries;
use App\Enums\GhanaRegions;
use BackedEnum;
use Dotswan\MapPicker\Fields\Map;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Hostels\Filament\Resources\HostelResource\Pages;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\BedsRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\BlocksRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\BookingsRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\FeeTypesRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\FloorsRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\HostelChargesRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\HostelOccupantsRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\HostelStaffAssignmentsRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\IncidentsRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\MaintenanceRequestsRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\RoomsRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\VisitorLogsRelationManager;
use Modules\Hostels\Filament\Resources\HostelResource\RelationManagers\WhatsAppGroupsRelationManager;
use Modules\Hostels\Models\Hostel;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class HostelResource extends Resource
{
    protected static ?string $model = Hostel::class;

    protected static ?string $slug = 'hostels';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Hostel Information')
                    ->description('Hostel profile and facility information')
                    ->collapsible(true)
                    ->schema([
                        Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->readOnly()
                            ->required()
                            ->unique(Hostel::class, 'slug', ignoreRecord: true),
                        TextInput::make('code')
                            ->label('Hostel Code')
                            ->required(),

                        FileUpload::make('photo')
                            ->image()
                            ->disk('public')
                            ->directory('hostels/photos')
                            ->visibility('public')
                            ->nullable()
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->nullable()
                            ->columnSpanFull(),

                        TimePicker::make('check_in_time_default')
                            ->label('Default Check-in Time')
                            ->nullable(),

                        TimePicker::make('check_out_time_default')
                            ->label('Default Check-out Time')
                            ->nullable(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->nullable()
                            ->columnSpanFull(),

                        TextInput::make('capacity')
                            ->numeric()
                            ->required(),

                        Select::make('gender_policy')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'mixed' => 'Mixed',
                            ])
                            ->required(),

                    ])
                    ->columns(2),

                Section::make('Hostel Location')
                    ->description('Hostel location information')
                    ->collapsible(true)
                    ->schema([

                        Select::make('country')
                            ->label('Country')
                            ->searchable()
                            ->options(Countries::options())
                            ->default(Countries::GHANA)
                            ->preload()
                            ->required(),

                        Select::make('region')
                            ->label('Region')
                            ->options(GhanaRegions::options())
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('city')
                            ->label('City')
                            ->required()
                            ->columnSpanFull(),

                        Map::make('location')
                            ->label('Location')
                            ->columnSpanFull()
                            // Basic Configuration
                            ->defaultLocation(latitude: 5.6037, longitude: -0.1870)
                            ->draggable(true)
                            ->clickable(true) // click to move marker
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
                                $set('latitude', $state['lat']);
                                $set('longitude', $state['lng']);
                            })
                            ->afterStateHydrated(function ($state, $record, Set $set): void {
                                $set('location', [
                                    'lat' => $record->latitude ?? null,
                                    'lng' => $record->longitude ?? null,
                                ]);
                            }),

                        TextInput::make('latitude')
                            ->hiddenLabel()
                            ->hidden(),

                        TextInput::make('longitude')
                            ->hiddenLabel()
                            ->hidden(),

                    ])
                    ->columns(2),

                Section::make('Hostel Contact')
                    ->description('Hostel contact information')
                    ->collapsible(true)
                    ->schema([

                        PhoneInput::make('contact_phone')
                            ->defaultCountry('GH')
                            ->required()
                            ->nullable(),

                        TextInput::make('contact_email')
                            ->label('Email')
                            ->email(),

                        TextInput::make('contact_name')
                            ->label('Contact Name')
                            ->required()
                            ->columnSpanFull(),

                    ])->columns(2),

                Section::make('Hostel Status')
                    ->description('Hostel status information')
                    ->collapsible(true)
                    ->schema([

                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->required()
                            ->default('active'),
                    ]),

                Section::make('Deposit Configuration')
                    ->description('Deposit and payment policy settings')
                    ->collapsible(true)
                    ->schema([
                        Select::make('require_deposit')
                            ->label('Require Deposit')
                            ->options([
                                true => 'Yes',
                                false => 'No',
                            ])
                            ->default(false)
                            ->reactive(),

                        Select::make('deposit_type')
                            ->label('Deposit Type')
                            ->options([
                                'fixed' => 'Fixed Amount',
                                'percentage' => 'Percentage of Total',
                            ])
                            ->default('fixed')
                            ->visible(fn (callable $get) => $get('require_deposit')),

                        TextInput::make('deposit_amount')
                            ->label('Deposit Amount (GHS)')
                            ->numeric()
                            ->minValue(0)
                            ->visible(fn (callable $get) => $get('require_deposit') && $get('deposit_type') === 'fixed'),

                        TextInput::make('deposit_percentage')
                            ->label('Deposit Percentage (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->visible(fn (callable $get) => $get('require_deposit') && $get('deposit_type') === 'percentage'),

                        Select::make('allow_partial_payments')
                            ->label('Allow Partial Payments')
                            ->options([
                                true => 'Yes',
                                false => 'No',
                            ])
                            ->default(false)
                            ->reactive(),

                        TextInput::make('partial_payment_min_percentage')
                            ->label('Minimum Partial Payment (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->visible(fn (callable $get) => $get('allow_partial_payments')),

                        Select::make('require_payment_before_checkin')
                            ->label('Require Payment Before Check-in')
                            ->options([
                                true => 'Yes',
                                false => 'No',
                            ])
                            ->default(false)
                            ->reactive(),

                        TextInput::make('reservation_hold_minutes')
                            ->label('Reservation Hold Time (minutes)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1440)
                            ->helperText('How long to hold a reservation before automatic cancellation')
                            ->nullable(),

                        Textarea::make('deposit_refund_policy')
                            ->label('Deposit Refund Policy')
                            ->placeholder('Describe the deposit refund policy for this hostel')
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Hostel Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('country')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('region')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('city')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('capacity')
                    ->sortable(),

                TextColumn::make('gender_policy')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('require_payment_before_checkin')
                    ->label('Pre-payment Required')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->sortable(),

                TextColumn::make('rooms_count')
                    ->label('Rooms')
                    ->counts('rooms')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BlocksRelationManager::class,
            FloorsRelationManager::class,
            RoomsRelationManager::class,
            BedsRelationManager::class,
            BookingsRelationManager::class,
            HostelOccupantsRelationManager::class,
            MaintenanceRequestsRelationManager::class,
            IncidentsRelationManager::class,
            HostelStaffAssignmentsRelationManager::class,
            HostelChargesRelationManager::class,
            FeeTypesRelationManager::class,
            VisitorLogsRelationManager::class,
            WhatsAppGroupsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostels::route('/'),
            'create' => Pages\CreateHostel::route('/create'),
            'view' => Pages\ViewHostel::route('/{record}'),
            'edit' => Pages\EditHostel::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['company']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'company.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->company) {
            $details['Company'] = $record->company->name;
        }

        return $details;
    }
}
