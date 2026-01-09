<?php

namespace Modules\PaymentsChannel\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayMethod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'provider',
        'channel',
        'payment_mode',
        'currency',
        'is_active',
        'sort_order',
        'config',
        'offline_payment_instruction',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'payment_mode' => 'string',
    ];

    /**
     * Get the company that owns this payment method.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the payment intents that used this payment method.
     */
    public function payIntents()
    {
        return $this->hasMany(PayIntent::class, 'pay_method_id');
    }

    // Create automatic code on creating only if not provided
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = strtolower($model->channel);
            }
        });
    }
}
