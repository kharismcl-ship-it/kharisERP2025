<?php

namespace Modules\Farms\Filament\Resources\FarmB2bAccountResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmB2bAccountResource;
use Modules\Farms\Models\ShopCustomer;

class EditFarmB2bAccount extends EditRecord
{
    protected static string $resource = FarmB2bAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function afterSave(): void
    {
        // Sync is_b2b flag on linked customers when status changes
        $record = $this->record;
        ShopCustomer::where('b2b_account_id', $record->id)
            ->update(['is_b2b' => $record->status === 'approved']);
    }
}
