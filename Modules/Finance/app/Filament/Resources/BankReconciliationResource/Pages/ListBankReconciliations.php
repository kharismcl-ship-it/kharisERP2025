<?php
namespace Modules\Finance\Filament\Resources\BankReconciliationResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\BankReconciliationResource;
class ListBankReconciliations extends ListRecords {
    protected static string $resource = BankReconciliationResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
