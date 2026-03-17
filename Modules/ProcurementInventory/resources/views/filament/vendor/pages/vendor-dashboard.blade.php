<x-filament-panels::page>
    @php $vendor = auth('vendor')->user()->vendor; @endphp

    {{-- Vendor Welcome --}}
    <div class="bg-slate-700 text-white rounded-xl p-6 mb-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-xl font-bold">
            {{ strtoupper(substr($vendor?->name ?? 'V', 0, 1)) }}
        </div>
        <div>
            <h2 class="text-lg font-bold">{{ $vendor?->name ?? 'Vendor Portal' }}</h2>
            <p class="text-slate-300 text-sm">Welcome, {{ auth('vendor')->user()->name }}</p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm text-center">
            <p class="text-3xl font-bold text-slate-700">{{ $openPos }}</p>
            <p class="text-xs text-gray-500 mt-1">Open Purchase Orders</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm text-center">
            <p class="text-3xl font-bold text-amber-600">{{ $pendingPos }}</p>
            <p class="text-xs text-gray-500 mt-1">Awaiting Your Confirmation</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm text-center">
            <p class="text-3xl font-bold text-green-600">{{ $receivedPos }}</p>
            <p class="text-xs text-gray-500 mt-1">Delivered / Received</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm text-center">
            <p class="text-2xl font-bold text-slate-700">${{ number_format($totalValue, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Order Value (Delivered)</p>
        </div>
    </div>

    {{-- Vendor Contact Details --}}
    @if($vendor)
    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Your Company Details</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm text-gray-600">
            <div><span class="font-medium text-gray-700">Email:</span> {{ $vendor->email ?? '—' }}</div>
            <div><span class="font-medium text-gray-700">Phone:</span> {{ $vendor->phone ?? '—' }}</div>
            <div><span class="font-medium text-gray-700">Payment Terms:</span> {{ $vendor->payment_terms ? $vendor->payment_terms . ' days' : '—' }}</div>
            <div><span class="font-medium text-gray-700">Currency:</span> {{ $vendor->currency ?? 'USD' }}</div>
            <div><span class="font-medium text-gray-700">City:</span> {{ $vendor->city ?? '—' }}</div>
            <div><span class="font-medium text-gray-700">Status:</span>
                <span class="ml-1 text-xs px-2 py-0.5 rounded-full {{ $vendor->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($vendor->status) }}
                </span>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>
