<?php

namespace Modules\Farms\Services;

use Modules\Farms\Models\FarmSmsCommand;
use Modules\Farms\Models\FarmUssdSession;
use Modules\Farms\Models\FarmTask;
use Modules\Farms\Models\FarmWorker;
use Modules\Farms\Models\FarmWorkerAttendance;

class FarmUssdService
{
    /**
     * Handle a USSD request from a telecom provider webhook.
     * Compatible with Africa's Talking USSD API.
     */
    public function handle(string $sessionId, string $phoneNumber, string $text): string
    {
        $worker = FarmWorker::whereHas('employee', fn ($q) => $q->where('phone', $phoneNumber))
            ->first();

        $session = FarmUssdSession::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'phone_number'   => $phoneNumber,
                'farm_worker_id' => $worker?->id,
                'company_id'     => $worker?->company_id,
                'session_data'   => [],
            ]
        );
        $session->update(['last_activity_at' => now()]);

        $parts = array_filter(explode('*', $text), fn ($v) => $v !== '');
        $level = count($parts);

        // Level 0 — Main menu
        if ($level === 0) {
            return "CON Welcome to FarmERP\n1. View My Tasks\n2. Report Attendance\n3. Submit Daily Report\n4. Check Weather\n0. Exit";
        }

        $choice = (string) last($parts);

        // Main menu selections
        if ($level === 1) {
            return match ($choice) {
                '1'     => $this->getTaskMenu($worker),
                '2'     => $this->recordAttendance($worker),
                '3'     => "CON Daily Report\nEnter summary of activities:\n(Type and press send)",
                '4'     => $this->getWeatherSummary($worker),
                '0'     => "END Thank you. Goodbye.",
                default => "CON Invalid option.\n1. Tasks\n2. Attendance\n3. Report\n4. Weather\n0. Exit",
            };
        }

        // Level 2 — Sub-menu responses
        if ($level === 2 && isset(array_values($parts)[0]) && array_values($parts)[0] === '3') {
            if ($worker) {
                FarmSmsCommand::create([
                    'phone_number'    => $phoneNumber,
                    'farm_worker_id'  => $worker->id,
                    'company_id'      => $worker->company_id,
                    'raw_message'     => $choice,
                    'command_type'    => 'REPORT',
                    'parsed_data'     => ['summary' => $choice, 'date' => today()->toDateString()],
                    'status'          => 'processed',
                    'processed_at'    => now(),
                ]);
            }
            return "END Report received. Thank you!";
        }

        return "END Session ended.";
    }

    private function getTaskMenu(?FarmWorker $worker): string
    {
        if (!$worker) {
            return "END Worker not found for this number.";
        }

        $tasks = FarmTask::where('assigned_to_worker_id', $worker->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date')
            ->limit(3)
            ->get(['title', 'due_date']);

        if ($tasks->isEmpty()) {
            return "END No pending tasks assigned.";
        }

        $menu = "END Your tasks:\n";
        foreach ($tasks as $i => $task) {
            $menu .= ($i + 1) . ". {$task->title} (Due: {$task->due_date?->format('d/m')})\n";
        }

        return $menu;
    }

    private function recordAttendance(?FarmWorker $worker): string
    {
        if (!$worker) {
            return "END Worker not found.";
        }

        $existing = FarmWorkerAttendance::where('farm_worker_id', $worker->id)
            ->where('attendance_date', today())
            ->first();

        if ($existing) {
            return "END Attendance already recorded today ({$existing->status}).";
        }

        FarmWorkerAttendance::create([
            'company_id'      => $worker->company_id,
            'farm_id'         => $worker->farm_id,
            'farm_worker_id'  => $worker->id,
            'attendance_date' => today(),
            'status'          => 'present',
        ]);

        return "END Attendance recorded: PRESENT for " . today()->format('d M Y');
    }

    private function getWeatherSummary(?FarmWorker $worker): string
    {
        if (!$worker) {
            return "END Worker not found.";
        }

        $log = \Modules\Farms\Models\FarmWeatherLog::where('farm_id', $worker->farm_id)
            ->orderByDesc('created_at')
            ->first();

        if (!$log) {
            return "END No weather data available.";
        }

        $temp     = $log->max_temp_c ?? $log->min_temp_c ?? 'N/A';
        $humidity = $log->humidity_pct ?? 'N/A';
        $cond     = $log->weather_condition ?? 'N/A';

        return "END Weather for {$log->farm?->name}:\nTemp: {$temp}°C\nHumidity: {$humidity}%\nCondition: {$cond}";
    }
}