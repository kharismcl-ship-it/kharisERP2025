<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\FarmWeatherLog;

class FarmWeatherLogPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_farm_weather_log'); }
    public function view(User $user, FarmWeatherLog $record): bool { return $user->can('view_farm_weather_log'); }
    public function create(User $user): bool   { return $user->can('create_farm_weather_log'); }
    public function update(User $user, FarmWeatherLog $record): bool { return $user->can('update_farm_weather_log'); }
    public function delete(User $user, FarmWeatherLog $record): bool { return $user->can('delete_farm_weather_log'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_farm_weather_log'); }
}