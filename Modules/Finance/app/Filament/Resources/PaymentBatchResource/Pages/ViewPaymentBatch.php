<?php

namespace Modules\Finance\Filament\Resources\PaymentBatchResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\PaymentBatchResource;

class ViewPaymentBatch extends ViewRecord
{
    protected static string $resource = PaymentBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Batch Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('batch_number')->weight('bold'),
                        TextEntry::make('status')->badge()
                            ->color(fn (string $state) => match ($state) {
                                'draft'     => 'gray',
                                'approved'  => 'info',
                                'processed' => 'success',
                                default     => 'gray',
                            }),
                        TextEntry::make('batch_date')->date(),
                        TextEntry::make('payment_method')->badge()->color('info'),
                        TextEntry::make('total_amount')->money('GHS')->weight('bold'),
                        TextEntry::make('bankAccount.name')->label('Bank Account')->placeholder('—'),
                        TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
                    ]),
            ]);
    }
}