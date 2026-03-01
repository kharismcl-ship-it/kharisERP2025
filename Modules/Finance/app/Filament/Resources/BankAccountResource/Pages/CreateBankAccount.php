<?php
namespace Modules\Finance\Filament\Resources\BankAccountResource\Pages;
use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\BankAccountResource;
class CreateBankAccount extends CreateRecord {
    protected static string $resource = BankAccountResource::class;
}
