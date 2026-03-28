<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Employee;

class PettyCashFund extends Model
{
    use HasFactory;

    protected $table = 'fin_petty_cash_funds';

    protected $fillable = [
        'company_id',
        'name',
        'custodian_employee_id',
        'float_amount',
        'current_balance',
        'gl_account_id',
        'is_active',
    ];

    protected $casts = [
        'float_amount'    => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function custodian()
    {
        return $this->belongsTo(Employee::class, 'custodian_employee_id');
    }

    public function transactions()
    {
        return $this->hasMany(PettyCashTransaction::class, 'fund_id');
    }

    public function glAccount()
    {
        return $this->belongsTo(Account::class, 'gl_account_id');
    }
}