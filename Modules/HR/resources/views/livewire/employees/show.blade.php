<div>
    <h1>Employee Profile: {{ $employee->full_name }}</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    @if($employee->photo_path)
                        <img src="{{ $employee->photo_path }}" class="img-fluid rounded-circle mb-3" alt="{{ $employee->full_name }}">
                    @else
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                            <i class="fas fa-user fa-3x text-secondary"></i>
                        </div>
                    @endif
                    <h4>{{ $employee->full_name }}</h4>
                    <p class="text-muted">{{ $employee->jobPosition->title ?? 'N/A' }}</p>
                    <p class="text-muted">{{ $employee->department->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <ul class="nav nav-tabs" id="employeeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">Profile</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="employment-tab" data-bs-toggle="tab" data-bs-target="#employment" type="button" role="tab">Employment</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">Attendance</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="leave-tab" data-bs-toggle="tab" data-bs-target="#leave" type="button" role="tab">Leave</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">Documents</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button" role="tab">Performance</button>
                </li>
            </ul>

            <div class="tab-content" id="employeeTabsContent">
                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Personal Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>First Name:</strong> {{ $employee->first_name }}</p>
                                    <p><strong>Last Name:</strong> {{ $employee->last_name }}</p>
                                    <p><strong>Other Names:</strong> {{ $employee->other_names ?? 'N/A' }}</p>
                                    <p><strong>Gender:</strong> {{ $employee->gender ?? 'N/A' }}</p>
                                    <p><strong>Date of Birth:</strong> {{ $employee->dob ? $employee->dob->format('M d, Y') : 'N/A' }}</p>
                                    <p><strong>Marital Status:</strong> {{ $employee->marital_status ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Phone:</strong> {{ $employee->phone }}</p>
                                    <p><strong>Alternative Phone:</strong> {{ $employee->alt_phone ?? 'N/A' }}</p>
                                    <p><strong>Email:</strong> {{ $employee->email ?? 'N/A' }}</p>
                                    <p><strong>National ID:</strong> {{ $employee->national_id_number ?? 'N/A' }}</p>
                                    <p><strong>Address:</strong> {{ $employee->address ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Emergency Contact</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> {{ $employee->emergency_contact_name ?? 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $employee->emergency_contact_phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="employment" role="tabpanel">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Employment Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Employee Code:</strong> {{ $employee->employee_code }}</p>
                                    <p><strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}</p>
                                    <p><strong>Position:</strong> {{ $employee->jobPosition->title ?? 'N/A' }}</p>
                                    <p><strong>Manager:</strong> {{ $employee->manager->full_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Hire Date:</strong> {{ $employee->hire_date->format('M d, Y') }}</p>
                                    <p><strong>Employment Type:</strong> {{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}</p>
                                    <p><strong>Employment Status:</strong> 
                                        <span class="badge bg-{{ $employee->employment_status == 'active' ? 'success' : ($employee->employment_status == 'probation' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($employee->employment_status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Contracts</h4>
                        </div>
                        <div class="card-body">
                            @if($employee->contracts->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Contract Number</th>
                                                <th>Type</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Basic Salary</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employee->contracts as $contract)
                                                <tr>
                                                    <td>{{ $contract->contract_number ?? 'N/A' }}</td>
                                                    <td>{{ ucfirst($contract->contract_type) }}</td>
                                                    <td>{{ $contract->start_date->format('M d, Y') }}</td>
                                                    <td>{{ $contract->end_date ? $contract->end_date->format('M d, Y') : 'Indefinite' }}</td>
                                                    <td>{{ $contract->basic_salary ? $contract->basic_salary . ' ' . $contract->currency : 'N/A' }}</td>
                                                    <td>
                                                        @if($contract->is_current)
                                                            <span class="badge bg-success">Current</span>
                                                        @else
                                                            <span class="badge bg-secondary">Past</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>No contracts found.</p>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Salary History</h4>
                        </div>
                        <div class="card-body">
                            @if($employee->salaries->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Salary Scale</th>
                                                <th>Basic Salary</th>
                                                <th>Effective From</th>
                                                <th>Effective To</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employee->salaries as $salary)
                                                <tr>
                                                    <td>{{ $salary->salaryScale->name ?? 'N/A' }}</td>
                                                    <td>{{ $salary->basic_salary . ' ' . $salary->currency }}</td>
                                                    <td>{{ $salary->effective_from->format('M d, Y') }}</td>
                                                    <td>{{ $salary->effective_to ? $salary->effective_to->format('M d, Y') : 'Present' }}</td>
                                                    <td>
                                                        @if($salary->is_current)
                                                            <span class="badge bg-success">Current</span>
                                                        @else
                                                            <span class="badge bg-secondary">Past</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>No salary records found.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="attendance" role="tabpanel">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Attendance Records</h4>
                        </div>
                        <div class="card-body">
                            @if($employee->attendanceRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Check In</th>
                                                <th>Check Out</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employee->attendanceRecords->sortByDesc('date')->take(10) as $record)
                                                <tr>
                                                    <td>{{ $record->date->format('M d, Y') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $record->status == 'present' ? 'success' : ($record->status == 'absent' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($record->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $record->check_in_time ? $record->check_in_time->format('H:i') : 'N/A' }}</td>
                                                    <td>{{ $record->check_out_time ? $record->check_out_time->format('H:i') : 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>No attendance records found.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="leave" role="tabpanel">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Leave Requests</h4>
                        </div>
                        <div class="card-body">
                            @if($employee->leaveRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Leave Type</th>
                                                <th>Dates</th>
                                                <th>Total Days</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employee->leaveRequests->sortByDesc('created_at')->take(10) as $request)
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
                                <p>No leave requests found.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Documents</h4>
                        </div>
                        <div class="card-body">
                            @if($employee->documents->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Document Type</th>
                                                <th>Description</th>
                                                <th>Uploaded By</th>
                                                <th>Uploaded At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employee->documents as $document)
                                                <tr>
                                                    <td>{{ $document->document_type }}</td>
                                                    <td>{{ $document->description ?? 'N/A' }}</td>
                                                    <td>{{ $document->uploadedBy->name ?? 'N/A' }}</td>
                                                    <td>{{ $document->created_at->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        <a href="{{ $document->file_path }}" class="btn btn-sm btn-primary" target="_blank">View</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>No documents found.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="performance" role="tabpanel">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Performance Reviews</h4>
                        </div>
                        <div class="card-body">
                            @if($employee->performanceReviews->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cycle</th>
                                                <th>Reviewer</th>
                                                <th>Rating</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employee->performanceReviews as $review)
                                                <tr>
                                                    <td>{{ $review->performanceCycle->name }}</td>
                                                    <td>{{ $review->reviewer->full_name ?? 'N/A' }}</td>
                                                    <td>{{ $review->rating ?? 'N/A' }}</td>
                                                    <td>{{ $review->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>No performance reviews found.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>