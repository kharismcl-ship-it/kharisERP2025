<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayIntentResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\PaymentsChannel\Filament\Resources\PayIntentResource;

class ViewPayIntent extends ViewRecord
{
    protected static string $resource = PayIntentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('reference')
                                    ->label('Reference')
                                    ->weight('bold')
                                    ->size('lg'),
                                Components\TextEntry::make('provider')
                                    ->label('Payment Provider')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'flutterwave' => 'primary',
                                        'paystack' => 'success',
                                        'payswitch' => 'info',
                                        'stripe' => 'gray',
                                        'ghanapay' => 'warning',
                                        'manual' => 'danger',
                                        default => 'gray',
                                    }),
                                Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'gray',
                                        'pending_offline' => 'warning',
                                        'initiated' => 'info',
                                        'processing' => 'info',
                                        'successful' => 'success',
                                        'failed' => 'danger',
                                        'cancelled' => 'danger',
                                        'expired' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),
                        Grid::make(2)
                            ->schema([
                                    Components\TextEntry::make('amount')
                                    ->label('Amount')
                                    ->money('ghs')
                                    ->weight('bold')
                                    ->size('lg'),
                                    Components\TextEntry::make('currency')
                                    ->label('Currency'),
                                ]),
                        Grid::make(2)
                            ->schema([
                                    Components\TextEntry::make('payMethod.name')
                                    ->label('Payment Method')
                                    ->placeholder('Not specified'),
                                    Components\TextEntry::make('provider_reference')
                                    ->label('Provider Reference')
                                    ->placeholder('Not provided'),
                                ]),
                    ]),

                Section::make('Customer Information')
                    ->schema([
                            Grid::make(3)
                                ->schema([
                                    Components\TextEntry::make('customer_name')
                                    ->label('Customer Name')
                                    ->placeholder('Not provided'),
                                    Components\TextEntry::make('customer_email')
                                    ->label('Email')
                                    ->placeholder('Not provided')
                                    ->icon('heroicon-m-envelope'),
                                    Components\TextEntry::make('customer_phone')
                                    ->label('Phone')
                                    ->placeholder('Not provided')
                                    ->icon('heroicon-m-phone'),
                                ]),
                        ])
                    ->collapsible(),

                Section::make('System Information')
                    ->schema([
                            Grid::make(2)
                                ->schema([
                                    Components\TextEntry::make('company.name')
                                    ->label('Company'),
                                    Components\TextEntry::make('payable_type')
                                    ->label('Payable Type')
                                    ->formatStateUsing(fn ($state) => class_basename($state)),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    Components\TextEntry::make('payable_id')
                                    ->label('Payable ID'),
                                    Components\TextEntry::make('description')
                                    ->label('Description')
                                    ->placeholder('No description'),
                                ]),
                        ])
                    ->collapsible(),

                Section::make('Timestamps')
                    ->schema([
                            Grid::make(2)
                                ->schema([
                                    Components\TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime(),
                                    Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),
                                ]),
                            Components\TextEntry::make('expires_at')
                                ->label('Expires At')
                                ->dateTime()
                                ->placeholder('No expiration'),
                        ])
                    ->collapsible(),

                Section::make('Metadata')
                    ->schema([
                            Components\KeyValueEntry::make('metadata')
                                ->label('')
                                ->columnSpanFull(),
                        ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('URLs')
                    ->schema([
                            Grid::make(2)
                                ->schema([
                                    Components\TextEntry::make('return_url')
                                    ->label('Return URL')
                                    ->placeholder('Not set')
                                    ->url(fn ($state) => $state),
                                    Components\TextEntry::make('callback_url')
                                    ->label('Callback URL')
                                    ->placeholder('Not set')
                                    ->url(fn ($state) => $state),
                                ]),
                        ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
