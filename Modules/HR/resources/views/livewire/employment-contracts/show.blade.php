<div class="container">
    <h1>Employment Contract Details</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Employee Information</h5>
                    <p><strong>Name:</strong> {{ $contract->employee->full_name ?? 'N/A' }}</p>
                    <p><strong>Company:</strong> {{ $contract->company->name ?? 'N/A' }}</p>
                </div>
                
                <div class="col-md-6">
                    <h5>Contract Information</h5>
                    <p><strong>Contract Number:</strong> {{ $contract->contract_number ?? 'N/A' }}</p>
                    <p><strong>Start Date:</strong> {{ $contract->start_date->format('M d, Y') }}</p>
                    <p><strong>End Date:</strong> {{ $contract->end_date ? $contract->end_date->format('M d, Y') : 'N/A' }}</p>
                    <p><strong>Contract Type:</strong> {{ ucfirst(str_replace('_', ' ', $contract->contract_type)) }}</p>
                    <p><strong>Current:</strong> 
                        @if($contract->is_current)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </p>
                    @if($contract->probation_end_date)
                        <p><strong>Probation End Date:</strong> {{ $contract->probation_end_date->format('M d, Y') }}</p>
                    @endif
                    @if($contract->basic_salary)
                        <p><strong>Basic Salary:</strong> {{ number_format($contract->basic_salary, 2) }} {{ $contract->currency }}</p>
                    @endif
                    @if($contract->working_hours_per_week)
                        <p><strong>Working Hours/Week:</strong> {{ $contract->working_hours_per_week }}</p>
                    @endif
                </div>
            </div>
            
            @if($contract->notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Notes</h5>
                        <p>{{ $contract->notes }}</p>
                    </div>
                </div>
            @endif
            
            <a href="{{ route('hr.employment-contracts.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>