<div class="container">
    <h1>Employee Salary Details</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Employee Information</h5>
                    <p><strong>Name:</strong> {{ $salary->employee->full_name ?? 'N/A' }}</p>
                    <p><strong>Company:</strong> {{ $salary->company->name ?? 'N/A' }}</p>
                </div>
                
                <div class="col-md-6">
                    <h5>Salary Information</h5>
                    <p><strong>Basic Salary:</strong> {{ number_format($salary->basic_salary, 2) }} {{ $salary->currency }}</p>
                    <p><strong>Effective From:</strong> {{ $salary->effective_from->format('M d, Y') }}</p>
                    <p><strong>Effective To:</strong> {{ $salary->effective_to ? $salary->effective_to->format('M d, Y') : 'N/A' }}</p>
                    <p><strong>Current:</strong> 
                        @if($salary->is_current)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </p>
                    @if($salary->salaryScale)
                        <p><strong>Salary Scale:</strong> {{ $salary->salaryScale->name }}</p>
                    @endif
                </div>
            </div>
            
            <a href="{{ route('hr.employee-salaries.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>