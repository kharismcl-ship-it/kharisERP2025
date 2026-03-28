<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceReminderRule extends Model
{
    use HasFactory;

    protected $table = 'fin_invoice_reminder_rules';

    protected $fillable = [
        'company_id',
        'name',
        'days_offset',
        'template',
        'is_active',
    ];

    protected $casts = [
        'days_offset' => 'integer',
        'is_active'   => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}