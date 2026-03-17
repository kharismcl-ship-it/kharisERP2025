<?php

namespace Modules\HR\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\HR\Models\Announcement;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveBalance;
use Modules\HR\Models\LeaveRequest;

class StaffDashboard extends Page
{
    protected string $view = 'hr::filament.pages.staff-dashboard';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    public ?Employee $employee = null;

    public array $leaveBalances   = [];
    public array $pendingLeaves   = [];
    public array $announcements   = [];
    public array $farmPortals     = [];
    public array $hostelPortals   = [];

    public function mount(): void
    {
        $companyId      = Filament::getTenant()?->id;
        $this->employee = Employee::with(['department', 'jobPosition'])
            ->where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $this->employee) {
            return;
        }

        $this->leaveBalances = LeaveBalance::where('employee_id', $this->employee->id)
            ->with('leaveType')
            ->get()
            ->toArray();

        $this->pendingLeaves = LeaveRequest::where('employee_id', $this->employee->id)
            ->where('status', 'pending')
            ->with('leaveType')
            ->latest()
            ->take(5)
            ->get()
            ->toArray();

        $this->announcements = Announcement::where('company_id', $companyId)
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('published_at')
            ->take(5)
            ->get()
            ->toArray();

        // Portal links — only loaded when user has the relevant permission
        if (auth()->user()->can('access_staff_farms') && class_exists(\Modules\Farms\Models\Farm::class)) {
            $this->farmPortals = \Modules\Farms\Models\Farm::where('company_id', $companyId)
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
                ->toArray();
        }

        if (auth()->user()->can('access_staff_hostels') && class_exists(\Modules\Hostels\Models\Hostel::class)) {
            $this->hostelPortals = \Modules\Hostels\Models\Hostel::where('company_id', $companyId)
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
                ->toArray();
        }
    }
}
