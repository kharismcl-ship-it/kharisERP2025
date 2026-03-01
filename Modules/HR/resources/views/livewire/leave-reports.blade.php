<div class="container mx-auto p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Leave Management Reports</h2>

    <!-- Report Type Selection -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                <select 
                    wire:model="reportType" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    @foreach($reportTypeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Export Format</label>
                <select 
                    wire:model="exportFormat" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                </select>
            </div>

            <div class="flex items-end">
                <button 
                    wire:click="exportReport"
                    class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center justify-center"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export Report
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Filters</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input 
                    type="date" 
                    wire:model="filters.start_date" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input 
                    type="date" 
                    wire:model="filters.end_date" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                    wire:model="filters.status" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Statuses</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                <select 
                    wire:model="filters.leave_type_id" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Leave Types</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select 
                    wire:model="filters.department_id" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($reportType === 'employee')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                    <select 
                        wire:model="filters.employee_id" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="flex items-end">
                    <button 
                        wire:click="clearFilters"
                        class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                    >
                        Clear Filters
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Report Content -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        @if($reportType === 'detailed')
            <!-- Detailed Report -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Detailed Leave Report</h3>
            
            @if(count($reportData) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reportData as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $item['employee_name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $item['employee_code'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['department'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['leave_type'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item['start_date'] }} to {{ $item['end_date'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['total_days'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ 
                                            $item['status'] === 'Approved' ? 'bg-green-100 text-green-800' : 
                                            ($item['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
                                        }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['approved_by'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2">No leave data found for the selected filters.</p>
                </div>
            @endif

        @elseif($reportType === 'summary')
            <!-- Summary Report -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Leave Summary Report</h3>
            
            @if(!empty($summaryData))
                <!-- Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="text-2xl font-bold text-blue-600">{{ $summaryData['overview']['total_requests'] }}</div>
                        <div class="text-sm text-blue-800">Total Requests</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="text-2xl font-bold text-green-600">{{ $summaryData['overview']['approved_requests'] }}</div>
                        <div class="text-sm text-green-800">Approved</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                        <div class="text-2xl font-bold text-yellow-600">{{ $summaryData['overview']['pending_requests'] }}</div>
                        <div class="text-sm text-yellow-800">Pending</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                        <div class="text-2xl font-bold text-red-600">{{ $summaryData['overview']['rejected_requests'] }}</div>
                        <div class="text-sm text-red-800">Rejected</div>
                    </div>
                </div>

                <!-- Department Summary -->
                <h4 class="text-md font-semibold text-gray-700 mb-3">By Department</h4>
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rejected</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($summaryData['department_summary'] as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['department'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['total_requests'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ $item['approved_requests'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600">{{ $item['pending_requests'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ $item['rejected_requests'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['total_days'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Leave Type Summary -->
                <h4 class="text-md font-semibold text-gray-700 mb-3">By Leave Type</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rejected</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($summaryData['leave_type_summary'] as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['leave_type'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['total_requests'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ $item['approved_requests'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600">{{ $item['pending_requests'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ $item['rejected_requests'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['total_days'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>No summary data available.</p>
                </div>
            @endif

        @elseif($reportType === 'employee')
            <!-- Employee Report -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Employee Leave Report</h3>
            
            @if(!empty($reportData['employee']) && !empty($reportData['leave_history']))
                <!-- Employee Info -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h4 class="text-md font-semibold text-gray-700 mb-2">Employee Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div><strong>Name:</strong> {{ $reportData['employee']['name'] }}</div>
                        <div><strong>Code:</strong> {{ $reportData['employee']['code'] }}</div>
                        <div><strong>Department:</strong> {{ $reportData['employee']['department'] }}</div>
                        <div><strong>Position:</strong> {{ $reportData['employee']['position'] }}</div>
                    </div>
                </div>

                <!-- Leave History -->
                <h4 class="text-md font-semibold text-gray-700 mb-3">Leave History</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reportData['leave_history'] as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['leave_type'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item['start_date'] }} to {{ $item['end_date'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['days'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ 
                                            $item['status'] === 'Approved' ? 'bg-green-100 text-green-800' : 
                                            ($item['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
                                        }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['approved_by'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Summary -->
                <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-700 mb-2">Summary</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div><strong>Total Requests:</strong> {{ $reportData['summary']['total_requests'] }}</div>
                        <div><strong>Approved:</strong> {{ $reportData['summary']['approved_requests'] }}</div>
                        <div><strong>Pending:</strong> {{ $reportData['summary']['pending_requests'] }}</div>
                        <div><strong>Rejected:</strong> {{ $reportData['summary']['rejected_requests'] }}</div>
                        <div><strong>Total Days Taken:</strong> {{ $reportData['summary']['total_days_taken'] }}</div>
                        <div><strong>Most Common:</strong> {{ $reportData['summary']['most_common_leave_type'] }}</div>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>Please select an employee to view their leave report.</p>
                </div>
            @endif
        @endif
    </div>
</div>