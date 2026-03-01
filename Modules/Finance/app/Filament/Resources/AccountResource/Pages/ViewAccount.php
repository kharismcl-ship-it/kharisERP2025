<?php

namespace Modules\Finance\Filament\Resources\AccountResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\AccountResource;

class ViewAccount extends ViewRecord
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('code')->weight('bold'),
                        TextEntry::make('name'),
                        TextEntry::make('type')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'asset'     => 'info',
                                'liability' => 'warning',
                                'equity'    => 'success',
                                'income'    => 'success',
                                'expense'   => 'danger',
                                default     => 'gray',
                            }),
                        TextEntry::make('company.name')->label('Company'),
                    ]),

                Section::make('Chart Hierarchy')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('parent.name')
                            ->label('Parent Account')
                            ->placeholder('Top-level account'),
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
