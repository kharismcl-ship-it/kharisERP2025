<div>
    <h1>Leave Management</h1>
    
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3>All Leave Requests</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-2">
                    <input type="text" wire:model.debounce.300ms="search" placeholder="Search employees..." class="form-control">
                </div>
                <div class="col-md-2">
                    <select wire:model="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model="leaveTypeId" class="form-control">
                        <option value="">All Leave Types</option>
                        @foreach($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model="departmentId" class="form-control">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" wire:model="startDate" class="form-control" placeholder="Start Date">
                </div>
                <div class="col-md-2">
                    <input type="date" wire:model="endDate" class="form-control" placeholder="End Date">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Leave Type</th>
                            <th>Dates</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveRequests as $request)
                            <tr>
                                <td>{{ $request->employee->full_name }}</td>
                                <td>{{ $request->employee->department->name ?? 'N/A' }}</td>
                                <td>{{ $request->leaveType->name }}</td>
                                <td>{{ $request->start_date->format('M d, Y') }} - {{ $request->end_date->format('M d, Y') }}</td>
                                <td>{{ $request->total_days }}</td>
                                <td>
                                    <span class="badge bg-{{ $request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($request->status == 'pending')
                                        <button wire:click="approveLeaveRequest({{ $request->id }})" class="btn btn-sm btn-success">Approve</button>
                                        <button wire:click="rejectLeaveRequest({{ $request->id }})" class="btn btn-sm btn-danger">Reject</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $leaveRequests->links() }}
        </div>
    </div>
</div>