<?php
namespace Modules\Finance\Filament\Resources\BankAccountResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\BankAccountResource;
class ListBankAccounts extends ListRecords {
    protected static string $resource = BankAccountResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
