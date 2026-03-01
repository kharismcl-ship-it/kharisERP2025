<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\FarmPlot;

class FarmPlotPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_farm_plot'); }
    public function view(User $user, FarmPlot $r): bool { return $user->can('view_farm_plot'); }
    public function create(User $user): bool    { return $user->can('create_farm_plot'); }
    public function update(User $user, FarmPlot $r): bool { return $user->can('update_farm_plot'); }
    public function delete(User $user, FarmPlot $r): bool { return $user->can('delete_farm_plot'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_farm_plot'); }
}
