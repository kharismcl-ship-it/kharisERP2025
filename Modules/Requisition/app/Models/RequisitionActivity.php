<?php

namespace Modules\Requisition\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Employee;

class RequisitionActivity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'requisition_id',
        'user_id',
        'employee_id',
        'action',
        'from_status',
        'to_status',
        'description',
        'meta',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'meta'       => 'array',
            'created_at' => 'datetime',
        ];
    }

    const ACTION_LABELS = [
        'requisition_created'  => 'Request Created',
        'status_changed'       => 'Status Changed',
        'item_added'           => 'Item Added',
        'item_updated'         => 'Item Updated',
        'item_removed'         => 'Item Removed',
        'approver_added'       => 'Approver Added',
        'approver_decision'    => 'Approver Decision',
        'party_added'          => 'Party Notified',
        'attachment_uploaded'  => 'Document Uploaded',
        'attachment_removed'   => 'Document Removed',
        'revision_requested'   => 'Returned for Revision',
    ];

    const ACTION_COLORS = [
        'requisition_created'  => 'primary',
        'status_changed'       => 'warning',
        'item_added'           => 'success',
        'item_updated'         => 'info',
        'item_removed'         => 'danger',
        'approver_added'       => 'info',
        'approver_decision'    => 'warning',
        'party_added'          => 'gray',
        'attachment_uploaded'  => 'success',
        'attachment_removed'   => 'danger',
        'revision_requested'   => 'warning',
    ];

    /**
     * Convenience static log helper.
     */
    public static function log(
        Requisition $requisition,
        string $action,
        ?string $description = null,
        array $meta = [],
        ?string $fromStatus = null,
        ?string $toStatus = null,
    ): static {
        return static::create([
            'requisition_id' => $requisition->id,
            'user_id'        => auth()->id(),
            'action'         => $action,
            'from_status'    => $fromStatus,
            'to_status'      => $toStatus,
            'description'    => $description,
            'meta'           => empty($meta) ? null : $meta,
        ]);
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}