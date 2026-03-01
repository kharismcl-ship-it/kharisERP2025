<?php

namespace Modules\ManufacturingWater\Policies;

use App\Models\User;
use Modules\ManufacturingWater\Models\MwChemicalUsage;

class MwChemicalUsagePolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_mw::chemical::usage'); }
    public function view(User $user, MwChemicalUsage $model): bool { return $user->can('view_mw::chemical::usage'); }
    public function create(User $user): bool    { return $user->can('create_mw::chemical::usage'); }
    public function update(User $user, MwChemicalUsage $model): bool { return $user->can('update_mw::chemical::usage'); }
    public function delete(User $user, MwChemicalUsage $model): bool { return $user->can('delete_mw::chemical::usage'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_mw::chemical::usage'); }
}