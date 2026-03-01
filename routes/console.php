<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Modules\Core\Console\Commands\ProcessAutomations;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Register automation command
Artisan::command('automations:process', function () {
    $this->call(ProcessAutomations::class);
})->purpose('Process all scheduled automations');

// Debug: list roles for a given user (with company context)
Artisan::command('debug:user-roles {userId}', function (int $userId) {
    $userClass = \App\Models\User::class;
    $tableNames = config('permission.table_names');
    $teamKey = config('permission.column_names.team_foreign_key', 'company_id');

    $roles = DB::table($tableNames['model_has_roles'].' as mhr')
        ->join($tableNames['roles'].' as r', 'r.id', '=', 'mhr.role_id')
        ->leftJoin('companies as c', 'c.id', '=', DB::raw('mhr.'.$teamKey))
        ->where('mhr.model_type', $userClass)
        ->where('mhr.model_id', $userId)
        ->selectRaw('r.name as role, mhr.'.$teamKey.' as company_id, c.name as company_name')
        ->get();

    if ($roles->isEmpty()) {
        $this->info("No roles assigned to user {$userId}.");

        return;
    }

    foreach ($roles as $row) {
        $companyId = $row->company_id ?? 'null';
        $companyName = $row->company_name ?? 'null';
        $this->line("role={$row->role}; company_id={$companyId}; company={$companyName}");
    }
})->purpose('List roles assigned to a user with company context');
