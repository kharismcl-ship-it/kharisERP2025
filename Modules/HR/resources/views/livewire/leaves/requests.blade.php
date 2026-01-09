<div>
    <h1>My Leave Requests</h1>
    
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Request Leave</h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="submitLeaveRequest">
                        <div class="mb-3">
                            <label for="leave_type_id" class="form-label">Leave Type</label>
                            <select wire:model="leaveTypeId" class="form-control" id="leave_type_id" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                                @endforeach
                            </select>
                            @error('leaveTypeId') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" wire:model="startDate" class="form-control" id="start_date" required>
                                    @error('startDate') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" wire:model="endDate" class="form-control" id="end_date" required>
                                    @error('endDate') <span class="text-danger">{{ $message }}</span> @enderror
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
                    <h3>My Leave Requests</h3>
                </div>
                <div class="card-body">
                    @if($leaveRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>Dates</th>
                                        <th>Days</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaveRequests as $request)
                                        <tr>
                                            <td>{{ $request->leaveType->name }}</td>
                                            <td>{{ $request->start_date->format('M d, Y') }} - {{ $request->end_date->format('M d, Y') }}</td>
                                            <td>{{ $request->total_days }}</td>
                                            <td>
                                                <span class="badge bg-{{ $request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>You have not submitted any leave requests yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>