<?php
namespace Modules\Finance\Filament\Resources\BankReconciliationResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\BankReconciliationResource;
class EditBankReconciliation extends EditRecord {
    protected static string $resource = BankReconciliationResource::class;
    protected function getHeaderActions(): array { return [ViewAction::make(), DeleteAction::make()]; }
}
