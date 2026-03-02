<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;

class LeaveAttachment extends Model
{
    use HasFactory, BelongsToCompany;

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_leave_attachments';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'leave_request_id',
        'uploaded_by_employee_id',
        'file_name',
        'original_name',
        'mime_type',
        'size',
        'disk',
        'path',
        'description',
        'is_private',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'size' => 'integer',
        'is_private' => 'boolean',
    ];

    /**
     * Get the readable file size.
     */
    public function getReadableSizeAttribute(): string
    {
        $size = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2).' '.$units[$i];
    }

    /**
     * Get the file extension.
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    /**
     * Get the company that owns the attachment.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the leave request that owns the attachment.
     */
    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    /**
     * Get the employee who uploaded the attachment.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by_employee_id');
    }

    /**
     * Check if the file is an image.
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if the file is a PDF.
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Check if the file is viewable in browser.
     */
    public function getIsViewableAttribute(): bool
    {
        return $this->is_image || $this->is_pdf || in_array($this->mime_type, [
            'text/plain',
            'application/json',
            'text/csv',
            'text/html',
        ]);
    }

    /**
     * Scope a query to only include public attachments.
     */
    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    /**
     * Scope a query to only include private attachments.
     */
    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }
}
