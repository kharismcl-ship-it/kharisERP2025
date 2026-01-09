<div class="container">
    <h1>Performance Cycle Details</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Cycle Information</h5>
                    <p><strong>Name:</strong> {{ $cycle->name }}</p>
                    <p><strong>Company:</strong> {{ $cycle->company->name ?? 'N/A' }}</p>
                    <p><strong>Status:</strong> 
                        @if($cycle->status == 'planned')
                            <span class="badge bg-secondary">Planned</span>
                        @elseif($cycle->status == 'open')
                            <span class="badge bg-success">Open</span>
                        @elseif($cycle->status == 'closed')
                            <span class="badge bg-danger">Closed</span>
                        @endif
                    </p>
                </div>
                
                <div class="col-md-6">
                    <h5>Dates</h5>
                    <p><strong>Start Date:</strong> {{ $cycle->start_date->format('M d, Y') }}</p>
                    <p><strong>End Date:</strong> {{ $cycle->end_date->format('M d, Y') }}</p>
                </div>
            </div>
            
            @if($cycle->description)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Description</h5>
                        <p>{{ $cycle->description }}</p>
                    </div>
                </div>
            @endif
            
            <a href="{{ route('hr.performance-cycles.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>