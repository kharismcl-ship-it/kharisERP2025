<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmReturnRequest extends Model
{
    protected $table = 'farm_return_requests';

    protected $fillable = [
        'company_id',
        'farm_order_id',
        'reason',
        'description',
        'status',
        'admin_notes',
    ];

    const REASONS = [
        'damaged'       => 'Item Arrived Damaged',
        'wrong_item'    => 'Wrong Item Received',
        'not_delivered' => 'Item Not Delivered',
        'quality_issue' => 'Quality Below Standard',
        'other'         => 'Other',
    ];

    const STATUSES = ['pending', 'approved', 'rejected'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(FarmOrder::class, 'farm_order_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
