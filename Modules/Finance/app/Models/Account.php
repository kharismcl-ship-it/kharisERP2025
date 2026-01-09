<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'type',
        'parent_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        //
    ];

    /**
     * Get the company that owns this account.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the parent account.
     */
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Get the child accounts.
     */
    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Get the journal lines for this account.
     */
    public function journalLines()
    {
        return $this->hasMany(JournalLine::class, 'account_id');
    }
}
