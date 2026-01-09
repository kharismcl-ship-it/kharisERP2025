<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Livewire\Attendance\Index as AttendanceIndex;
use Modules\HR\Http\Livewire\Employees\Index as EmployeesIndex;
use Modules\HR\Http\Livewire\Employees\Show as EmployeesShow;
use Modules\HR\Http\Livewire\EmployeeSalaries\Index as EmployeeSalariesIndex;
use Modules\HR\Http\Livewire\EmployeeSalaries\Show as EmployeeSalariesShow;
use Modules\HR\Http\Livewire\EmploymentContracts\Index as EmploymentContractsIndex;
use Modules\HR\Http\Livewire\EmploymentContracts\Show as EmploymentContractsShow;
use Modules\HR\Http\Livewire\HRDashboard;
use Modules\HR\Http\Livewire\Leaves\Index as LeavesIndex;
use Modules\HR\Http\Livewire\Leaves\Requests as LeavesRequests;
use Modules\HR\Http\Livewire\OrgChart\Index as OrgChartIndex;
use Modules\HR\Http\Livewire\PerformanceCycles\Index as PerformanceCyclesIndex;
use Modules\HR\Http\Livewire\PerformanceCycles\Show as PerformanceCyclesShow;
use Modules\HR\Http\Livewire\PerformanceReviews\Index as PerformanceReviewsIndex;
use Modules\HR\Http\Livewire\PerformanceReviews\Show as PerformanceReviewsShow;
use Modules\HR\Http\Livewire\SalaryScales\Index as SalaryScalesIndex;
use Modules\HR\Http\Livewire\SalaryScales\Show as SalaryScalesShow;

Route::middleware(['web', 'auth', 'set-company:hr'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {
        Route::get('/', HRDashboard::class)->name('index');
        Route::get('/employees', EmployeesIndex::class)->name('employees.index');
        Route::get('/employees/{employee}', EmployeesShow::class)->name('employees.show');
        Route::get('/attendance', AttendanceIndex::class)->name('attendance.index');
        Route::get('/leaves', LeavesIndex::class)->name('leaves.index');
        Route::get('/leave-requests', LeavesRequests::class)->name('leaves.requests');
        Route::get('/org-chart', OrgChartIndex::class)->name('org-chart.index');

        // New routes for additional HR modules
        Route::get('/employee-salaries', EmployeeSalariesIndex::class)->name('employee-salaries.index');
        Route::get('/employee-salaries/{salary}', EmployeeSalariesShow::class)->name('employee-salaries.show');
        Route::get('/employment-contracts', EmploymentContractsIndex::class)->name('employment-contracts.index');
        Route::get('/employment-contracts/{contract}', EmploymentContractsShow::class)->name('employment-contracts.show');
        Route::get('/performance-cycles', PerformanceCyclesIndex::class)->name('performance-cycles.index');
        Route::get('/performance-cycles/{cycle}', PerformanceCyclesShow::class)->name('performance-cycles.show');
        Route::get('/performance-reviews', PerformanceReviewsIndex::class)->name('performance-reviews.index');
        Route::get('/performance-reviews/{review}', PerformanceReviewsShow::class)->name('performance-reviews.show');
        Route::get('/salary-scales', SalaryScalesIndex::class)->name('salary-scales.index');
        Route::get('/salary-scales/{scale}', SalaryScalesShow::class)->name('salary-scales.show');
    });
