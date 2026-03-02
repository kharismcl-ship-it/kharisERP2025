<?php

namespace App\Models\Scopes;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Automatically scopes queries to the active Filament tenant (company).
 *
 * When the active tenant is an HQ/group company it expands the scope to
 * include all subsidiary company IDs so the group can see every
 * subsidiary's records in one view.
 *
 * The scope is a no-op in the admin panel (no tenant), in CLI contexts,
 * and in queue workers — those contexts always see all data.
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenant = $this->resolveActiveTenant();

        if (! $tenant instanceof Company) {
            return;
        }

        $table  = $model->getTable();
        $column = $table . '.company_id';

        $ids = $tenant->selfAndDescendantIds();

        if (count($ids) === 1) {
            $builder->where($column, $ids[0]);
        } else {
            $builder->whereIn($column, $ids);
        }
    }

    private function resolveActiveTenant(): mixed
    {
        // Must be an HTTP request handled by Filament
        if (app()->runningInConsole()) {
            return null;
        }

        try {
            if (! app()->bound(\Filament\Panel::class) && ! class_exists(\Filament\Facades\Filament::class)) {
                return null;
            }

            return \Filament\Facades\Filament::getTenant();
        } catch (\Throwable) {
            return null;
        }
    }
}