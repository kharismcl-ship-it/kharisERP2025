<div class="container">
    <h1>Performance Review Details</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Review Information</h5>
                    <p><strong>Employee:</strong> {{ $review->employee->full_name ?? 'N/A' }}</p>
                    <p><strong>Reviewer:</strong> {{ $review->reviewer->full_name ?? 'N/A' }}</p>
                    <p><strong>Company:</strong> {{ $review->company->name ?? 'N/A' }}</p>
                    <p><strong>Cycle:</strong> {{ $review->performanceCycle->name ?? 'N/A' }}</p>
                </div>
                
                <div class="col-md-6">
                    <h5>Review Details</h5>
                    <p><strong>Rating:</strong> {{ $review->rating ?? 'N/A' }}</p>
                </div>
            </div>
            
            @if($review->comments)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Comments</h5>
                        <p>{{ $review->comments }}</p>
                    </div>
                </div>
            @endif
            
            <a href="{{ route('hr.performance-reviews.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>