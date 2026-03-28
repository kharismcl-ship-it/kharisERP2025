<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Modules\Farms\Models\FarmInputVoucher;
use Modules\Farms\Models\FarmInputCreditAccount;
use Modules\Farms\Models\Farm;
use Filament\Facades\Filament;

class FarmInputVoucherResource extends Resource
{
    protected static ?string $model = FarmInputVoucher::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static string|\UnitEnum|null $navigationGroup = 'Cooperatives';
    protected static ?string $navigationLabel = 'Input Vouchers';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        $companyId = Filament::getTenant()?->id;

        return $schema->components([
            Section::make('Voucher Details')->schema([
                Grid::make(2)->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', $companyId)->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    Select::make('farm_input_credit_account_id')
                        ->label('Credit Account')
                        ->options(fn () => FarmInputCreditAccount::where('company_id', $companyId)
                            ->pluck('account_ref', 'id'))
                        ->nullable()
                        ->searchable(),
                    Select::make('voucher_type')
                        ->options([
                            'seed'       => 'Seed',
                            'fertilizer' => 'Fertilizer',
                            'chemical'   => 'Chemical',
                            'equipment'  => 'Equipment',
                            'general'    => 'General',
                        ])
                        ->default('general')
                        ->required(),
                    TextInput::make('input_item')
                        ->label('Input Item Description')
                        ->maxLength(255),
                    TextInput::make('beneficiary_name')
                        ->maxLength(255),
                    TextInput::make('beneficiary_phone')
                        ->tel()
                        ->maxLength(30),
                    TextInput::make('face_value')
                        ->numeric()
                        ->prefix('GHS')
                        ->required(),
                    DatePicker::make('issued_date')->required(),
                    DatePicker::make('expiry_date'),
                    Select::make('status')
                        ->options([
                            'issued'             => 'Issued',
                            'partially_redeemed' => 'Partially Redeemed',
                            'redeemed'           => 'Redeemed',
                            'expired'            => 'Expired',
                            'cancelled'          => 'Cancelled',
                        ])
                        ->default('issued'),
                    TextInput::make('verification_pin')
                        ->label('Verification PIN')
                        ->disabled()
                        ->helperText('Auto-generated on creation'),
                ]),
                Textarea::make('notes')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('voucher_code')->searchable()->sortable(),
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('beneficiary_name')->searchable()->placeholder('—'),
                TextColumn::make('voucher_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'seed'       => 'success',
                        'fertilizer' => 'warning',
                        'chemical'   => 'danger',
                        'equipment'  => 'info',
                        default      => 'gray',
                    }),
                TextColumn::make('face_value')->money('GHS'),
                TextColumn::make('redeemed_value')->money('GHS')->label('Redeemed'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'issued'             => 'info',
                        'partially_redeemed' => 'warning',
                        'redeemed'           => 'success',
                        'expired'            => 'danger',
                        'cancelled'          => 'gray',
                        default              => 'gray',
                    }),
                TextColumn::make('expiry_date')->date()->placeholder('—'),
            ])
            ->actions([
                Action::make('mark_redeemed')
                    ->label('Mark Redeemed')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record): bool => in_array($record->status, ['issued', 'partially_redeemed']))
                    ->form([
                        TextInput::make('redeemed_value')
                            ->label('Redeemed Value (GHS)')
                            ->numeric()
                            ->required(),
                        TextInput::make('redeemed_at_supplier')
                            ->label('Supplier Name')
                            ->maxLength(255),
                        TextInput::make('pin_verify')
                            ->label('Verification PIN')
                            ->maxLength(6)
                            ->required()
                            ->helperText('Enter the 6-digit PIN to confirm redemption'),
                    ])
                    ->action(function ($record, array $data): void {
                        if ($data['pin_verify'] !== $record->verification_pin) {
                            \Filament\Notifications\Notification::make()
                                ->title('Invalid PIN')
                                ->body('The verification PIN does not match.')
                                ->danger()
                                ->send();
                            return;
                        }
                        $redeemedValue = (float) $data['redeemed_value'];
                        $newStatus = $redeemedValue >= $record->face_value ? 'redeemed' : 'partially_redeemed';
                        $record->update([
                            'redeemed_value'      => $redeemedValue,
                            'redeemed_at_supplier' => $data['redeemed_at_supplier'] ?? null,
                            'status'              => $newStatus,
                            'redeemed_at'         => now(),
                        ]);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\Farms\Filament\Resources\FarmInputVoucherResource\Pages\ListFarmInputVouchers::route('/'),
            'create' => \Modules\Farms\Filament\Resources\FarmInputVoucherResource\Pages\CreateFarmInputVoucher::route('/create'),
            'edit'   => \Modules\Farms\Filament\Resources\FarmInputVoucherResource\Pages\EditFarmInputVoucher::route('/{record}/edit'),
        ];
    }
}