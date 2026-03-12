<?php

namespace Modules\Farms\Http\Livewire\Tasks;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmTask;
use Modules\Farms\Models\FarmWorker;

class Index extends Component
{
    use WithPagination;

    public Farm $farm;

    public string $statusFilter = 'open';

    public string $priorityFilter = '';

    public string $workerFilter = '';

    // Create modal
    public bool $showCreateModal = false;

    public string $title = '';

    public string $taskType = 'other';

    public string $priority = 'medium';

    public string $dueDate = '';

    public ?int $assignedToWorkerId = null;

    public function mount(Farm $farm): void
    {
        $this->farm = $farm;
        $this->dueDate = now()->addDay()->format('Y-m-d');
    }

    public function getTasksProperty()
    {
        $query = FarmTask::with(['assignedWorker'])
            ->where('farm_id', $this->farm->id);

        if ($this->statusFilter === 'open') {
            $query->whereNull('completed_at');
        } elseif ($this->statusFilter === 'completed') {
            $query->whereNotNull('completed_at');
        } elseif ($this->statusFilter === 'overdue') {
            $query->whereNull('completed_at')
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now());
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        if ($this->workerFilter) {
            $query->where('assigned_to_worker_id', $this->workerFilter);
        }

        return $query->orderBy('due_date')->paginate(20);
    }

    public function getWorkersProperty()
    {
        return FarmWorker::where('farm_id', $this->farm->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function markComplete(int $taskId): void
    {
        $task = FarmTask::findOrFail($taskId);

        $task->update(['completed_at' => now()]);

        $this->dispatch('notify', type: 'success', message: "Task \"{$task->title}\" marked complete.");
    }

    protected function rules(): array
    {
        return [
            'title'    => 'required|string|max:255',
            'taskType' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'dueDate'  => 'required|date',
        ];
    }

    public function saveTask(): void
    {
        $this->validate();

        FarmTask::create([
            'farm_id'                => $this->farm->id,
            'company_id'             => $this->farm->company_id,
            'title'                  => $this->title,
            'task_type'              => $this->taskType,
            'priority'               => $this->priority,
            'due_date'               => $this->dueDate,
            'assigned_to_worker_id'  => $this->assignedToWorkerId ?: null,
        ]);

        $this->showCreateModal = false;
        $this->title = '';
        $this->taskType = 'other';
        $this->priority = 'medium';
        $this->dueDate = now()->addDay()->format('Y-m-d');
        $this->assignedToWorkerId = null;

        $this->dispatch('notify', type: 'success', message: 'Task created.');
    }

    public function render()
    {
        return view('farms::livewire.tasks.index', [
            'tasks'   => $this->tasks,
            'workers' => $this->workers,
        ])->layout('farms::layouts.app');
    }
}
