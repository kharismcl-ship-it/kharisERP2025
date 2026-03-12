<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Tasks</h1>
                <p class="text-sm text-gray-500">{{ $farm->name }}</p>
            </div>
            <button
                wire:click="$set('showCreateModal', true)"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700"
            >
                New Task
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 flex flex-wrap gap-3">
            <select wire:model.live="statusFilter" class="rounded-md border-gray-300 text-sm">
                <option value="open">Open</option>
                <option value="overdue">Overdue</option>
                <option value="completed">Completed</option>
                <option value="">All</option>
            </select>
            <select wire:model.live="priorityFilter" class="rounded-md border-gray-300 text-sm">
                <option value="">All Priorities</option>
                <option value="urgent">Urgent</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
            <select wire:model.live="workerFilter" class="rounded-md border-gray-300 text-sm">
                <option value="">All Workers</option>
                @foreach($workers as $worker)
                    <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Task List -->
        <div class="space-y-3">
            @forelse($tasks as $task)
                @php
                    $isOverdue = !$task->completed_at && $task->due_date && $task->due_date->isPast();
                @endphp
                <div class="bg-white rounded-lg shadow p-4 flex items-start gap-4
                    @if($isOverdue) border-l-4 border-red-400 @endif">

                    <!-- Complete checkbox -->
                    @if(!$task->completed_at)
                        <button
                            wire:click="markComplete({{ $task->id }})"
                            class="mt-0.5 w-5 h-5 rounded border-2 border-gray-300 hover:border-green-500 flex-shrink-0"
                            title="Mark complete"
                        ></button>
                    @else
                        <div class="mt-0.5 w-5 h-5 rounded bg-green-500 flex-shrink-0 flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-medium text-gray-900 {{ $task->completed_at ? 'line-through text-gray-400' : '' }}">
                                {{ $task->title }}
                            </h3>
                            <div class="flex gap-2 flex-shrink-0">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                    @if($task->priority === 'urgent') bg-red-100 text-red-800
                                    @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-1 text-xs text-gray-500">
                            <span>{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</span>
                            @if($task->due_date)
                                <span class="{{ $isOverdue ? 'text-red-600 font-medium' : '' }}">
                                    Due {{ $task->due_date->format('M j, Y') }}
                                    @if($isOverdue) (overdue) @endif
                                </span>
                            @endif
                            @if($task->assignedWorker)
                                <span>Assigned: {{ $task->assignedWorker->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-12 text-center text-gray-400">
                    No tasks found.
                </div>
            @endforelse
        </div>

        <div>{{ $tasks->links() }}</div>

    </div>

    <!-- Create Task Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold mb-4">New Task</h3>

                <form wire:submit="saveTask" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" wire:model="title" class="w-full rounded-md border-gray-300 text-sm" />
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                            <select wire:model="taskType" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="other">Other</option>
                                <option value="weeding">Weeding</option>
                                <option value="spraying">Spraying</option>
                                <option value="harvesting">Harvesting</option>
                                <option value="irrigation">Irrigation</option>
                                <option value="feeding">Feeding</option>
                                <option value="health_check">Health Check</option>
                                <option value="planting">Planting</option>
                                <option value="scouting">Scouting</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                            <select wire:model="priority" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                            <input type="date" wire:model="dueDate" class="w-full rounded-md border-gray-300 text-sm" />
                            @error('dueDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assign to</label>
                            <select wire:model="assignedToWorkerId" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="">Unassigned</option>
                                @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end mt-2">
                        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                            Create Task
                        </button>
                        <button type="button" wire:click="$set('showCreateModal', false)"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
