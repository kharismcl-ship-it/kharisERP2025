<?php

namespace Modules\ClientService\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Modules\ClientService\Filament\Resources\CsVisitorResource\Pages;
use Modules\ClientService\Models\CsVisitor;
use Modules\ClientService\Models\CsVisitorBadge;
use Modules\ClientService\Models\CsVisitorProfile;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class CsVisitorResource extends Resource
{
    protected static ?string $model = CsVisitor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Client Services';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Visitor Log';

    /**
     * Shared wizard steps used by both the admin resource form and the
     * public kiosk Livewire component.
     *
     * @param  bool  $kiosk  When true, read-only/hidden fields are applied for public kiosk context.
     * @return Step[]
     */
    public static function wizardSteps(bool $kiosk = false): array
    {
        return [
            // ── Step 1: Visitor Type ──────────────────────────────
            Step::make('Visitor Type')
                ->schema([
                    Radio::make('visitor_type')
                        ->label('Visitor Type')
                        ->options(['new' => 'New Visitor', 'returning' => 'Returning Visitor'])
                        ->default('new')
                        ->required()
                        ->dehydrated(false)
                        ->live(),

                    TextInput::make('search_phone')
                        ->label('Enter Phone Number to Look Up')
                        ->tel()
                        ->dehydrated(false)
                        ->live(debounce: 500)
                        ->visible(fn (Get $get) => $get('visitor_type') === 'returning'),

                    Placeholder::make('visitor_lookup_hint')
                        ->label('')
                        ->content(function (Get $get) {
                            if ($get('visitor_type') !== 'returning' || blank($get('search_phone'))) {
                                return new HtmlString('');
                            }
                            $companyId = $get('company_id');
                            $query     = CsVisitorProfile::query()->where('phone', $get('search_phone'));
                            if ($companyId) {
                                $query->withoutGlobalScopes()->where('company_id', $companyId);
                            }
                            $profile = $query->first();
                            if ($profile) {
                                return new HtmlString('<p class="text-sm font-medium text-success-600">Welcome back, ' . e($profile->full_name) . '! Details pre-filled — please confirm and sign.</p>');
                            }
                            return new HtmlString('<p class="text-sm font-medium text-warning-600">No profile found. You will be registered as a new visitor.</p>');
                        }),
                ])
                ->afterValidation(function (Get $get, Set $set) {
                    if ($get('visitor_type') !== 'returning') {
                        return;
                    }
                    $phone = $get('search_phone');
                    if (blank($phone)) {
                        return;
                    }
                    $companyId = $get('company_id');
                    $query     = CsVisitorProfile::query()->where('phone', $phone);
                    if ($companyId) {
                        $query->withoutGlobalScopes()->where('company_id', $companyId);
                    }
                    $profile = $query->first();
                    if (! $profile) {
                        return;
                    }
                    $set('full_name',    $profile->full_name);
                    $set('phone',        $profile->phone);
                    $set('email',        $profile->email);
                    $set('id_type',      $profile->id_type);
                    $set('id_number',    $profile->id_number);
                    $set('organization', $profile->organization);
                    // Prefill photo & signature from stored profile
                    if ($profile->photo_path) {
                        $set('photo_path', $profile->photo_path);
                    }
                    if ($profile->check_in_signature) {
                        $set('check_in_signature', $profile->check_in_signature);
                    }
                    $set('communication_opt_in', (bool) $profile->communication_opt_in);
                }),

            // ── Step 2: Personal Details ──────────────────────────
            Step::make('Personal Details')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('full_name')->required()->maxLength(255),
                        TextInput::make('phone')->tel()->nullable(),
                        TextInput::make('email')->email()->nullable(),
                    ]),
                    Grid::make(3)->schema([
                        Select::make('id_type')
                            ->options(CsVisitor::ID_TYPES)
                            ->nullable(),
                        TextInput::make('id_number')->nullable(),
                        TextInput::make('organization')->nullable(),
                    ]),
                    FileUpload::make('photo_path')
                        ->label('Photo')
                        ->directory('visitor-photos')
                        ->image()
                        ->nullable()
                        ->columnSpanFull(),
                    SignaturePad::make('check_in_signature')
                        ->label('Visitor Signature')
                        ->clearAction(fn (Action $action) => $action->button())
                        ->downloadAction(fn (Action $action) => $action->color('primary'))
                        ->undoAction(fn (Action $action) => $action->icon('heroicon-o-pencil'))
                        ->doneAction(fn (Action $action) => $action->iconButton()->icon('heroicon-o-thumbs-up')),
                    Toggle::make('communication_opt_in')
                        ->label('Receive a thank-you message when I leave')
                        ->helperText('We will send you one message via SMS or email upon departure. You can opt out at any time.')
                        ->default(false)
                        ->columnSpanFull(),
                ]),

            // ── Step 3: Visit Details ─────────────────────────────
            Step::make('Visit Details')
                ->schema([
                    Select::make('company_id')
                        ->label('Hosting Company')
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ->disabled($kiosk)   // kiosk sets this from URL; admin can choose
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        Select::make('host_employee_id')
                            ->label('Host Employee')
                            ->relationship('hostEmployee', 'full_name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ]),

                    // Badge select — available badges for the company
                    Select::make('badge_number')
                        ->label('Assign Badge')
                        ->options(function (Get $get) {
                            $companyId = $get('company_id');
                            $query     = CsVisitorBadge::available();
                            if ($companyId) {
                                $query->where('company_id', $companyId);
                            }
                            return $query->orderBy('badge_code')
                                ->pluck('badge_code', 'badge_code');
                        })
                        ->searchable()
                        ->nullable()
                        ->live()
                        ->hidden($kiosk)  // kiosk auto-assigns; admin picks manually
                        ->placeholder('Auto-assigned on kiosk'),

                    Grid::make(2)->schema([
                        Textarea::make('items_brought')->rows(2)->nullable(),
                        Textarea::make('notes')->rows(2)->nullable(),
                    ]),

                    Textarea::make('purpose_of_visit')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    // ── Group Members ─────────────────────────────
                    Section::make('Group Members')
                        ->description('Visiting as a group? Add each member below — leave empty for solo visits.')
                        ->collapsed()
                        ->schema([
                            Repeater::make('group_members')
                                ->label('')
                                ->schema([
                                    TextInput::make('full_name')
                                        ->label('Full Name')
                                        ->required(),
                                    TextInput::make('phone')
                                        ->label('Phone')
                                        ->tel(),
                                    TextInput::make('email')
                                        ->label('Email')
                                        ->email(),
                                    Toggle::make('communication_opt_in')
                                        ->label('Receive departure message?')
                                        ->default(false),
                                ])
                                ->columns(2)
                                ->addActionLabel('Add Group Member')
                                ->defaultItems(0)
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),
                ]),

            // ── Step 4: Check In ──────────────────────────────────
            Step::make('Check In')
                ->schema([
                    Grid::make(2)->schema([
                        DateTimePicker::make('check_in_at')
                            ->required()
                            ->default(now())
                            ->disabled($kiosk)   // kiosk: set automatically, not editable
                            ->dehydrated(true),  // still submit even when disabled

                        DateTimePicker::make('check_out_at')
                            ->nullable()
                            ->hidden($kiosk),    // hidden on kiosk — staff do this from admin
                    ]),
                ]),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make(static::wizardSteps(kiosk: false))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->searchable()->sortable(),
                TextColumn::make('organization')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone'),
                TextColumn::make('hostEmployee.full_name')->label('Host'),
                TextColumn::make('check_in_at')->dateTime()->sortable(),
                TextColumn::make('check_out_at')
                    ->label('Check Out')
                    ->dateTime()
                    ->formatStateUsing(fn ($state) => $state ? $state : 'Still In'),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->state(fn (CsVisitor $record) => $record->duration ?? '—'),
                TextColumn::make('is_checked_out')
                    ->label('Status')
                    ->badge()
                    ->state(fn (CsVisitor $record) => $record->is_checked_out ? 'Out' : 'In')
                    ->color(fn ($state) => $state === 'Out' ? 'success' : 'warning'),
                TextColumn::make('badge_number')
                    ->label('Badge')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),
                TextColumn::make('checkedInBy.name')
                    ->label('Checked In By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('check_in_at', 'desc')
            ->filters([
                SelectFilter::make('host_employee_id')
                    ->label('Host Employee')
                    ->relationship('hostEmployee', 'full_name'),
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name'),
                Filter::make('still_in')
                    ->label('Still In (Not Checked Out)')
                    ->query(fn ($query) => $query->whereNull('check_out_at')),
                Filter::make('today')
                    ->label("Today's Visitors")
                    ->query(fn ($query) => $query->whereDate('check_in_at', today())),
            ])
            ->actions([
                Action::make('check_out')
                    ->label('Check Out')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->visible(fn (CsVisitor $record) => ! $record->is_checked_out)
                    ->action(function (CsVisitor $record): void {
                        $record->update([
                            'check_out_at'           => now(),
                            'checked_out_by_user_id' => Auth::id(),
                        ]);
                        CsVisitorBadge::where('issued_to_visitor_id', $record->id)
                            ->where('status', 'issued')
                            ->first()
                            ?->revokeFromVisitor('Checked out by staff via admin panel.');
                        // Also check out any group members still inside
                        $record->groupMembers()
                            ->whereNull('check_out_at')
                            ->each(function (CsVisitor $member) {
                                $member->update([
                                    'check_out_at'           => now(),
                                    'checked_out_by_user_id' => Auth::id(),
                                ]);
                                CsVisitorBadge::where('issued_to_visitor_id', $member->id)
                                    ->where('status', 'issued')
                                    ->first()
                                    ?->revokeFromVisitor('Group lead checked out.');
                            });
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCsVisitors::route('/'),
            'create' => Pages\CreateCsVisitor::route('/create'),
            'view'   => Pages\ViewCsVisitor::route('/{record}'),
            'edit'   => Pages\EditCsVisitor::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'phone', 'email'];
    }
}
