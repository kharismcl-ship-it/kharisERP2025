<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmGrowerPaymentResource\Pages;
use Modules\Farms\Models\FarmGrowerPayment;

class FarmGrowerPaymentResource extends Resource
{
    protected static ?string $model = FarmGrowerPayment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Cooperatives';

    protected static ?string $navigationLabel = 'Grower Payments';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payment Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_cooperative_id')
                        ->label('Cooperative (optional)')
                        ->relationship('cooperative', 'name')
                        ->searchable()
                        ->nullable(),

                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('payment_type')
                        ->options([
                            'produce_purchase' => 'Produce Purchase',
                            'input_advance'    => 'Input Advance',
                            'input_recovery'   => 'Input Recovery',
                            'bonus'            => 'Bonus',
                            'other'            => 'Other',
                        ])
                        ->required(),

                    Select::make('harvest_record_id')
                        ->label('Harvest Record (optional)')
                        ->relationship('harvestRecord', 'id')
                        ->searchable()
                        ->nullable(),
                ]),

            Section::make('Quantity & Amount')
                ->columns(2)
                ->schema([
                    TextInput::make('quantity_kg')->label('Quantity (kg)')->numeric()->step(0.01)->nullable(),
                    TextInput::make('price_per_kg')->label('Price per kg (GHS)')->numeric()->step(0.0001)->prefix('GHS')->nullable(),
                    TextInput::make('gross_amount')->label('Gross Amount (GHS)')->numeric()->step(0.01)->prefix('GHS')->required(),
                    TextInput::make('net_amount')->label('Net Amount (GHS)')->numeric()->step(0.01)->prefix('GHS')->required(),
                ]),

            Section::make('Payment Method')
                ->columns(2)
                ->schema([
                    Select::make('payment_method')
                        ->options([
                            'cash'           => 'Cash',
                            'mobile_money'   => 'Mobile Money',
                            'bank_transfer'  => 'Bank Transfer',
                        ])
                        ->default('mobile_money')
                        ->live()
                        ->required(),

                    TextInput::make('momo_number')
                        ->label('MoMo Number')
                        ->tel()
                        ->maxLength(20)
                        ->visible(fn (Get $get): bool => $get('payment_method') === 'mobile_money'),

                    DatePicker::make('payment_date')->required()->default(today()),

                    Select::make('status')
                        ->options([
                            'pending'  => 'Pending',
                            'paid'     => 'Paid',
                            'reversed' => 'Reversed',
                        ])
                        ->default('pending')
                        ->required(),
                ]),

            Section::make('Notes')
                ->schema([
                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payment_ref')->label('Ref')->searchable()->copyable(),
                TextColumn::make('farm.name')->label('Farm')->sortable()->searchable(),
                TextColumn::make('cooperative.name')->label('Cooperative')->placeholder('—')->toggleable(),
                TextColumn::make('payment_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
                TextColumn::make('gross_amount')->money('GHS')->label('Gross')->sortable(),
                TextColumn::make('net_amount')->money('GHS')->label('Net')->sortable(),
                TextColumn::make('payment_method')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'mobile_money'  => 'MoMo',
                        'bank_transfer' => 'Bank',
                        default         => ucfirst($state),
                    }),
                TextColumn::make('payment_date')->date()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'     => 'success',
                        'pending'  => 'warning',
                        'reversed' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('status')->options(['pending' => 'Pending', 'paid' => 'Paid', 'reversed' => 'Reversed']),
            ])
            ->actions([
                Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (FarmGrowerPayment $record): bool => $record->status === 'pending')
                    ->action(fn (FarmGrowerPayment $record) => $record->update(['status' => 'paid', 'payment_date' => today()])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('payment_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmGrowerPayments::route('/'),
            'create' => Pages\CreateFarmGrowerPayment::route('/create'),
            'edit'   => Pages\EditFarmGrowerPayment::route('/{record}/edit'),
        ];
    }
}