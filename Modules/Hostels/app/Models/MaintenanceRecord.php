<?php

namespace Modules\Hostels\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Hostels\Enums\MaintenanceOutcome;
use Modules\Hostels\Enums\MaintenancePriority;
use Modules\Hostels\Enums\MaintenanceStatus;
use Modules\Hostels\Enums\MaintenanceType;
use Modules\HR\Models\Employee;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $table = 'inventory_maintenance_records';

    protected $fillable = [
        'hostel_occupant_id',
        'inventory_item_id',
        'room_assignment_id',
        'assigned_to',
        'maintenance_type',
        'priority',
        'status',
        'scheduled_date',
        'started_at',
        'completed_at',
        'description',
        'issue_details',
        'work_performed',
        'parts_used',
        'labor_cost',
        'parts_cost',
        'total_cost',
        'outcome',
        'notes',
        'follow_up_required',
        'next_maintenance_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'next_maintenance_date' => 'datetime',
        'labor_cost' => 'decimal:2',
        'parts_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'parts_used' => 'array',
        'priority' => MaintenancePriority::class,
        'status' => MaintenanceStatus::class,
        'maintenance_type' => MaintenanceType::class,
        'outcome' => MaintenanceOutcome::class,
    ];

    /**
     * Get the inventory item that this maintenance record belongs to.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(HostelInventoryItem::class);
    }

    /**
     * Get the room assignment that this maintenance record belongs to.
     */
    public function roomAssignment(): BelongsTo
    {
        return $this->belongsTo(RoomInventoryAssignment::class);
    }

    /**
     * Get the staff member assigned to this maintenance.
     */
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    /**
     * Get the user who created this maintenance record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this maintenance record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the procurement items used in this maintenance.
     */
    public function procurementItems(): BelongsToMany
    {
        return $this->belongsToMany(
            \Modules\ProcurementInventory\Models\Item::class,
            'maintenance_procurement_items',
            'maintenance_record_id',
            'procurement_item_id'
        )->withPivot(['quantity_used', 'unit_cost', 'total_cost', 'notes']);
    }

    /**
     * Scope a query to only include pending maintenance records.
     */
    public function scopePending($query)
    {
        return $query->where('status', MaintenanceStatus::PENDING->value);
    }

    /**
     * Scope a query to only include in-progress maintenance records.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', MaintenanceStatus::IN_PROGRESS->value);
    }

    /**
     * Scope a query to only include completed maintenance records.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', MaintenanceStatus::COMPLETED->value);
    }

    /**
     * Scope a query to only include high priority maintenance records.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', MaintenancePriority::HIGH->value);
    }

    /**
     * Scope a query to only include critical priority maintenance records.
     */
    public function scopeCriticalPriority($query)
    {
        return $query->where('priority', MaintenancePriority::CRITICAL->value);
    }

    /**
     * Scope a query to only include overdue maintenance records.
     */
    public function scopeOverdue($query)
    {
        return $query->where('scheduled_date', '<', now())
            ->whereIn('status', [MaintenanceStatus::PENDING->value, MaintenanceStatus::IN_PROGRESS->value]);
    }

    /**
     * Check if the maintenance record is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->scheduled_date &&
               $this->scheduled_date->isPast() &&
               in_array($this->status, [MaintenanceStatus::PENDING->value, MaintenanceStatus::IN_PROGRESS->value]);
    }

    /**
     * Calculate the total cost of maintenance.
     */
    public function calculateTotalCost(): void
    {
        $this->total_cost = $this->labor_cost + $this->parts_cost;
    }

    /**
     * Mark maintenance as started.
     */
    public function markAsStarted(): void
    {
        $this->status = MaintenanceStatus::IN_PROGRESS->value;
        $this->started_at = now();
        $this->save();
    }

    /**
     * Mark maintenance as completed.
     */
    public function markAsCompleted(?string $outcome = null, ?string $workPerformed = null): void
    {
        $this->status = MaintenanceStatus::COMPLETED->value;
        $this->completed_at = now();
        $this->outcome = $outcome ?? $this->outcome;
        $this->work_performed = $workPerformed ?? $this->work_performed;
        $this->save();
    }

    /**
     * Add parts used during maintenance.
     */
    public function addPartsUsed(array $parts): void
    {
        $currentParts = $this->parts_used ?? [];
        $this->parts_used = array_merge($currentParts, $parts);
        $this->save();
    }
}
