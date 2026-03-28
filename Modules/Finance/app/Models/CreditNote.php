<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use HasFactory;

    protected $table = 'fin_credit_notes';

    protected $fillable = [
        'company_id',
        'customer_name',
        'customer_type',
        'customer_id_ref',
        'invoice_id',
        'credit_note_number',
        'issue_date',
        'reason',
        'sub_total',
        'tax_total',
        'total',
        'status',
        'applied_amount',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'issue_date'     => 'date',
        'sub_total'      => 'decimal:2',
        'tax_total'      => 'decimal:2',
        'total'          => 'decimal:2',
        'applied_amount' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->credit_note_number)) {
                $count = static::where('company_id', $model->company_id)->count() + 1;
                $model->credit_note_number = 'CN-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function lines()
    {
        return $this->hasMany(CreditNoteLine::class, 'credit_note_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function remainingBalance(): float
    {
        return (float) $this->total - (float) $this->applied_amount;
    }
}