<div>
    <h1>Employees</h1>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3>Employee List</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" wire:model.debounce.300ms="search" placeholder="Search employees..." class="form-control">
                </div>
                <div class="col-md-3">
                    <select wire:model="departmentId" class="form-control">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model="employmentStatus" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="probation">Probation</option>
                        <option value="suspended">Suspended</option>
                        <option value="terminated">Terminated</option>
                        <option value="resigned">Resigned</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model="employmentType" class="form-control">
                        <option value="">All Types</option>
                        <option value="full_time">Full Time</option>
                        <option value="part_time">Part Time</option>
                        <option value="contract">Contract</option>
                        <option value="intern">Intern</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Hire Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->employee_code }}</td>
                                <td>{{ $employee->full_name }}</td>
                                <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                <td>{{ $employee->jobPosition->title ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $employee->employment_status == 'active' ? 'success' : ($employee->employment_status == 'probation' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($employee->employment_status) }}
                                    </span>
                                </td>
                                <td>{{ $employee->hire_date->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('hr.employees.show', $employee->id) }}" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $employees->links() }}
        </div>
    </div>
</div>