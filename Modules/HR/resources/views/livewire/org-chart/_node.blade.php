{{-- Recursive org chart node --}}
<li class="relative {{ $depth > 0 ? 'ml-8 border-l-2 border-gray-200 pl-4 pt-2' : '' }}">

    {{-- Employee card --}}
    <div class="inline-flex items-center gap-3 bg-white border border-gray-200 rounded-lg shadow-sm px-3 py-2 mb-1 min-w-[200px]">
        {{-- Avatar --}}
        <div class="flex-shrink-0">
            @if($employee->photo_path)
                <img src="{{ Storage::url($employee->photo_path) }}"
                     alt="{{ $employee->full_name }}"
                     class="w-9 h-9 rounded-full object-cover">
            @else
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm">
                    {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name ?? '', 0, 1)) }}
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-800 truncate">{{ $employee->full_name }}</p>
            @if($employee->jobPosition)
                <p class="text-xs text-gray-500 truncate">{{ $employee->jobPosition->title }}</p>
            @endif
            @if($employee->department)
                <p class="text-xs text-indigo-500 truncate">{{ $employee->department->name }}</p>
            @endif
        </div>
    </div>

    {{-- Subordinates --}}
    @if($employee->subordinates && $employee->subordinates->count() > 0)
        <ul class="flex flex-col gap-0 mt-0">
            @foreach($employee->subordinates->where('employment_status', 'active') as $sub)
                @include('hr::livewire.org-chart._node', ['employee' => $sub, 'depth' => $depth + 1])
            @endforeach
        </ul>
    @endif
</li>
