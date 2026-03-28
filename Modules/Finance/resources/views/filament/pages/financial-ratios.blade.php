<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Bar --}}
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Period & Date</h3>
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase mb-1 block">Period</label>
                    <select wire:model.live="period"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm">
                        <option value="mtd">Month to Date</option>
                        <option value="qtd">Quarter to Date</option>
                        <option value="ytd">Year to Date</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase mb-1 block">As Of</label>
                    <input type="date" wire:model.live="asOf"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        {{-- Ratio Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($ratios as $ratio)
                @php
                    $statusColor = match($ratio['status']) {
                        'good'    => 'green',
                        'warning' => 'amber',
                        'danger'  => 'red',
                        default   => 'gray',
                    };
                    $badgeBg = match($ratio['status']) {
                        'good'    => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                        'warning' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                        'danger'  => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                        default   => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                    };
                    $borderColor = match($ratio['status']) {
                        'good'    => 'border-l-green-500',
                        'warning' => 'border-l-amber-500',
                        'danger'  => 'border-l-red-500',
                        default   => 'border-l-gray-300',
                    };
                @endphp
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 border-l-4 {{ $borderColor }} p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $ratio['name'] }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                                @if($ratio['value'] !== null)
                                    @if($ratio['unit'] === ' GHS')
                                        GHS {{ number_format((float)$ratio['value'], 0) }}
                                    @else
                                        {{ is_float($ratio['value']) ? number_format($ratio['value'], 2) : $ratio['value'] }}{{ $ratio['unit'] }}
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xl">N/A</span>
                                @endif
                            </p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-medium {{ $badgeBg }}">
                            {{ ucfirst($ratio['status']) === 'Na' ? 'N/A' : ucfirst($ratio['status']) }}
                        </span>
                    </div>

                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 space-y-1">
                        <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-medium">Benchmark:</span>
                            <span>{{ $ratio['benchmark'] }}</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ratio['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="fi-section rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                <span class="font-medium text-gray-700 dark:text-gray-300">How to read:</span>
                <span class="inline-flex items-center gap-1 ml-2"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Within benchmark</span>
                <span class="inline-flex items-center gap-1 ml-2"><span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span> Near boundary</span>
                <span class="inline-flex items-center gap-1 ml-2"><span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span> Outside benchmark — requires attention</span>
                <span class="ml-2 italic">Ratios are computed from live journal entries. Accuracy improves with complete double-entry records.</span>
            </p>
        </div>
    </div>
</x-filament-panels::page>