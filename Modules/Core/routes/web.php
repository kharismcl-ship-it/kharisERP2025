<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\AutomationController;
use Modules\Core\Http\Controllers\CoreController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('cores', CoreController::class)->names('core');

    // Automation monitoring routes
    Route::prefix('automations')->name('automations.')->group(function () {
        Route::get('/', [AutomationController::class, 'index'])->name('index');
        Route::get('{automation_setting}', [AutomationController::class, 'show'])->name('show');
        Route::get('logs', [AutomationController::class, 'logs'])->name('logs');
        Route::post('{automation_setting}/run', [AutomationController::class, 'run'])->name('run');
        Route::post('{automation_setting}/toggle', [AutomationController::class, 'toggle'])->name('toggle');
    });
});
