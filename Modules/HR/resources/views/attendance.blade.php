<div>
    <h1>Attendance Management</h1>
    
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3>Mark Attendance for {{ $date }}</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" wire:model="date" class="form-control" id="date">
            </div>

            <button wire:click="loadAttendance" class="btn btn-primary mb-3">Load Attendance</button>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Status</th>
                        <th>Check-in Time</th>
                        <th>Check-out Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                        <tr>
                            <td>{{ $employee->full_name }}</td>
                            <td>
                                <select wire:model="attendance.{{ $employee->id }}.status" class="form-control">
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="leave">Leave</option>
                                    <option value="off">Day Off</option>
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

            <button wire:click="saveAttendance" class="btn btn-success">Save Attendance</button>
        </div>
    </div>
</div>