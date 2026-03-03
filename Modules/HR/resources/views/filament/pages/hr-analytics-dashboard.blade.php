<x-filament-panels::page>

    @assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @endassets

    {{-- ══════════════════════════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-2">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ now()->format('l, F j, Y') }}
                @if($companyName)
                    &mdash;
                    <span class="font-semibold text-primary-600 dark:text-primary-400">{{ $companyName }}</span>
                @else
                    &mdash;
                    <span class="font-semibold text-gray-700 dark:text-gray-300">All Companies (HQ Aggregate View)</span>
                @endif
            </p>
        </div>
        <span class="text-xs text-gray-400 dark:text-gray-500">Refreshed at {{ now()->format('H:i') }}</span>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 1 — WORKFORCE
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="mb-2">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Workforce</h3>
    </div>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 mb-6">

        {{-- Total Employees --}}
        <div class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="absolute inset-x-0 top-0 h-1 rounded-t-xl bg-primary-500"></div>
            <div class="flex items-start gap-3 pt-1">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                    <x-filament::icon icon="heroicon-o-users" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Employees</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $stats['inactive'] }} inactive</p>
                </div>
            </div>
        </div>

        {{-- Active Employees --}}
        <div class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="absolute inset-x-0 top-0 h-1 rounded-t-xl bg-success-500"></div>
            <div class="flex items-start gap-3 pt-1">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400">
                    <x-filament::icon icon="heroicon-o-check-circle" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Active</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active'] }}</p>
                    @if($stats['total'] > 0)
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ round($stats['active'] / $stats['total'] * 100) }}% of workforce</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- On Leave Today --}}
        <div class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="absolute inset-x-0 top-0 h-1 rounded-t-xl bg-warning-500"></div>
            <div class="flex items-start gap-3 pt-1">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400">
                    <x-filament::icon icon="heroicon-o-calendar-days" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">On Leave Today</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['onLeaveToday'] }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $stats['pendingLeave'] }} requests pending</p>
                </div>
            </div>
        </div>

        {{-- Pending Leave --}}
        <div class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="absolute inset-x-0 top-0 h-1 rounded-t-xl bg-amber-500"></div>
            <div class="flex items-start gap-3 pt-1">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <x-filament::icon icon="heroicon-o-clock" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Pending Approvals</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pendingLeave'] }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $stats['approvedThisMonth'] }} approved this month</p>
                </div>
            </div>
        </div>

        {{-- New Hires --}}
        <div class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="absolute inset-x-0 top-0 h-1 rounded-t-xl bg-sky-500"></div>
            <div class="flex items-start gap-3 pt-1">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">
                    <x-filament::icon icon="heroicon-o-user-plus" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">New Hires</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['newHires'] }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">This month</p>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 2 — OPERATIONS KPIs
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="mb-2">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Operations</h3>
    </div>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 mb-6">

        {{-- Last Payroll --}}
        <div class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="absolute inset-x-0 top-0 h-1 rounded-t-xl bg-violet-500"></div>
            <div class="flex items-start gap-3 pt-1">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                    <x-filament::icon icon="heroicon-o-banknotes" class="h-5 w-5" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Last Payroll</p>
                    @if($stats['lastPayroll'])
                        <p class="text-base font-bold text-gray-900 dark:text-white truncate">{{ $stats['lastPayroll']->period_label }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Net: {{ number_format($stats['lastPayroll']->total_net, 0) }}</p>
                    @else
                        <p class="text-base font-bold text-gray-400">None yet</p>
                    @endif
                    @if($stats['draftPayrolls'] > 0)
                        <p class="text-xs text-warning-600 dark:text-warning-400">{{ $stats['draftPayrolls'] }} draft</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Active Loans --}}
        <div class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="absolute inset-x-0 top-0 h-1 rounded-t-xl bg-rose-500"></div>
            <div class="flex items-start gap-3 pt-1">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <x-filament::icon icon="heroicon-o-credit-card" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Active Loans</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['activeLoans'] }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ number_format($stats['totalOutstanding'], 0) }} outstanding</p>
                </div>
            </div>
        </div>

        {{-- Open Vacancies --}}
        <div class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="absolute inset-x-0 top-0 h-1 rounded-t-xl bg-indigo-500"></div>
            <div class="flex items-start gap-3 pt-1">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <x-filament::icon icon="heroicon-o-briefcase" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Open Vacancies</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['openVacancies'] }}</p>
                    @if($stats['closingSoon'] > 0)
                        <p class="text-xs text-warning-600 dark:text-warning-400">{{ $stats['closingSoon'] }} closing soon</p>
                    @else
                        <p class="text-xs text-gray-400 dark:text-gray-500">No closures this week</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Training --}}
        <div class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="absolute inset-x-0 top-0 h-1 rounded-t-xl bg-emerald-500"></div>
            <div class="flex items-start gap-3 pt-1">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <x-filament::icon icon="heroicon-o-academic-cap" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Training Programs</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['ongoingTraining'] }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $stats['plannedTraining'] }} planned &middot; {{ $stats['completedTraining'] }} done this year</p>
                </div>
            </div>
        </div>

        {{-- Open Cases --}}
        <div class="relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="absolute inset-x-0 top-0 h-1 rounded-t-xl {{ ($stats['openDisciplinary'] + $stats['openGrievances']) > 0 ? 'bg-danger-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
            <div class="flex items-start gap-3 pt-1">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400">
                    <x-filament::icon icon="heroicon-o-scale" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Open Cases</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['openDisciplinary'] + $stats['openGrievances'] }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $stats['openDisciplinary'] }} disciplinary &middot; {{ $stats['openGrievances'] }} grievance</p>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 3 — PERFORMANCE & GENDER (mini stats)
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">

        {{-- Avg Performance Rating --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Avg Performance Rating</p>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['avgRating'] > 0 ? $stats['avgRating'] : '—' }}</span>
                @if($stats['avgRating'] > 0)
                    <span class="text-sm text-gray-400 dark:text-gray-500 mb-1">/ 5.0</span>
                @endif
            </div>
            <div class="mt-2 flex gap-0.5">
                @for($i = 1; $i <= 5; $i++)
                    <div class="h-1.5 flex-1 rounded-full {{ $i <= round($stats['avgRating']) ? 'bg-amber-400' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                @endfor
            </div>
        </div>

        {{-- Gender Breakdown --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3">Gender Breakdown (Active)</p>
            @forelse($stats['genderCounts'] as $gender => $count)
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs capitalize text-gray-600 dark:text-gray-400">{{ $gender ?: 'Unknown' }}</span>
                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $count }}</span>
                </div>
                @if($stats['active'] > 0)
                    <div class="mb-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                        <div class="h-full rounded-full {{ $gender === 'male' ? 'bg-sky-500' : ($gender === 'female' ? 'bg-pink-500' : 'bg-gray-400') }}"
                            style="width: {{ round($count / $stats['active'] * 100) }}%"></div>
                    </div>
                @endif
            @empty
                <p class="text-xs text-gray-400">No data</p>
            @endforelse
        </div>

        {{-- Employment Type --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Employment Types</p>
            <div class="h-28 flex items-center justify-center" wire:ignore>
                <canvas id="empTypeChart"></canvas>
            </div>
        </div>

        {{-- Attendance This Week --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Attendance This Week</p>
            <div class="h-28 flex items-center justify-center" wire:ignore>
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 4 — CHARTS ROW
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5 mb-6">

        {{-- Headcount by Department (wider) --}}
        <div class="lg:col-span-3 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Active Headcount by Department</p>
            <div class="h-56" wire:ignore>
                <canvas id="deptChart"></canvas>
            </div>
        </div>

        {{-- Leave Status Last 30 Days --}}
        <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Leave Requests (Last 30 Days)</p>
            <div class="h-56 flex items-center justify-center" wire:ignore>
                <canvas id="leaveStatusChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Payroll Trend --}}
    @if(count($charts['payroll_trend']['labels']) > 0)
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Payroll Trend — Gross vs Net (Last 6 Paid Runs)</p>
        <div class="h-52" wire:ignore>
            <canvas id="payrollChart"></canvas>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 5 — ACTIVITY LISTS
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Recent Leave Requests --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-white/10">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Recent Leave Requests</p>
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    {{ count($recentLeave) }}
                </span>
            </div>
            @if(count($recentLeave))
                <ul class="divide-y divide-gray-50 dark:divide-white/5">
                    @foreach($recentLeave as $leave)
                        <li class="flex items-center justify-between px-5 py-3 text-sm">
                            <div class="min-w-0">
                                <p class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $leave['employee'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $leave['type'] }} &middot; {{ $leave['start_date'] }}–{{ $leave['end_date'] }} ({{ $leave['days'] }}d)</p>
                            </div>
                            @php
                                $badge = match($leave['status']) {
                                    'approved'  => 'bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                                    'pending'   => 'bg-warning-100 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                                    'rejected'  => 'bg-danger-100 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400',
                                    default     => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                };
                            @endphp
                            <span class="ml-3 shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $badge }}">
                                {{ ucfirst($leave['status']) }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="px-5 py-6 text-center text-sm text-gray-400 dark:text-gray-500">No recent leave requests.</p>
            @endif
        </div>

        {{-- Open Disciplinary & Grievance Cases --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-white/10">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Open Cases</p>
                <span class="rounded-full {{ count($openCases) > 0 ? 'bg-danger-100 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }} px-2 py-0.5 text-xs font-medium">
                    {{ count($openCases) }}
                </span>
            </div>
            @if(count($openCases))
                <ul class="divide-y divide-gray-50 dark:divide-white/5">
                    @foreach($openCases as $case)
                        <li class="flex items-center justify-between px-5 py-3 text-sm">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="rounded px-1.5 py-0.5 text-xs font-medium {{ $case['kind'] === 'Disciplinary' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400' }}">
                                        {{ $case['kind'] }}
                                    </span>
                                    <p class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $case['employee'] }}</p>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $case['type'] }} &middot; {{ $case['date'] }}</p>
                            </div>
                            <span class="ml-3 shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $case['color'] === 'danger' ? 'bg-danger-100 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400' : 'bg-warning-100 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400' }}">
                                {{ $case['status'] }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="px-5 py-6 text-center text-sm text-gray-400 dark:text-gray-500">No open cases. All clear.</p>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         CHART.JS INITIALISATION
    ══════════════════════════════════════════════════════════════════════════ --}}
    @script
    <script>
        const isDark = document.documentElement.classList.contains('dark');

        const gridColor   = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
        const labelColor  = isDark ? 'rgba(255,255,255,0.5)'  : 'rgba(0,0,0,0.45)';
        const font        = { family: 'inherit', size: 11 };

        Chart.defaults.font    = font;
        Chart.defaults.color   = labelColor;

        // ── 1. Headcount by Department ──────────────────────────────────────
        const deptCtx = document.getElementById('deptChart');
        if (deptCtx && @json(count($charts['dept_headcount']['data'])) > 0) {
            new Chart(deptCtx, {
                type: 'bar',
                data: {
                    labels:   @json($charts['dept_headcount']['labels']),
                    datasets: [{
                        label:           'Employees',
                        data:            @json($charts['dept_headcount']['data']),
                        backgroundColor: 'rgba(99,102,241,0.75)',
                        borderColor:     'rgba(99,102,241,1)',
                        borderWidth:     1,
                        borderRadius:    4,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: gridColor }, ticks: { maxRotation: 30 } },
                        y: { grid: { color: gridColor }, beginAtZero: true, ticks: { stepSize: 1 } },
                    },
                },
            });
        }

        // ── 2. Payroll Trend ────────────────────────────────────────────────
        const payrollCtx = document.getElementById('payrollChart');
        if (payrollCtx && @json(count($charts['payroll_trend']['labels'])) > 0) {
            new Chart(payrollCtx, {
                type: 'line',
                data: {
                    labels:   @json($charts['payroll_trend']['labels']),
                    datasets: [
                        {
                            label:       'Gross',
                            data:        @json($charts['payroll_trend']['gross']),
                            borderColor: 'rgba(139,92,246,0.9)',
                            backgroundColor: 'rgba(139,92,246,0.1)',
                            fill:        true,
                            tension:     0.35,
                            pointRadius: 3,
                        },
                        {
                            label:       'Net',
                            data:        @json($charts['payroll_trend']['net']),
                            borderColor: 'rgba(34,197,94,0.9)',
                            backgroundColor: 'rgba(34,197,94,0.08)',
                            fill:        true,
                            tension:     0.35,
                            pointRadius: 3,
                        },
                        {
                            label:       'Deductions',
                            data:        @json($charts['payroll_trend']['deductions']),
                            borderColor: 'rgba(239,68,68,0.8)',
                            backgroundColor: 'transparent',
                            borderDash:  [4,3],
                            tension:     0.35,
                            pointRadius: 3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { boxWidth: 12 } } },
                    scales: {
                        x: { grid: { color: gridColor } },
                        y: { grid: { color: gridColor }, beginAtZero: false },
                    },
                },
            });
        }

        // ── 3. Attendance Doughnut ──────────────────────────────────────────
        const attCtx = document.getElementById('attendanceChart');
        if (attCtx && @json(count($charts['attendance']['data'])) > 0) {
            new Chart(attCtx, {
                type: 'doughnut',
                data: {
                    labels:   @json($charts['attendance']['labels']),
                    datasets: [{
                        data:            @json($charts['attendance']['data']),
                        backgroundColor: [
                            'rgba(34,197,94,0.8)',
                            'rgba(239,68,68,0.8)',
                            'rgba(245,158,11,0.8)',
                            'rgba(14,165,233,0.8)',
                        ],
                        borderWidth: 2,
                        borderColor: isDark ? '#1f2937' : '#ffffff',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: { legend: { position: 'right', labels: { boxWidth: 10, padding: 8 } } },
                },
            });
        }

        // ── 4. Leave Status Doughnut ────────────────────────────────────────
        const leaveCtx = document.getElementById('leaveStatusChart');
        if (leaveCtx && @json(count($charts['leave_status']['data'])) > 0) {
            new Chart(leaveCtx, {
                type: 'doughnut',
                data: {
                    labels:   @json($charts['leave_status']['labels']),
                    datasets: [{
                        data:            @json($charts['leave_status']['data']),
                        backgroundColor: [
                            'rgba(245,158,11,0.8)',
                            'rgba(34,197,94,0.8)',
                            'rgba(239,68,68,0.8)',
                            'rgba(156,163,175,0.8)',
                        ],
                        borderWidth: 2,
                        borderColor: isDark ? '#1f2937' : '#ffffff',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '58%',
                    plugins: { legend: { position: 'right', labels: { boxWidth: 10, padding: 8 } } },
                },
            });
        }

        // ── 5. Employment Type Doughnut ─────────────────────────────────────
        const empTypeCtx = document.getElementById('empTypeChart');
        if (empTypeCtx && @json(count($charts['emp_type']['data'])) > 0) {
            new Chart(empTypeCtx, {
                type: 'doughnut',
                data: {
                    labels:   @json($charts['emp_type']['labels']),
                    datasets: [{
                        data:            @json($charts['emp_type']['data']),
                        backgroundColor: [
                            'rgba(99,102,241,0.8)',
                            'rgba(14,165,233,0.8)',
                            'rgba(168,85,247,0.8)',
                            'rgba(34,197,94,0.8)',
                            'rgba(245,158,11,0.8)',
                        ],
                        borderWidth: 2,
                        borderColor: isDark ? '#1f2937' : '#ffffff',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: { legend: { display: false } },
                },
            });
        }
    </script>
    @endscript

</x-filament-panels::page>