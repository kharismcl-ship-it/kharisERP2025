<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Clusters\FarmMarketplaceCluster;
use Modules\Farms\Filament\Resources\FarmB2bAccountResource\Pages;
use Modules\Farms\Models\FarmB2bAccount;
use Modules\Farms\Models\ShopCustomer;

class FarmB2bAccountResource extends Resource
{
    protected static ?string $model = FarmB2bAccount::class;

    protected static ?string $cluster = FarmMarketplaceCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 15;

    protected static ?string $navigationLabel = 'B2B Accounts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Business Details')
                ->columns(2)
                ->schema([
                    TextInput::make('business_name')->required()->maxLength(255),
                    Select::make('business_type')
                        ->options(array_combine(
                            FarmB2bAccount::TYPES,
                            array_map('ucfirst', FarmB2bAccount::TYPES)
                        ))
                        ->default('restaurant')
                        ->required(),

                    TextInput::make('contact_name')->required()->maxLength(255),
                    TextInput::make('contact_phone')->required()->maxLength(30),
                    TextInput::make('contact_email')->email()->nullable(),
                    TextInput::make('tax_id')->label('TIN / VAT Number')->nullable(),
                    TextInput::make('ghc_reg')->label('GRC Cert. No.')->nullable(),

                    Textarea::make('business_address')->rows(2)->columnSpanFull(),
                ]),

            Section::make('Wholesale Terms')
                ->columns(3)
                ->schema([
                    Select::make('status')
                        ->options([
                            'pending'  => 'Pending Review',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->default('pending')
                        ->required(),

                    TextInput::make('discount_percent')
                        ->label('Wholesale Discount %')
                        ->numeric()
                        ->step(0.5)
                        ->minValue(0)
                        ->maxValue(50)
                        ->suffix('%')
                        ->default(0),

                    Select::make('payment_terms')
                        ->options([
                            'prepay' => 'Prepay (full payment upfront)',
                            'net7'   => 'Net 7 days',
                            'net14'  => 'Net 14 days',
                            'net30'  => 'Net 30 days',
                        ])
                        ->default('prepay')
                        ->required(),

                    TextInput::make('credit_limit')
                        ->label('Credit Limit (GHS)')
                        ->numeric()
                        ->prefix('GHS')
                        ->nullable()
                        ->placeholder('Leave blank for unlimited'),

                    Textarea::make('rejection_reason')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Reason for rejection (shown to applicant)'),

                    Textarea::make('internal_notes')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Internal notes (not shown to customer)'),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Business')
                ->columns(3)
                ->schema([
                    TextEntry::make('business_name')->weight('bold'),
                    TextEntry::make('business_type')->formatStateUsing(fn ($s) => ucfirst($s))->badge()->color('info'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($s) => match ($s) {
                            'approved' => 'success', 'rejected' => 'danger', default => 'warning',
                        }),
                    TextEntry::make('contact_name'),
                    TextEntry::make('contact_phone'),
                    TextEntry::make('contact_email')->placeholder('—'),
                    TextEntry::make('tax_id')->label('TIN')->placeholder('—'),
                    TextEntry::make('ghc_reg')->label('GRC No.')->placeholder('—'),
                    TextEntry::make('business_address')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Wholesale Terms')
                ->columns(3)
                ->schema([
                    TextEntry::make('discount_percent')->suffix('%')->label('Discount'),
                    TextEntry::make('payment_terms')
                        ->formatStateUsing(fn ($s) => match ($s) {
                            'prepay' => 'Prepay', 'net7' => 'Net 7', 'net14' => 'Net 14', 'net30' => 'Net 30',
                            default => $s,
                        }),
                    TextEntry::make('credit_limit')->money('GHS')->placeholder('Unlimited'),
                    TextEntry::make('credit_used')->money('GHS'),
                    TextEntry::make('approved_at')->dateTime()->placeholder('—'),
                    TextEntry::make('rejection_reason')->placeholder('—')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('business_name')->searchable()->sortable(),
                TextColumn::make('business_type')
                    ->formatStateUsing(fn ($s) => ucfirst($s))
                    ->badge()->color('info'),
                TextColumn::make('contact_name')->toggleable(),
                TextColumn::make('contact_phone'),
                TextColumn::make('discount_percent')->suffix('%')->label('Discount')->alignCenter(),
                TextColumn::make('payment_terms')
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'prepay' => 'Prepay', 'net7' => 'Net 7', 'net14' => 'Net 14', 'net30' => 'Net 30',
                        default => $s,
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($s) => match ($s) {
                        'approved' => 'success', 'rejected' => 'danger', default => 'warning',
                    }),
                TextColumn::make('created_at')->date()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
                SelectFilter::make('business_type')->options(
                    array_combine(FarmB2bAccount::TYPES, array_map('ucfirst', FarmB2bAccount::TYPES))
                ),
            ])
            ->actions([
                ViewAction::make(),
                // Quick Approve action
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (FarmB2bAccount $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (FarmB2bAccount $record): void {
                        $record->update([
                            'status'      => 'approved',
                            'approved_at' => now(),
                            'rejection_reason' => null,
                        ]);
                        // Mark all linked ShopCustomers as is_b2b
                        ShopCustomer::where('b2b_account_id', $record->id)
                            ->update(['is_b2b' => true]);

                        Notification::make()->title('B2B account approved')->success()->send();
                    }),
                // Quick Reject action
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (FarmB2bAccount $record) => $record->status === 'pending')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(function (FarmB2bAccount $record, array $data): void {
                        $record->update([
                            'status'           => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()->title('B2B account rejected')->warning()->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmB2bAccounts::route('/'),
            'create' => Pages\CreateFarmB2bAccount::route('/create'),
            'view'   => Pages\ViewFarmB2bAccount::route('/{record}'),
            'edit'   => Pages\EditFarmB2bAccount::route('/{record}/edit'),
        ];
    }
}
