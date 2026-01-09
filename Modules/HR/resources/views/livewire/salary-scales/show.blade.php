<div class="container">
    <h1>Salary Scale Details</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Scale Information</h5>
                    <p><strong>Name:</strong> {{ $scale->name }}</p>
                    <p><strong>Code:</strong> {{ $scale->code ?? 'N/A' }}</p>
                    <p><strong>Company:</strong> {{ $scale->company->name ?? 'N/A' }}</p>
                </div>
                
                <div class="col-md-6">
                    <h5>Financial Details</h5>
                    <p><strong>Min Basic:</strong> {{ number_format($scale->min_basic, 2) }} {{ $scale->currency }}</p>
                    <p><strong>Max Basic:</strong> {{ number_format($scale->max_basic, 2) }} {{ $scale->currency }}</p>
                </div>
            </div>
            
            @if($scale->description)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Description</h5>
                        <p>{{ $scale->description }}</p>
                    </div>
                </div>
            @endif
            
            <a href="{{ route('hr.salary-scales.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>