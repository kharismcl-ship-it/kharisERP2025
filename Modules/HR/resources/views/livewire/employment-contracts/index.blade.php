<div class="container">
    <h1>Employment Contracts</h1>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Company</th>
                        <th>Contract Number</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Contract Type</th>
                        <th>Current</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contracts as $contract)
                        <tr>
                            <td>{{ $contract->employee->full_name ?? 'N/A' }}</td>
                            <td>{{ $contract->company->name ?? 'N/A' }}</td>
                            <td>{{ $contract->contract_number ?? 'N/A' }}</td>
                            <td>{{ $contract->start_date->format('M d, Y') }}</td>
                            <td>{{ $contract->end_date ? $contract->end_date->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $contract->contract_type)) }}</td>
                            <td>
                                @if($contract->is_current)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('hr.employment-contracts.show', $contract) }}" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{ $contracts->links() }}
        </div>
    </div>
</div>