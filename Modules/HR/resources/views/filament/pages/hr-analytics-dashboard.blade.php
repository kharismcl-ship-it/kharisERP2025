<x-filament-panels::page>

    {{-- ══════════════════════════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ now()->format('l, F j, Y') }}
            @if($companyName)
                &mdash; <span class="font-semibold text-primary-600 dark:text-primary-400">{{ $companyName }}</span>
            @else
                &mdash; <span class="font-semibold text-gray-700 dark:text-gray-300">All Companies (HQ View)</span>
            @endif
        </p>
        <span class="text-xs text-gray-400">Refreshed {{ now()->format('H:i') }}</span>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         WORKFORCE
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="mb-1">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Workforce</p>
    </div>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-5 mb-6">

        <x-filament::card>
            <div class="text-sm font-medium text-primary-600 dark:text-primary-400">Total Employees</div>
            <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
            <div class="mt-1 text-xs text-gray-400">{{ $stats['inactive'] }} inactive</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-green-600">Active</div>
            <div class="mt-1 text-3xl font-bold text-green-600">{{ $stats['active'] }}</div>
            <div class="mt-1 text-xs text-gray-400">
                @if($stats['total'] > 0){{ round($stats['active'] / $stats['total'] * 100) }}% of workforce@endif
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-amber-600">On Leave Today</div>
            <div class="mt-1 text-3xl font-bold text-amber-600">{{ $stats['onLeaveToday'] }}</div>
            <div class="mt-1 text-xs text-gray-400">{{ $stats['pendingLeave'] }} requests pending</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-orange-500">Pending Approvals</div>
            <div class="mt-1 text-3xl font-bold text-orange-500">{{ $stats['pendingLeave'] }}</div>
            <div class="mt-1 text-xs text-gray-400">{{ $stats['approvedThisMonth'] }} approved this month</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-blue-600">New Hires</div>
            <div class="mt-1 text-3xl font-bold text-blue-600">{{ $stats['newHires'] }}</div>
            <div class="mt-1 text-xs text-gray-400">This month</div>
        </x-filament::card>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         OPERATIONS
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="mb-1">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Operations</p>
    </div>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-5 mb-6">

        <x-filament::card>
            <div class="text-sm font-medium text-violet-600 dark:text-violet-400">Last Payroll</div>
            @if($stats['lastPayroll'])
                <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $stats['lastPayroll']->period_label }}</div>
                <div class="mt-1 text-xs text-gray-400">Net: {{ number_format($stats['lastPayroll']->total_net, 0) }}</div>
            @else
                <div class="mt-1 text-lg font-bold text-gray-400">None yet</div>
            @endif
            @if($stats['draftPayrolls'] > 0)
                <div class="mt-1 text-xs text-amber-600">{{ $stats['draftPayrolls'] }} draft / in-progress</div>
            @endif
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-rose-600">Active Loans</div>
            <div class="mt-1 text-3xl font-bold text-rose-600">{{ $stats['activeLoans'] }}</div>
            <div class="mt-1 text-xs text-gray-400">{{ number_format($stats['totalOutstanding'], 0) }} outstanding</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-indigo-600">Open Vacancies</div>
            <div class="mt-1 text-3xl font-bold text-indigo-600">{{ $stats['openVacancies'] }}</div>
            <div class="mt-1 text-xs {{ $stats['closingSoon'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                {{ $stats['closingSoon'] > 0 ? $stats['closingSoon'].' closing this week' : 'None closing soon' }}
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-emerald-600">Training</div>
            <div class="mt-1 text-3xl font-bold text-emerald-600">{{ $stats['ongoingTraining'] }}</div>
            <div class="mt-1 text-xs text-gray-400">{{ $stats['plannedTraining'] }} planned &middot; {{ $stats['completedTraining'] }} done</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium {{ ($stats['openDisciplinary'] + $stats['openGrievances']) > 0 ? 'text-red-600' : 'text-gray-500 dark:text-gray-400' }}">
                Open Cases
            </div>
            <div class="mt-1 text-3xl font-bold {{ ($stats['openDisciplinary'] + $stats['openGrievances']) > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                {{ $stats['openDisciplinary'] + $stats['openGrievances'] }}
            </div>
            <div class="mt-1 text-xs text-gray-400">{{ $stats['openDisciplinary'] }} disciplinary &middot; {{ $stats['openGrievances'] }} grievance</div>
        </x-filament::card>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         PERFORMANCE + GENDER + MINI CHARTS
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4 mb-6">

        {{-- Avg Performance Rating --}}
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Performance Rating</div>
            <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                {{ $stats['avgRating'] > 0 ? $stats['avgRating'] : '—' }}
                @if($stats['avgRating'] > 0)<span class="text-base font-normal text-gray-400"> / 5.0</span>@endif
            </div>
            <div class="mt-2 flex gap-1">
                @for($i = 1; $i <= 5; $i++)
                    <div style="height:6px; flex:1; border-radius:9999px; background: {{ $i <= round($stats['avgRating']) ? '#f59e0b' : '#e5e7eb' }}"></div>
                @endfor
            </div>
        </x-filament::card>

        {{-- Gender Breakdown --}}
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Gender (Active Staff)</div>
            @forelse($stats['genderCounts'] as $gender => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs capitalize text-gray-600 dark:text-gray-400">{{ $gender ?: 'Unknown' }}</span>
                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $count }}</span>
                </div>
                <div style="height:5px; width:100%; border-radius:9999px; background:#e5e7eb; margin-bottom:8px; overflow:hidden;">
                    <div style="height:100%; border-radius:9999px; width:{{ $stats['active'] > 0 ? round($count / $stats['active'] * 100) : 0 }}%; background: {{ $gender === 'male' ? '#3b82f6' : ($gender === 'female' ? '#ec4899' : '#9ca3af') }}"></div>
                </div>
            @empty
                <p class="text-xs text-gray-400">No gender data</p>
            @endforelse
        </x-filament::card>

        {{-- Employment Type mini chart --}}
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Employment Types</div>
            <div wire:ignore style="position:relative; height:110px;">
                <canvas id="empTypeChart"></canvas>
            </div>
        </x-filament::card>

        {{-- Attendance This Week mini chart --}}
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Attendance This Week</div>
            <div wire:ignore style="position:relative; height:110px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </x-filament::card>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         CHARTS ROW
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-6">

        {{-- Dept Headcount (spans 2 cols) --}}
        <div class="md:col-span-2">
            <x-filament::card>
                <div class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Active Headcount by Department</div>
                <div wire:ignore style="position:relative; height:220px;">
                    <canvas id="deptChart"></canvas>
                </div>
            </x-filament::card>
        </div>

        {{-- Leave Status Doughnut --}}
        <div>
            <x-filament::card>
                <div class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Leave Requests (30 days)</div>
                <div wire:ignore style="position:relative; height:220px;">
                    <canvas id="leaveStatusChart"></canvas>
                </div>
            </x-filament::card>
        </div>

    </div>

    {{-- Payroll Trend --}}
    @if(count($charts['payroll_trend']['labels']) > 0)
    <div class="mb-6">
        <x-filament::card>
            <div class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Payroll Trend — Gross vs Net (Last 6 Paid Runs)</div>
            <div wire:ignore style="position:relative; height:200px;">
                <canvas id="payrollChart"></canvas>
            </div>
        </x-filament::card>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         ACTIVITY LISTS
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        {{-- Recent Leave Requests --}}
        <x-filament::card>
            <div class="flex items-center justify-between mb-3">
                <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">Recent Leave Requests</div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full">{{ count($recentLeave) }}</span>
            </div>
            @if(count($recentLeave))
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="pb-2 text-left text-xs font-medium text-gray-400">Employee</th>
                            <th class="pb-2 text-left text-xs font-medium text-gray-400">Type</th>
                            <th class="pb-2 text-center text-xs font-medium text-gray-400">Days</th>
                            <th class="pb-2 text-right text-xs font-medium text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLeave as $leave)
                            <tr class="border-b border-gray-50 dark:border-gray-800">
                                <td class="py-2 text-xs font-medium text-gray-800 dark:text-gray-200">{{ $leave['employee'] }}</td>
                                <td class="py-2 text-xs text-gray-500">{{ $leave['type'] }}</td>
                                <td class="py-2 text-center text-xs text-gray-500">{{ $leave['days'] }}</td>
                                <td class="py-2 text-right">
                                    @php
                                        $color = match($leave['status']) {
                                            'approved'  => 'color:#16a34a',
                                            'pending'   => 'color:#d97706',
                                            'rejected'  => 'color:#dc2626',
                                            default     => 'color:#6b7280',
                                        };
                                    @endphp
                                    <span style="font-size:11px; font-weight:600; {{ $color }}">{{ ucfirst($leave['status']) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-400 text-center py-4">No recent leave requests.</p>
            @endif
        </x-filament::card>

        {{-- Open Cases --}}
        <x-filament::card>
            <div class="flex items-center justify-between mb-3">
                <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">Open Cases</div>
                <span class="text-xs font-medium {{ count($openCases) > 0 ? 'text-red-600 bg-red-50 dark:bg-red-900/20' : 'text-gray-500 bg-gray-100 dark:bg-gray-800' }} px-2 py-0.5 rounded-full">
                    {{ count($openCases) }}
                </span>
            </div>
            @if(count($openCases))
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="pb-2 text-left text-xs font-medium text-gray-400">Employee</th>
                            <th class="pb-2 text-left text-xs font-medium text-gray-400">Kind / Type</th>
                            <th class="pb-2 text-left text-xs font-medium text-gray-400">Date</th>
                            <th class="pb-2 text-right text-xs font-medium text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($openCases as $case)
                            <tr class="border-b border-gray-50 dark:border-gray-800">
                                <td class="py-2 text-xs font-medium text-gray-800 dark:text-gray-200">{{ $case['employee'] }}</td>
                                <td class="py-2 text-xs text-gray-500">
                                    <span style="font-size:10px; font-weight:600; {{ $case['kind'] === 'Disciplinary' ? 'color:#e11d48' : 'color:#d97706' }}">{{ $case['kind'] }}</span>
                                    <span class="block text-gray-400">{{ $case['type'] }}</span>
                                </td>
                                <td class="py-2 text-xs text-gray-400">{{ $case['date'] }}</td>
                                <td class="py-2 text-right">
                                    <span style="font-size:11px; font-weight:600; {{ $case['color'] === 'danger' ? 'color:#dc2626' : 'color:#d97706' }}">{{ $case['status'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-400 text-center py-4">No open cases. All clear.</p>
            @endif
        </x-filament::card>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         CHART INITIALISATION — Alpine x-init with dynamic Chart.js load
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div
        x-data
        x-init="
            $nextTick(function() {
                function initCharts() {
                    var dark = document.documentElement.classList.contains('dark');
                    var grid = dark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
                    var label = dark ? 'rgba(255,255,255,0.45)' : 'rgba(0,0,0,0.4)';
                    var border = dark ? '#111827' : '#ffffff';

                    Chart.defaults.font = { family: 'inherit', size: 11 };
                    Chart.defaults.color = label;

                    // 1. Dept Headcount bar
                    var deptEl = document.getElementById('deptChart');
                    if (deptEl && {{ count($charts['dept_headcount']['data']) }} > 0) {
                        new Chart(deptEl, {
                            type: 'bar',
                            data: {
                                labels: @json($charts['dept_headcount']['labels']),
                                datasets: [{ label: 'Employees', data: @json($charts['dept_headcount']['data']),
                                    backgroundColor: 'rgba(99,102,241,0.75)', borderColor: 'rgba(99,102,241,1)',
                                    borderWidth: 1, borderRadius: 4 }]
                            },
                            options: {
                                responsive: true, maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    x: { grid: { color: grid }, ticks: { maxRotation: 35 } },
                                    y: { grid: { color: grid }, beginAtZero: true, ticks: { stepSize: 1 } }
                                }
                            }
                        });
                    }

                    // 2. Payroll Trend line
                    var payEl = document.getElementById('payrollChart');
                    if (payEl && {{ count($charts['payroll_trend']['labels']) }} > 0) {
                        new Chart(payEl, {
                            type: 'line',
                            data: {
                                labels: @json($charts['payroll_trend']['labels']),
                                datasets: [
                                    { label: 'Gross', data: @json($charts['payroll_trend']['gross']),
                                      borderColor: 'rgba(139,92,246,0.9)', backgroundColor: 'rgba(139,92,246,0.1)',
                                      fill: true, tension: 0.35, pointRadius: 3 },
                                    { label: 'Net', data: @json($charts['payroll_trend']['net']),
                                      borderColor: 'rgba(34,197,94,0.9)', backgroundColor: 'rgba(34,197,94,0.08)',
                                      fill: true, tension: 0.35, pointRadius: 3 },
                                    { label: 'Deductions', data: @json($charts['payroll_trend']['deductions']),
                                      borderColor: 'rgba(239,68,68,0.8)', backgroundColor: 'transparent',
                                      borderDash: [4,3], tension: 0.35, pointRadius: 3 }
                                ]
                            },
                            options: {
                                responsive: true, maintainAspectRatio: false,
                                plugins: { legend: { position: 'top', labels: { boxWidth: 12 } } },
                                scales: {
                                    x: { grid: { color: grid } },
                                    y: { grid: { color: grid }, beginAtZero: false }
                                }
                            }
                        });
                    }

                    // 3. Attendance doughnut
                    var attEl = document.getElementById('attendanceChart');
                    if (attEl && {{ count($charts['attendance']['data']) }} > 0) {
                        new Chart(attEl, {
                            type: 'doughnut',
                            data: {
                                labels: @json($charts['attendance']['labels']),
                                datasets: [{ data: @json($charts['attendance']['data']),
                                    backgroundColor: ['rgba(34,197,94,0.8)','rgba(239,68,68,0.8)','rgba(245,158,11,0.8)','rgba(14,165,233,0.8)'],
                                    borderWidth: 2, borderColor: border }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, cutout: '60%',
                                plugins: { legend: { position: 'right', labels: { boxWidth: 10, padding: 6 } } } }
                        });
                    }

                    // 4. Leave Status doughnut
                    var lvEl = document.getElementById('leaveStatusChart');
                    if (lvEl && {{ count($charts['leave_status']['data']) }} > 0) {
                        new Chart(lvEl, {
                            type: 'doughnut',
                            data: {
                                labels: @json($charts['leave_status']['labels']),
                                datasets: [{ data: @json($charts['leave_status']['data']),
                                    backgroundColor: ['rgba(245,158,11,0.8)','rgba(34,197,94,0.8)','rgba(239,68,68,0.8)','rgba(156,163,175,0.8)'],
                                    borderWidth: 2, borderColor: border }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, cutout: '58%',
                                plugins: { legend: { position: 'right', labels: { boxWidth: 10, padding: 6 } } } }
                        });
                    }

                    // 5. Employment Type doughnut
                    var etEl = document.getElementById('empTypeChart');
                    if (etEl && {{ count($charts['emp_type']['data']) }} > 0) {
                        new Chart(etEl, {
                            type: 'doughnut',
                            data: {
                                labels: @json($charts['emp_type']['labels']),
                                datasets: [{ data: @json($charts['emp_type']['data']),
                                    backgroundColor: ['rgba(99,102,241,0.8)','rgba(14,165,233,0.8)','rgba(168,85,247,0.8)','rgba(34,197,94,0.8)','rgba(245,158,11,0.8)'],
                                    borderWidth: 2, borderColor: border }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, cutout: '60%',
                                plugins: { legend: { display: false } } }
                        });
                    }
                }

                if (window.Chart) {
                    initCharts();
                } else {
                    var s = document.createElement('script');
                    s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js';
                    s.onload = initCharts;
                    document.head.appendChild(s);
                }
            });
        "
    ></div>

</x-filament-panels::page>
