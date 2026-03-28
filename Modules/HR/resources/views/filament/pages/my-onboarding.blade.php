<x-filament-panels::page>
    <div class="space-y-4">
        @if(empty($tasks))
            <x-filament::card>
                <p class="text-center text-gray-500 py-6">No onboarding tasks have been assigned yet.</p>
            </x-filament::card>
        @else
            @php
                $total     = count($tasks);
                $completed = collect($tasks)->where('status', 'completed')->count();
                $pct       = $total > 0 ? round(($completed / $total) * 100) : 0;
            @endphp

            <x-filament::card>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Progress: {{ $completed }}/{{ $total }} tasks completed
                    </span>
                    <span class="text-sm font-semibold text-primary-600">{{ $pct }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                </div>
            </x-filament::card>

            <div class="space-y-3">
                @foreach($tasks as $task)
                    <x-filament::card>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 mt-1">
                                @if($task['status'] === 'completed')
                                    <x-heroicon-o-check-circle class="w-6 h-6 text-success-500"/>
                                @elseif($task['status'] === 'in_progress')
                                    <x-heroicon-o-clock class="w-6 h-6 text-warning-500"/>
                                @else
                                    <x-heroicon-o-ellipsis-horizontal-circle class="w-6 h-6 text-gray-400"/>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $task['title'] }}</h3>
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        @if($task['status'] === 'completed') bg-success-100 text-success-700
                                        @elseif($task['status'] === 'in_progress') bg-warning-100 text-warning-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ str($task['status'])->headline() }}
                                    </span>
                                </div>
                                @if($task['description'])
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $task['description'] }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">
                                    Due: Day {{ $task['due_days_from_hire'] }} &bull;
                                    Owner: {{ str($task['assignee_type'])->headline() }}
                                </p>
                            </div>
                            @if($task['status'] !== 'completed')
                                <div class="flex-shrink-0">
                                    <x-filament::button
                                        wire:click="complete({{ $task['id'] }})"
                                        size="sm"
                                        color="success"
                                    >
                                        Mark Complete
                                    </x-filament::button>
                                </div>
                            @endif
                        </div>
                    </x-filament::card>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>