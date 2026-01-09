<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'name',
        'category',
        'description',
        'sku',
        'unit_cost',
        'current_stock',
        'min_stock_level',
        'max_stock_level',
        'uom',
        'status',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function transactions()
    {
        return $this->hasMany(HostelInventoryTransaction::class);
    }

    public function scopeLowStock($query)
    {
        return $query->where('current_stock', '<=', 'min_stock_level')
            ->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the room assignments for the inventory item.
     */
    public function roomAssignments()
    {
        return $this->hasMany(RoomInventoryAssignment::class, 'inventory_item_id');
    }

    /**
     * Get the active room assignments for the inventory item.
     */
    public function activeRoomAssignments()
    {
        return $this->roomAssignments()->active();
    }

    public function isLowStock()
    {
        return $this->current_stock <= $this->min_stock_level;
    }

    public function increaseStock($quantity, $notes = null)
    {
        $this->current_stock += $quantity;
        $this->save();

        // Create transaction record
        HostelInventoryTransaction::create([
            'hostel_id' => $this->hostel_id,
            'inventory_item_id' => $this->id,
            'transaction_type' => 'receipt',
            'quantity' => $quantity,
            'balance_after' => $this->current_stock,
            'notes' => $notes,
            'transaction_date' => now(),
        ]);
    }

    public function decreaseStock($quantity, $notes = null, $roomId = null)
    {
        if ($this->current_stock < $quantity) {
            throw new \Exception("Insufficient stock for {$this->name}");
        }

        $this->current_stock -= $quantity;
        $this->save();

        // Create transaction record
        HostelInventoryTransaction::create([
            'hostel_id' => $this->hostel_id,
            'inventory_item_id' => $this->id,
            'room_id' => $roomId,
            'transaction_type' => 'issue',
            'quantity' => $quantity,
            'balance_after' => $this->current_stock,
            'notes' => $notes,
            'transaction_date' => now(),
        ]);
    }

    /**
     * Get the maintenance records for this inventory item.
     */
    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class, 'inventory_item_id');
    }

    /**
     * Get the pending maintenance records for this inventory item.
     */
    public function pendingMaintenance()
    {
        return $this->maintenanceRecords()->pending();
    }

    /**
     * Get the completed maintenance records for this inventory item.
     */
    public function completedMaintenance()
    {
        return $this->maintenanceRecords()->completed();
    }

    /**
     * Check if this inventory item has any pending maintenance.
     */
    public function hasPendingMaintenance(): bool
    {
        return $this->pendingMaintenance()->exists();
    }
}
