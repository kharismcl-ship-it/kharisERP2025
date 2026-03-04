<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Modules\ClientService\Filament\Resources\CsVisitorResource;

class CreateCsVisitor extends CreateRecord
{
    protected static string $resource = CsVisitorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['checked_in_by_user_id'] = Auth::id();

        return $data;
    }
}
