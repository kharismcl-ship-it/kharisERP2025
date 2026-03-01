<?php

namespace Modules\Construction\Policies;

use App\Models\User;
use Modules\Construction\Models\Contractor;

class ContractorPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_contractor'); }
    public function view(User $user, Contractor $r): bool { return $user->can('view_contractor'); }
    public function create(User $user): bool   { return $user->can('create_contractor'); }
    public function update(User $user, Contractor $r): bool { return $user->can('update_contractor'); }
    public function delete(User $user, Contractor $r): bool { return $user->can('delete_contractor'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_contractor'); }
}
