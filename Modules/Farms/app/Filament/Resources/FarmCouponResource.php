<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Clusters\FarmMarketplaceCluster;
use Modules\Farms\Filament\Resources\FarmCouponResource\Pages;
use Modules\Farms\Models\FarmCoupon;

class FarmCouponResource extends Resource
{
    protected static ?string $model = FarmCoupon::class;

    protected static ?string $cluster = FarmMarketplaceCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Coupons';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Coupon Details')
                ->columns(3)
                ->schema([
                    TextInput::make('code')
                        ->label('Coupon Code')
                        ->required()
                        ->maxLength(50)
                        ->hint('Uppercase recommended')
                        ->dehydrateStateUsing(fn ($state) => strtoupper(trim($state))),

                    Select::make('type')
                        ->options([
                            'percentage' => 'Percentage (%)',
                            'fixed'      => 'Fixed Amount (GHS)',
                        ])
                        ->required()
                        ->default('fixed')
                        ->live(),

                    TextInput::make('discount_value')
                        ->label(fn ($get) => $get('type') === 'percentage' ? 'Discount (%)' : 'Discount (GHS)')
                        ->numeric()
                        ->step(0.01)
                        ->required(),

                    TextInput::make('min_order_amount')
                        ->label('Min Order Amount (GHS)')
                        ->numeric()
                        ->step(0.01)
                        ->nullable()
                        ->prefix('GHS')
                        ->helperText('Leave blank for no minimum'),

                    TextInput::make('max_uses')
                        ->label('Max Uses')
                        ->numeric()
                        ->nullable()
                        ->helperText('Leave blank for unlimited'),

                    Textarea::make('description')
                        ->rows(2)
                        ->nullable()
                        ->columnSpanFull(),
                ]),

            Section::make('Validity')
                ->columns(3)
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    DatePicker::make('valid_from')
                        ->label('Valid From')
                        ->nullable(),

                    DatePicker::make('valid_to')
                        ->label('Valid To')
                        ->nullable()
                        ->after('valid_from'),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Coupon Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('code')->badge()->color('success'),
                    TextEntry::make('type')->formatStateUsing(fn ($s) => $s === 'percentage' ? 'Percentage' : 'Fixed'),
                    TextEntry::make('discount_value')
                        ->label('Discount')
                        ->formatStateUsing(fn ($state, $record) => $record->type === 'percentage'
                            ? "{$state}%"
                            : "GHS {$state}"),
                    TextEntry::make('min_order_amount')->money('GHS')->placeholder('None'),
                    TextEntry::make('max_uses')->placeholder('Unlimited'),
                    TextEntry::make('uses_count')->label('Times Used'),
                    TextEntry::make('is_active')->label('Status')
                        ->badge()
                        ->formatStateUsing(fn ($s) => $s ? 'Active' : 'Inactive')
                        ->color(fn ($s) => $s ? 'success' : 'gray'),
                    TextEntry::make('valid_from')->date()->placeholder('No limit'),
                    TextEntry::make('valid_to')->date()->placeholder('No limit'),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->badge()->color('success')->searchable(),
                TextColumn::make('type')->formatStateUsing(fn ($s) => $s === 'percentage' ? '%' : 'GHS'),
                TextColumn::make('discount_value')->label('Discount')
                    ->formatStateUsing(fn ($state, $record) => $record->type === 'percentage'
                        ? "{$state}% off"
                        : "GHS {$state} off"),
                TextColumn::make('min_order_amount')->money('GHS')->label('Min Order')->placeholder('—'),
                TextColumn::make('uses_count')->label('Used'),
                TextColumn::make('max_uses')->label('Limit')->placeholder('∞'),
                TextColumn::make('is_active')->label('Active')
                    ->badge()
                    ->formatStateUsing(fn ($s) => $s ? 'Active' : 'Inactive')
                    ->color(fn ($s) => $s ? 'success' : 'gray'),
                TextColumn::make('valid_to')->date()->label('Expires')->placeholder('—')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')->options(['percentage' => 'Percentage', 'fixed' => 'Fixed']),
                SelectFilter::make('is_active')->options([1 => 'Active', 0 => 'Inactive'])->label('Status'),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmCoupons::route('/'),
            'create' => Pages\CreateFarmCoupon::route('/create'),
            'view'   => Pages\ViewFarmCoupon::route('/{record}'),
            'edit'   => Pages\EditFarmCoupon::route('/{record}/edit'),
        ];
    }
}
