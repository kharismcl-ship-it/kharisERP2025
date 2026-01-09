<div class="container">
    <h1>Performance Reviews</h1>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Reviewer</th>
                        <th>Company</th>
                        <th>Cycle</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reviews as $review)
                        <tr>
                            <td>{{ $review->employee->full_name ?? 'N/A' }}</td>
                            <td>{{ $review->reviewer->full_name ?? 'N/A' }}</td>
                            <td>{{ $review->company->name ?? 'N/A' }}</td>
                            <td>{{ $review->performanceCycle->name ?? 'N/A' }}</td>
                            <td>{{ $review->rating ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('hr.performance-reviews.show', $review) }}" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{ $reviews->links() }}
        </div>
    </div>
</div>