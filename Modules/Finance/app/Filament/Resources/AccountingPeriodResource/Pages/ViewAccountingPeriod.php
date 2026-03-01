<?php

namespace Modules\Finance\Filament\Resources\AccountingPeriodResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\AccountingPeriodResource;
use Modules\Finance\Models\AccountingPeriod;

class ViewAccountingPeriod extends ViewRecord
{
    protected static string $resource = AccountingPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Period Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')->weight('bold')->columnSpanFull(),
                        TextEntry::make('start_date')->date(),
                        TextEntry::make('end_date')->date(),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => AccountingPeriod::STATUSES[$state] ?? $state)
                            ->color(fn (string $state) => match ($state) {
                                'open'    => 'success',
                                'closing' => 'warning',
                                'closed'  => 'gray',
                                default   => 'gray',
                            }),
                        TextEntry::make('company.name')->label('Company'),
                    ]),

                Section::make('Closing Info')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('closedBy.name')->label('Closed By')->placeholder('Not yet closed'),
                        TextEntry::make('closed_at')->dateTime()->label('Closed At')->placeholder('—'),
                        TextEntry::make('notes')->columnSpanFull()->placeholder('None'),
                    ]),

                Section::make('Audit')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')->dateTime()->label('Created'),
                        TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                    ]),
            ]);
    }
}
