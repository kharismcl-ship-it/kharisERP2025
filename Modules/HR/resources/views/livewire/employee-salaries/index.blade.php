<div class="container">
    <h1>Employee Salaries</h1>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Company</th>
                        <th>Basic Salary</th>
                        <th>Currency</th>
                        <th>Effective From</th>
                        <th>Effective To</th>
                        <th>Current</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $salary)
                        <tr>
                            <td>{{ $salary->employee->full_name ?? 'N/A' }}</td>
                            <td>{{ $salary->company->name ?? 'N/A' }}</td>
                            <td>{{ number_format($salary->basic_salary, 2) }}</td>
                            <td>{{ $salary->currency }}</td>
                            <td>{{ $salary->effective_from->format('M d, Y') }}</td>
                            <td>{{ $salary->effective_to ? $salary->effective_to->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                @if($salary->is_current)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('hr.employee-salaries.show', $salary) }}" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{ $salaries->links() }}
        </div>
    </div>
</div>