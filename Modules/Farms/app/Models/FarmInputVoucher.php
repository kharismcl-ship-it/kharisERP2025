<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmInputVoucher extends Model
{
    protected $table = 'farm_input_vouchers';

    protected $fillable = [
        'company_id', 'farm_input_credit_account_id', 'farm_id', 'voucher_code',
        'beneficiary_name', 'beneficiary_phone', 'voucher_type', 'input_item',
        'face_value', 'redeemed_value', 'redeemed_at_supplier', 'issued_date',
        'expiry_date', 'status', 'verification_pin', 'redeemed_at', 'notes',
    ];

    protected $casts = [
        'issued_date'    => 'date',
        'expiry_date'    => 'date',
        'redeemed_at'    => 'datetime',
        'face_value'     => 'float',
        'redeemed_value' => 'float',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(FarmInputCreditAccount::class, 'farm_input_credit_account_id');
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $model) {
            if (empty($model->voucher_code)) {
                $yearMonth = now()->format('Ym');
                $count = static::where('company_id', $model->company_id)
                    ->whereRaw('voucher_code LIKE ?', ["VCH-{$yearMonth}-%"])
                    ->count() + 1;
                $model->voucher_code = "VCH-{$yearMonth}-" . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
            if (empty($model->verification_pin)) {
                $model->verification_pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            }
        });
    }
}