<?php

namespace Modules\Farms\Http\Livewire\Attendance;

use Livewire\Component;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmWorker;
use Modules\Farms\Models\FarmWorkerAttendance;

class Index extends Component
{
    public Farm $farm;

    public string $attendanceDate;

    public array $entries = [];

    public bool $saved = false;

    public function mount(Farm $farm): void
    {
        $this->farm = $farm;
        $this->attendanceDate = now()->format('Y-m-d');
        $this->loadWorkers();
    }

    public function updatedAttendanceDate(): void
    {
        $this->loadWorkers();
        $this->saved = false;
    }

    protected function loadWorkers(): void
    {
        $workers = FarmWorker::where('farm_id', $this->farm->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Load any existing attendance for the date
        $existing = FarmWorkerAttendance::where('farm_id', $this->farm->id)
            ->where('attendance_date', $this->attendanceDate)
            ->get()
            ->keyBy('farm_worker_id');

        $this->entries = $workers->map(function ($worker) use ($existing) {
            $record = $existing->get($worker->id);
            return [
                'worker_id'   => $worker->id,
                'worker_name' => $worker->name,
                'status'      => $record ? $record->status : 'present',
                'hours'       => $record ? $record->hours_worked : 8,
                'notes'       => $record ? ($record->notes ?? '') : '',
            ];
        })->toArray();
    }

    public function markAttendance(): void
    {
        foreach ($this->entries as $entry) {
            FarmWorkerAttendance::updateOrCreate(
                [
                    'farm_id'         => $this->farm->id,
                    'farm_worker_id'  => $entry['worker_id'],
                    'attendance_date' => $this->attendanceDate,
                ],
                [
                    'company_id'   => $this->farm->company_id,
                    'status'       => $entry['status'],
                    'hours_worked' => $entry['hours'] ?? null,
                    'notes'        => $entry['notes'] ?? null,
                ]
            );
        }

        $this->saved = true;
        $this->dispatch('notify', type: 'success', message: 'Attendance saved for ' . $this->attendanceDate);
    }

    public function render()
    {
        return view('farms::livewire.attendance.index')
            ->layout('farms::layouts.app');
    }
}
