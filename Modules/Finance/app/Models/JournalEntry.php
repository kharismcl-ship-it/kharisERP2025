<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class JournalEntry extends Model
{
    use HasFactory, BelongsToCompany;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'date',
        'reference',
        'description',
        'period_id',
        'is_locked',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date'      => 'date',
        'is_locked' => 'boolean',
    ];

    /**
     * Get the company that owns this journal entry.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the journal lines for this entry.
     */
    public function lines()
    {
        return $this->hasMany(JournalLine::class, 'journal_entry_id');
    }

    public function period()
    {
        return $this->belongsTo(AccountingPeriod::class, 'period_id');
    }
}
