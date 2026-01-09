<div class="container">
    <h1>Performance Cycles</h1>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cycles as $cycle)
                        <tr>
                            <td>{{ $cycle->company->name ?? 'N/A' }}</td>
                            <td>{{ $cycle->name }}</td>
                            <td>{{ $cycle->start_date->format('M d, Y') }}</td>
                            <td>{{ $cycle->end_date->format('M d, Y') }}</td>
                            <td>
                                @if($cycle->status == 'planned')
                                    <span class="badge bg-secondary">Planned</span>
                                @elseif($cycle->status == 'open')
                                    <span class="badge bg-success">Open</span>
                                @elseif($cycle->status == 'closed')
                                    <span class="badge bg-danger">Closed</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('hr.performance-cycles.show', $cycle) }}" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{ $cycles->links() }}
        </div>
    </div>
</div>