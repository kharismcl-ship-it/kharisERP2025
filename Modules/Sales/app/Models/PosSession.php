<?php

namespace Modules\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosSession extends Model
{
    protected $fillable = [
        'terminal_id',
        'cashier_id',
        'opened_at',
        'closed_at',
        'opening_float',
        'closing_cash',
        'expected_cash',
        'cash_variance',
        'status',
        'notes',
    ];

    protected $casts = [
        'opened_at'     => 'datetime',
        'closed_at'     => 'datetime',
        'opening_float' => 'decimal:2',
        'closing_cash'  => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'cash_variance' => 'decimal:2',
    ];

    /**
     * Close the session, compute expected cash and variance.
     */
    public function close(float $closingCash, ?string $notes = null): void
    {
        $cashSales      = $this->sales()
            ->join('pos_payments', 'pos_payments.pos_sale_id', '=', 'pos_sales.id')
            ->where('pos_payments.method', 'cash')
            ->sum('pos_payments.amount');

        $expectedCash   = (float) $this->opening_float + $cashSales;
        $variance       = $closingCash - $expectedCash;

        $this->update([
            'closed_at'     => now(),
            'closing_cash'  => $closingCash,
            'expected_cash' => $expectedCash,
            'cash_variance' => $variance,
            'status'        => 'closed',
            'notes'         => $notes,
        ]);
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'terminal_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(PosSale::class, 'session_id');
    }
}