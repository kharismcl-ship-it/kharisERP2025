<?php
namespace Modules\Finance\Filament\Resources\BankAccountResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\BankAccountResource;
class EditBankAccount extends EditRecord {
    protected static string $resource = BankAccountResource::class;
    protected function getHeaderActions(): array { return [ViewAction::make(), DeleteAction::make()]; }
}
