<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentBatch extends Model
{
    use HasFactory;

    protected $table = 'fin_payment_batches';

    protected $fillable = [
        'company_id',
        'batch_number',
        'batch_date',
        'payment_method',
        'bank_account_id',
        'total_amount',
        'status',
        'notes',
        'approved_by_user_id',
        'approved_at',
    ];

    protected $casts = [
        'batch_date'   => 'date',
        'total_amount' => 'decimal:2',
        'approved_at'  => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->batch_number)) {
                $count = static::where('company_id', $model->company_id)->count() + 1;
                $model->batch_number = 'PAY-BATCH-' . now()->format('Ym') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function items()
    {
        return $this->hasMany(PaymentBatchItem::class, 'batch_id');
    }
}