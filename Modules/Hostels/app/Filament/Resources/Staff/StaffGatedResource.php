<?php

namespace Modules\Hostels\Filament\Resources\Staff;

use Filament\Resources\Resource;

abstract class StaffGatedResource extends Resource
{
    // All CRUD authorization handled by Policy → Shield.
    // No overrides here — Filament's default canAccess() calls canViewAny() → Policy → Shield.
}
