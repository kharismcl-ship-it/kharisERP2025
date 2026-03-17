<?php

namespace Modules\Requisition\Filament\Resources\Staff;

use Filament\Resources\Resource;

abstract class StaffGatedResource extends Resource
{
    // All CRUD authorization (canAccess → canViewAny, canCreate, canEdit, canDelete)
    // is handled by each model's Policy, which delegates to Filament Shield toggles.
    // No overrides here — Filament's default canAccess() calls canViewAny() → Policy → Shield.
}
