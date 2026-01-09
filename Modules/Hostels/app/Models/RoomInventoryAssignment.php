<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Hostels\Enums\AssignmentStatus;

class RoomInventoryAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'inventory_item_id',
        'quantity',
        'notes',
        'assigned_at',
        'removed_at',
        'status',
        'condition_notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'removed_at' => 'datetime',
        'quantity' => 'integer',
        'status' => AssignmentStatus::class,
    ];

    /**
     * Get the room that owns the assignment.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the inventory item that owns the assignment.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(HostelInventoryItem::class, 'inventory_item_id');
    }

    /**
     * Scope a query to only include active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', AssignmentStatus::ACTIVE);
    }

    /**
     * Scope a query to only include assignments for a specific room.
     */
    public function scopeForRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    /**
     * Scope a query to only include assignments for a specific inventory item.
     */
    public function scopeForInventoryItem($query, $inventoryItemId)
    {
        return $query->where('inventory_item_id', $inventoryItemId);
    }

    /**
     * Get the maintenance records for this room assignment.
     */
    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class, 'room_assignment_id');
    }

    /**
     * Get the pending maintenance records for this room assignment.
     */
    public function pendingMaintenance()
    {
        return $this->maintenanceRecords()->pending();
    }

    /**
     * Get the completed maintenance records for this room assignment.
     */
    public function completedMaintenance()
    {
        return $this->maintenanceRecords()->completed();
    }

    /**
     * Check if this room assignment has any pending maintenance.
     */
    public function hasPendingMaintenance(): bool
    {
        return $this->pendingMaintenance()->exists();
    }

    /**
     * Mark this assignment as needing maintenance.
     */
    public function markAsNeedingMaintenance(string $issue, string $priority = 'medium'): MaintenanceRecord
    {
        // Update assignment status to maintenance
        $this->update(['status' => AssignmentStatus::MAINTENANCE]);

        return MaintenanceRecord::create([
            'room_assignment_id' => $this->id,
            'inventory_item_id' => $this->inventory_item_id,
            'issue_details' => $issue,
            'priority' => $priority,
            'status' => 'pending',
            'description' => 'Maintenance required for room assignment',
        ]);
    }

    /**
     * Check if the assignment is active.
     */
    public function isActive(): bool
    {
        return $this->status === AssignmentStatus::ACTIVE;
    }

    /**
     * Check if the assignment has problematic status (damaged, maintenance, lost).
     */
    public function isProblematic(): bool
    {
        return $this->status->isProblematic();
    }

    /**
     * Mark assignment as damaged with optional notes.
     */
    public function markAsDamaged(string $notes = ''): bool
    {
        return $this->update([
            'status' => AssignmentStatus::DAMAGED,
            'condition_notes' => $notes,
        ]);
    }

    /**
     * Mark assignment as lost.
     */
    public function markAsLost(): bool
    {
        return $this->update(['status' => AssignmentStatus::LOST]);
    }

    /**
     * Mark assignment as decommissioned.
     */
    public function markAsDecommissioned(): bool
    {
        return $this->update(['status' => AssignmentStatus::DECOMMISSIONED]);
    }

    /**
     * Reactivate a previously removed or problematic assignment.
     */
    public function reactivate(): bool
    {
        return $this->update([
            'status' => AssignmentStatus::ACTIVE,
            'removed_at' => null,
            'condition_notes' => null,
        ]);
    }

    /**
     * Remove assignment from room (soft removal).
     */
    public function removeFromRoom(): bool
    {
        return $this->update([
            'status' => AssignmentStatus::REMOVED,
            'removed_at' => now(),
        ]);
    }

    /**
     * Get the current condition status with label and color.
     */
    public function getConditionAttribute(): array
    {
        return [
            'label' => $this->status->label(),
            'color' => $this->status->color(),
            'value' => $this->status->value,
        ];
    }
}
