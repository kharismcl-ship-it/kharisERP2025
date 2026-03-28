<?php

declare(strict_types=1);

namespace Modules\Requisition\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionCustomFieldValue extends Model
{
    use HasFactory;

    protected $table = 'requisition_custom_field_values';

    protected $fillable = [
        'requisition_id',
        'custom_field_id',
        'value',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function customField()
    {
        return $this->belongsTo(RequisitionCustomField::class, 'custom_field_id');
    }
}