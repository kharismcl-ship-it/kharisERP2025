<?php

use Illuminate\Support\Facades\Route;
use Modules\Farms\Http\Livewire\Attendance\Index as AttendanceIndex;
use Modules\Farms\Http\Livewire\Crops\Index as CropsIndex;
use Modules\Farms\Http\Livewire\Crops\RecordHarvest;
use Modules\Farms\Http\Livewire\Crops\Show as CropShow;
use Modules\Farms\Http\Livewire\DailyReports\Create as DailyReportsCreate;
use Modules\Farms\Http\Livewire\DailyReports\Index as DailyReportsIndex;
use Modules\Farms\Http\Livewire\DailyReports\Show as DailyReportsShow;
use Modules\Farms\Http\Livewire\FarmDashboard;
use Modules\Farms\Http\Livewire\FarmIndex;
use Modules\Farms\Http\Livewire\FarmMap;
use Modules\Farms\Http\Livewire\Livestock\Index as LivestockIndex;
use Modules\Farms\Http\Livewire\Livestock\Show as LivestockShow;
use Modules\Farms\Http\Livewire\Reports\Index as ReportsIndex;
use Modules\Farms\Http\Livewire\Requests\Create as RequestsCreate;
use Modules\Farms\Http\Livewire\Requests\Index as RequestsIndex;
use Modules\Farms\Http\Livewire\Requests\Show as RequestsShow;
use Modules\Farms\Http\Livewire\Tasks\Index as TasksIndex;

Route::middleware(['web', 'auth', 'set-company:farms'])
    ->prefix('farms')
    ->name('farms.')
    ->group(function () {
        Route::get('/', FarmIndex::class)->name('index');

        Route::prefix('{farm:slug}')->group(function () {
            Route::get('/', FarmDashboard::class)->name('dashboard');

            // Tasks
            Route::get('/tasks', TasksIndex::class)->name('tasks.index');

            // Daily Reports
            Route::get('/daily-reports', DailyReportsIndex::class)->name('daily-reports.index');
            Route::get('/daily-reports/create', DailyReportsCreate::class)->name('daily-reports.create');
            Route::get('/daily-reports/{report}', DailyReportsShow::class)->name('daily-reports.show');

            // Crops
            Route::get('/crops', CropsIndex::class)->name('crops.index');
            Route::get('/crops/{cropCycle}', CropShow::class)->name('crops.show');
            Route::get('/crops/{cropCycle}/harvest', RecordHarvest::class)->name('crops.harvest');

            // Livestock
            Route::get('/livestock', LivestockIndex::class)->name('livestock.index');
            Route::get('/livestock/{batch}', LivestockShow::class)->name('livestock.show');

            // Requests
            Route::get('/requests', RequestsIndex::class)->name('requests.index');
            Route::get('/requests/create', RequestsCreate::class)->name('requests.create');
            Route::get('/requests/{request}', RequestsShow::class)->name('requests.show');

            // Attendance
            Route::get('/attendance', AttendanceIndex::class)->name('attendance.index');

            // Map
            Route::get('/map', FarmMap::class)->name('map');

            // Reports
            Route::get('/reports', ReportsIndex::class)->name('reports.index');
        });
    });
