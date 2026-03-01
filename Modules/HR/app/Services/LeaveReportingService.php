<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveRequest;

class LeaveReportingService
{
    public function generateLeaveReport(array $filters): Collection
    {
        $query = LeaveRequest::with(['employee.department', 'leaveType', 'approver'])
            ->where('company_id', app('current_company_id'));

        // Apply filters
        $this->applyFilters($query, $filters);

        return $query->get()->map(function ($request) {
            return [
                'id' => $request->id,
                'employee_name' => $request->employee->full_name,
                'employee_code' => $request->employee->employee_code,
                'department' => $request->employee->department->name ?? 'N/A',
                'leave_type' => $request->leaveType->name,
                'start_date' => $request->start_date->format('Y-m-d'),
                'end_date' => $request->end_date->format('Y-m-d'),
                'total_days' => $request->total_days,
                'status' => ucfirst($request->status),
                'reason' => $request->reason,
                'approved_by' => $request->approver->full_name ?? 'N/A',
                'approved_at' => $request->approved_at?->format('Y-m-d H:i:s'),
                'created_at' => $request->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function generateLeaveSummaryReport(array $filters): array
    {
        $query = LeaveRequest::with(['employee.department', 'leaveType'])
            ->where('company_id', app('current_company_id'));

        $this->applyFilters($query, $filters);

        $leaveRequests = $query->get();

        // Summary by department
        $departmentSummary = $leaveRequests->groupBy(function ($request) {
            return $request->employee->department->name ?? 'Unassigned';
        })->map(function ($departmentRequests, $departmentName) {
            return [
                'department' => $departmentName,
                'total_requests' => $departmentRequests->count(),
                'approved_requests' => $departmentRequests->where('status', 'approved')->count(),
                'pending_requests' => $departmentRequests->where('status', 'pending')->count(),
                'rejected_requests' => $departmentRequests->where('status', 'rejected')->count(),
                'total_days' => $departmentRequests->where('status', 'approved')->sum('total_days'),
            ];
        })->values();

        // Summary by leave type
        $leaveTypeSummary = $leaveRequests->groupBy('leaveType.name')->map(function ($typeRequests, $typeName) {
            return [
                'leave_type' => $typeName,
                'total_requests' => $typeRequests->count(),
                'approved_requests' => $typeRequests->where('status', 'approved')->count(),
                'pending_requests' => $typeRequests->where('status', 'pending')->count(),
                'rejected_requests' => $typeRequests->where('status', 'rejected')->count(),
                'total_days' => $typeRequests->where('status', 'approved')->sum('total_days'),
            ];
        })->values();

        // Summary by status
        $statusSummary = $leaveRequests->groupBy('status')->map(function ($statusRequests, $status) {
            return [
                'status' => ucfirst($status),
                'count' => $statusRequests->count(),
                'total_days' => $statusRequests->sum('total_days'),
            ];
        })->values();

        // Monthly trend
        $monthlyTrend = $leaveRequests->where('status', 'approved')
            ->groupBy(function ($request) {
                return $request->start_date->format('Y-m');
            })
            ->map(function ($monthRequests, $month) {
                return [
                    'month' => Carbon::parse($month)->format('M Y'),
                    'requests' => $monthRequests->count(),
                    'days' => $monthRequests->sum('total_days'),
                ];
            })->values();

        return [
            'overview' => [
                'total_requests' => $leaveRequests->count(),
                'approved_requests' => $leaveRequests->where('status', 'approved')->count(),
                'pending_requests' => $leaveRequests->where('status', 'pending')->count(),
                'rejected_requests' => $leaveRequests->where('status', 'rejected')->count(),
                'total_days_approved' => $leaveRequests->where('status', 'approved')->sum('total_days'),
                'average_approval_time' => $this->calculateAverageApprovalTime($leaveRequests),
            ],
            'department_summary' => $departmentSummary,
            'leave_type_summary' => $leaveTypeSummary,
            'status_summary' => $statusSummary,
            'monthly_trend' => $monthlyTrend,
        ];
    }

    public function generateEmployeeLeaveReport(int $employeeId, array $filters): array
    {
        $query = LeaveRequest::with(['leaveType', 'approver'])
            ->where('employee_id', $employeeId)
            ->where('company_id', app('current_company_id'));

        $this->applyFilters($query, $filters);

        $leaveRequests = $query->get();

        $employee = Employee::find($employeeId);

        return [
            'employee' => [
                'name' => $employee->full_name,
                'code' => $employee->employee_code,
                'department' => $employee->department->name ?? 'N/A',
                'position' => $employee->jobPosition->title ?? 'N/A',
            ],
            'leave_history' => $leaveRequests->map(function ($request) {
                return [
                    'leave_type' => $request->leaveType->name,
                    'start_date' => $request->start_date->format('Y-m-d'),
                    'end_date' => $request->end_date->format('Y-m-d'),
                    'days' => $request->total_days,
                    'status' => ucfirst($request->status),
                    'reason' => $request->reason,
                    'approved_by' => $request->approver->full_name ?? 'N/A',
                ];
            }),
            'summary' => [
                'total_requests' => $leaveRequests->count(),
                'approved_requests' => $leaveRequests->where('status', 'approved')->count(),
                'pending_requests' => $leaveRequests->where('status', 'pending')->count(),
                'rejected_requests' => $leaveRequests->where('status', 'rejected')->count(),
                'total_days_taken' => $leaveRequests->where('status', 'approved')->sum('total_days'),
                'most_common_leave_type' => $leaveRequests->groupBy('leaveType.name')->count() > 0 ?
                    $leaveRequests->groupBy('leaveType.name')->sortDesc()->keys()->first() : 'N/A',
            ],
        ];
    }

    public function exportToCsv(Collection $data, string $filename): string
    {
        $headers = array_keys($data->first() ?? []);

        $csvContent = implode(',', $headers)."\n";

        foreach ($data as $row) {
            $csvContent .= implode(',', array_map(function ($value) {
                return '"'.str_replace('"', '""', $value).'"';
            }, $row))."\n";
        }

        $filePath = storage_path('app/reports/'.$filename.'_'.now()->format('Ymd_His').'.csv');

        if (! file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        file_put_contents($filePath, $csvContent);

        return $filePath;
    }

    public function exportToExcel(Collection $data, string $filename): string
    {
        // This would typically use a package like PhpSpreadsheet
        // For now, we'll create a simple CSV as Excel-compatible
        return $this->exportToCsv($data, $filename);
    }

    protected function applyFilters(object $query, array $filters): void
    {
        if (! empty($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['leave_type_id'])) {
            $query->where('leave_type_id', $filters['leave_type_id']);
        }

        if (! empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        if (! empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
    }

    protected function calculateAverageApprovalTime(Collection $leaveRequests): ?float
    {
        $approvedRequests = $leaveRequests->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->whereNotNull('created_at');

        if ($approvedRequests->isEmpty()) {
            return null;
        }

        $totalHours = $approvedRequests->sum(function ($request) {
            return $request->created_at->diffInHours($request->approved_at);
        });

        return round($totalHours / $approvedRequests->count(), 2);
    }
}
