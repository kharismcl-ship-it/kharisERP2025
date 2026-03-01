<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\KitchenTicket;

class KitchenTicketPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_kitchen_ticket'); }
    public function view(User $user, KitchenTicket $model): bool  { return $user->can('view_kitchen_ticket'); }
    public function create(User $user): bool   { return $user->can('create_kitchen_ticket'); }
    public function update(User $user, KitchenTicket $model): bool { return $user->can('update_kitchen_ticket'); }
    public function delete(User $user, KitchenTicket $model): bool { return $user->can('delete_kitchen_ticket'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_kitchen_ticket'); }
}