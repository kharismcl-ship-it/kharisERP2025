<?php

namespace Modules\HR\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\HR\Models\LeaveAttachment;
use Modules\HR\Models\LeaveRequest;

class LeaveAttachmentService
{
    /**
     * Save attachments for a leave request
     */
    public function saveAttachments(LeaveRequest $leaveRequest, array $attachmentsData): void
    {
        if (empty($attachmentsData)) {
            return;
        }

        $employeeId = Auth::user()->employee?->id;

        foreach ($attachmentsData as $attachmentData) {
            if (isset($attachmentData['file'])) {
                $filename = $attachmentData['file'];
                $path = 'leave-attachments/'.$filename;
                $disk = config('filament.default_filesystem_disk');

                LeaveAttachment::create([
                    'company_id' => $leaveRequest->company_id,
                    'leave_request_id' => $leaveRequest->id,
                    'uploaded_by_employee_id' => $employeeId,
                    'file_name' => $filename,
                    'original_name' => $attachmentData['original_name'] ?? $filename,
                    'mime_type' => Storage::disk($disk)->mimeType($path),
                    'size' => Storage::disk($disk)->size($path),
                    'disk' => $disk,
                    'path' => $path,
                    'description' => $attachmentData['description'] ?? null,
                    'is_private' => $attachmentData['is_private'] ?? false,
                ]);
            }
        }
    }

    /**
     * Delete an attachment
     */
    public function deleteAttachment(LeaveAttachment $attachment): bool
    {
        // Delete the file from storage
        Storage::disk($attachment->disk)->delete($attachment->path);

        // Delete the database record
        return $attachment->delete();
    }

    /**
     * Get attachment URL for viewing
     */
    public function getAttachmentUrl(LeaveAttachment $attachment): string
    {
        return Storage::disk($attachment->disk)->url($attachment->path);
    }

    /**
     * Get attachment download URL
     */
    public function getAttachmentDownloadUrl(LeaveAttachment $attachment): string
    {
        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name);
    }

    /**
     * Check if user can view attachment
     */
    public function canViewAttachment(LeaveAttachment $attachment): bool
    {
        $user = auth()->user();

        // HR and managers can view all attachments
        if ($user->hasRole(['hr', 'manager'])) {
            return true;
        }

        // Employees can only view their own non-private attachments
        if ($user->employee && $user->employee->id === $attachment->uploaded_by_employee_id) {
            return ! $attachment->is_private;
        }

        return false;
    }

    /**
     * Get attachments for leave request with access control
     */
    public function getAttachmentsForRequest(LeaveRequest $leaveRequest): array
    {
        $user = auth()->user();
        $attachments = $leaveRequest->attachments;

        return $attachments->filter(function ($attachment) use ($user) {
            if ($user->hasRole(['hr', 'manager'])) {
                return true;
            }

            if ($user->employee && $user->employee->id === $attachment->uploaded_by_employee_id) {
                return ! $attachment->is_private;
            }

            return false;
        })->values()->toArray();
    }
}
