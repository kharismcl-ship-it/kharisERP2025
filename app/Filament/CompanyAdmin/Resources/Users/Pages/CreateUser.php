<?php

namespace App\Filament\CompanyAdmin\Resources\Users\Pages;

use App\Filament\CompanyAdmin\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
