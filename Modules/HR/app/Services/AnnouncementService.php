<?php

namespace Modules\HR\Services;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\HR\Models\Announcement;
use Modules\HR\Models\AnnouncementRead;
use Modules\HR\Models\Employee;

class AnnouncementService
{
    /**
     * Publish an announcement immediately and dispatch email/SMS if configured.
     */
    public function publish(Announcement $announcement): Announcement
    {
        $announcement->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        if ($announcement->send_email || $announcement->send_sms) {
            $this->sendViaCommunicationCentre($announcement);
        }

        return $announcement;
    }

    /**
     * Unpublish an announcement.
     */
    public function unpublish(Announcement $announcement): Announcement
    {
        $announcement->update(['is_published' => false]);

        return $announcement;
    }

    /**
     * Mark an announcement as read by an employee.
     */
    public function markAsRead(Announcement $announcement, Employee $employee): AnnouncementRead
    {
        return AnnouncementRead::firstOrCreate(
            ['announcement_id' => $announcement->id, 'employee_id' => $employee->id],
            ['read_at' => now()]
        );
    }

    /**
     * Get announcements relevant to an employee (based on target_audience).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForEmployee(Employee $employee)
    {
        return Announcement::query()
            ->where('company_id', $employee->company_id)
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) use ($employee) {
                $q->where('target_audience', 'all')
                  ->orWhere(function ($q2) use ($employee) {
                      $q2->where('target_audience', 'department')
                         ->where('department_id', $employee->department_id);
                  })
                  ->orWhere(function ($q2) use ($employee) {
                      $q2->where('target_audience', 'job_position')
                         ->where('job_position_id', $employee->job_position_id);
                  });
            })
            ->orderByDesc('published_at')
            ->get();
    }

    /**
     * Get read counts for an announcement.
     */
    public function getReadCount(Announcement $announcement): int
    {
        return AnnouncementRead::where('announcement_id', $announcement->id)->count();
    }

    /**
     * Send announcement via CommunicationCentre (email and/or SMS).
     * Resolves target employees based on target_audience and dispatches messages.
     */
    public function sendViaCommunicationCentre(Announcement $announcement): void
    {
        $recipients = $this->resolveRecipients($announcement);

        if ($recipients->isEmpty()) {
            return;
        }

        try {
            $commService = app(CommunicationService::class);
        } catch (\Throwable $e) {
            Log::warning('AnnouncementService: CommunicationCentre not available — skipping dispatch.', [
                'announcement_id' => $announcement->id,
                'error'           => $e->getMessage(),
            ]);
            return;
        }

        $subject = "[{$announcement->priority_label}] {$announcement->title}";
        $body    = strip_tags($announcement->content);

        foreach ($recipients as $employee) {
            try {
                if ($announcement->send_email && $employee->email) {
                    $commService->sendRawEmail(
                        $employee->email,
                        $employee->full_name,
                        $subject,
                        $announcement->content,
                    );
                }

                if ($announcement->send_sms && $employee->phone) {
                    $commService->sendRaw(
                        'sms',
                        $employee->phone,
                        null,
                        "{$subject}: {$body}",
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('AnnouncementService: Failed to dispatch to employee.', [
                    'employee_id'     => $employee->id,
                    'announcement_id' => $announcement->id,
                    'error'           => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Resolve the list of employees who should receive this announcement.
     */
    private function resolveRecipients(Announcement $announcement): \Illuminate\Database\Eloquent\Collection
    {
        $query = Employee::where('company_id', $announcement->company_id)
            ->where('employment_status', 'active');

        switch ($announcement->target_audience) {
            case 'department':
                $query->where('department_id', $announcement->target_department_id);
                break;
            case 'job_position':
                $query->where('job_position_id', $announcement->target_job_position_id);
                break;
            // 'all' — no additional filter
        }

        return $query->get();
    }
}