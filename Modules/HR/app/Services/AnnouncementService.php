<?php

namespace Modules\HR\Services;

use Modules\HR\Models\Announcement;
use Modules\HR\Models\AnnouncementRead;
use Modules\HR\Models\Employee;

class AnnouncementService
{
    /**
     * Publish an announcement immediately.
     */
    public function publish(Announcement $announcement): Announcement
    {
        $announcement->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

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
     * Send announcement via CommunicationCentre (stub).
     */
    public function sendViaCommunicationCentre(Announcement $announcement): void
    {
        // TODO: integrate with CommunicationCentre module to send emails/SMS
        // using CommTemplate and CommunicationService
    }
}