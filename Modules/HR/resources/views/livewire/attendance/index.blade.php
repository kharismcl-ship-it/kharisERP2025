<div>
    <h1>Attendance Management</h1>
    
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3>Mark Attendance for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" wire:model="date" class="form-control" id="date">
                </div>
                <div class="col-md-3">
                    <label for="department" class="form-label">Department</label>
                    <select wire:model="departmentId" class="form-control" id="department">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="loadAttendance" class="btn btn-primary">Load Attendance</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Check-in Time</th>
                            <th>Check-out Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->full_name }}</td>
                                <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                <td>
                                    <select wire:model="attendance.{{ $employee->id }}.status" class="form-control">
                                        <option value="present">Present</option>
                                        <option value="absent">Absent</option>
                                        <option value="leave">Leave</option>
                                        <option value="off">Day Off</option>
                                        <option value="remote">Remote</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="datetime-local" wire:model="attendance.{{ $employee->id }}.check_in_time" class="form-control">
                                </td>
                                <td>
                                    <input type="datetime-local" wire:model="attendance.{{ $employee->id }}.check_out_time" class="form-control">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button wire:click="saveAttendance" class="btn btn-success">Save Attendance</button>
        </div>
    </div>
</div>