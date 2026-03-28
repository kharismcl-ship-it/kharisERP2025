<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bom extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'procurement_boms';

    protected $fillable = [
        'company_id',
        'item_id',
        'name',
        'version',
        'unit_of_measure',
        'quantity_produced',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'quantity_produced' => 'decimal:4',
        'is_active'         => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(BomLine::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Explode BOM for a given production quantity.
     * Returns collection of ['component_item' => Item, 'required_qty' => float].
     */
    public function explode(float $quantity = 1): Collection
    {
        $qtyProduced = (float) $this->quantity_produced;
        if ($qtyProduced <= 0) {
            $qtyProduced = 1;
        }

        $multiplier = $quantity / $qtyProduced;

        return $this->lines->load('componentItem')->map(function (BomLine $line) use ($multiplier) {
            return [
                'component_item' => $line->componentItem,
                'required_qty'   => $line->effectiveQuantity() * $multiplier,
            ];
        });
    }

    /**
     * Generate procurement requirement rows for a given production quantity.
     */
    public function generateProcurementRequirements(float $qty): array
    {
        return $this->explode($qty)->map(fn ($row) => [
            'item_id'      => $row['component_item']?->id,
            'item_name'    => $row['component_item']?->name,
            'quantity'     => $row['required_qty'],
            'unit_of_measure' => $row['component_item']?->unit_of_measure,
        ])->toArray();
    }
}