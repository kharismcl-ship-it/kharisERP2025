<?php

namespace Modules\Core\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Core\Filament\Resources\UserResource;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
}
