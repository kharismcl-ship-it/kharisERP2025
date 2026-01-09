<div class="container">
    <h1>Salary Scales</h1>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Min Basic</th>
                        <th>Max Basic</th>
                        <th>Currency</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scales as $scale)
                        <tr>
                            <td>{{ $scale->company->name ?? 'N/A' }}</td>
                            <td>{{ $scale->name }}</td>
                            <td>{{ $scale->code ?? 'N/A' }}</td>
                            <td>{{ number_format($scale->min_basic, 2) }}</td>
                            <td>{{ number_format($scale->max_basic, 2) }}</td>
                            <td>{{ $scale->currency }}</td>
                            <td>
                                <a href="{{ route('hr.salary-scales.show', $scale) }}" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{ $scales->links() }}
        </div>
    </div>
</div>