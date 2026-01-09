<div class="container">
    <h1>HR Dashboard</h1>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Employees</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalEmployees }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Departments</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $departments }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Present Today</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $presentToday }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">On Leave Today</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $onLeaveToday }}</h5>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('hr.employees.index') }}" class="btn btn-primary btn-block">Employees</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('hr.attendance.index') }}" class="btn btn-success btn-block">Attendance</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('hr.leaves.index') }}" class="btn btn-warning btn-block">Leaves</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('hr.leave-requests') }}" class="btn btn-info btn-block">Leave Requests ({{ $pendingLeaveRequests }})</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('hr.employee-salaries.index') }}" class="btn btn-secondary btn-block">Employee Salaries</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('hr.employment-contracts.index') }}" class="btn btn-dark btn-block">Employment Contracts</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('hr.performance-cycles.index') }}" class="btn btn-primary btn-block">Performance Cycles</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('hr.performance-reviews.index') }}" class="btn btn-success btn-block">Performance Reviews</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('hr.salary-scales.index') }}" class="btn btn-warning btn-block">Salary Scales</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('hr.org-chart.index') }}" class="btn btn-info btn-block">Org Chart</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>