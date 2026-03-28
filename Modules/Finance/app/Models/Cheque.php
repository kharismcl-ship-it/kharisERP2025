<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $table = 'fin_cheques';

    protected $fillable = [
        'company_id',
        'cheque_book_id',
        'bank_account_id',
        'cheque_number',
        'payee_name',
        'amount',
        'cheque_date',
        'payment_id',
        'status',
        'cleared_date',
        'return_reason',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'cheque_date'  => 'date',
        'cleared_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function chequeBook()
    {
        return $this->belongsTo(ChequeBook::class, 'cheque_book_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}