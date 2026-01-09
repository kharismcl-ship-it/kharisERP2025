<div>
    <h1>Organization Chart</h1>

    <div class="row">
        @foreach($departments as $department)
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $department->name }}</h4>
                    </div>
                    <div class="card-body">
                        @if($department->employees->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Position</th>
                                            <th>Manager</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($department->employees as $employee)
                                            <tr>
                                                <td>{{ $employee->full_name }}</td>
                                                <td>{{ $employee->jobPosition->title ?? 'N/A' }}</td>
                                                <td>{{ $employee->manager->full_name ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No employees in this department.</p>
                        @endif

                        @if($department->children->count() > 0)
                            <h5>Sub-departments:</h5>
                            <ul>
                                @foreach($department->children as $child)
                                    <li>{{ $child->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>