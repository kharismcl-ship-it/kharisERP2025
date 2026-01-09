<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Hostels</h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
        @php
            $companyId = null;
            try {
                $companyId = app('current_company_id');
            } catch (\Exception $e) {
                $companyId = session('current_company_id');
            }
            
            $companyId = $companyId ?? (auth()->check() ? auth()->user()->companies()->first()?->id : null);
            $isSuperAdmin = auth()->check() && auth()->user()->hasRole(config('core.super_admin_role', 'Super Admin'));
        @endphp
        
        @if (!$companyId && !$isSuperAdmin)
            <div class="text-red-500">
                <p>No company context found. Please select a company to view hostels.</p>
            </div>
        @else
            <ul class="list-disc pl-6">
                @forelse ($hostels as $hostel)
                    <li>
                        <a href="{{ route('hostels.admin.dashboard', $hostel->slug) }}" class="text-blue-500 hover:underline">
                            {{ $hostel->name }}
                        </a>
                    </li>
                @empty
                    <li>{{ $companyId ? 'No hostels found for this company.' : 'No hostels found.' }}</li>
                @endforelse
            </ul>
        @endif
    </div>
</div>