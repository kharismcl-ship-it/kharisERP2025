<div class="p-4 space-y-6">

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $total }}</p>
            <p class="text-xs text-gray-500 mt-1">Total</p>
        </div>
        <div class="rounded-xl bg-success-50 dark:bg-success-900/30 border border-success-200 dark:border-success-800 p-4 text-center">
            <p class="text-2xl font-bold text-success-700 dark:text-success-300">{{ $available }}</p>
            <p class="text-xs text-success-600 dark:text-success-400 mt-1">Available</p>
        </div>
        <div class="rounded-xl bg-warning-50 dark:bg-warning-900/30 border border-warning-200 dark:border-warning-800 p-4 text-center">
            <p class="text-2xl font-bold text-warning-700 dark:text-warning-300">{{ $issued }}</p>
            <p class="text-xs text-warning-600 dark:text-warning-400 mt-1">Issued</p>
        </div>
        <div class="rounded-xl bg-danger-50 dark:bg-danger-900/30 border border-danger-200 dark:border-danger-800 p-4 text-center">
            <p class="text-2xl font-bold text-danger-700 dark:text-danger-300">{{ $void }}</p>
            <p class="text-xs text-danger-600 dark:text-danger-400 mt-1">Void</p>
        </div>
    </div>

    {{-- Distribution bar --}}
    @if ($total > 0)
        <div>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Distribution</p>
            <div class="flex h-4 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                @if ($pctAvail > 0)
                    <div class="bg-success-500" style="width: {{ $pctAvail }}%" title="Available: {{ $pctAvail }}%"></div>
                @endif
                @if ($pctIssued > 0)
                    <div class="bg-warning-500" style="width: {{ $pctIssued }}%" title="Issued: {{ $pctIssued }}%"></div>
                @endif
                @if ($pctVoid > 0)
                    <div class="bg-danger-400" style="width: {{ $pctVoid }}%" title="Void: {{ $pctVoid }}%"></div>
                @endif
            </div>
            <div class="flex gap-4 mt-2 text-xs text-gray-500">
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full bg-success-500"></span>Available {{ $pctAvail }}%</span>
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full bg-warning-500"></span>Issued {{ $pctIssued }}%</span>
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full bg-danger-400"></span>Void {{ $pctVoid }}%</span>
            </div>
        </div>
    @else
        <p class="text-sm text-gray-400 text-center py-4">No badge codes generated yet.</p>
    @endif

</div>