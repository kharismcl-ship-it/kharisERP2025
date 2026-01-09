<div>
    <h1>Leave Management</h1>
    
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Submit Leave Request</h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="submitLeaveRequest">
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select wire:model="employee_id" class="form-control" id="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                            @error('employee_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="leave_type_id" class="form-label">Leave Type</label>
                            <select wire:model="leave_type_id" class="form-control" id="leave_type_id" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                                @endforeach
                            </select>
                            @error('leave_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" wire:model="start_date" class="form-control" id="start_date" required>
                                    @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" wire:model="end_date" class="form-control" id="end_date" required>
                                    @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea wire:model="reason" class="form-control" id="reason" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Leave Request</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Leave Requests</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee</th>
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
                                    <td>{{ $request->leaveType->name }}</td>
                                    <td>{{ $request->start_date->format('M d, Y') }} - {{ $request->end_date->format('M d, Y') }}</td>
                                    <td>{{ $request->start_date->diffInDays($request->end_date) + 1 }}</td>
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
            </div>
        </div>
    </div>
</div>