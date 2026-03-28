<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\Currency;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurrencyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Currency');
    }

    public function view(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('View:Currency');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Currency');
    }

    public function update(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('Update:Currency');
    }

    public function delete(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('Delete:Currency');
    }

    public function restore(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('Restore:Currency');
    }

    public function forceDelete(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('ForceDelete:Currency');
    }
}