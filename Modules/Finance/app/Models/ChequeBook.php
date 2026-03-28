<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChequeBook extends Model
{
    use HasFactory;

    protected $table = 'fin_cheque_books';

    protected $fillable = [
        'company_id',
        'bank_account_id',
        'series_from',
        'series_to',
        'current_leaf',
        'is_exhausted',
    ];

    protected $casts = [
        'is_exhausted' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function cheques()
    {
        return $this->hasMany(Cheque::class, 'cheque_book_id');
    }
}