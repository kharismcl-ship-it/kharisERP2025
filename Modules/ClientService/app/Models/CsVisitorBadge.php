<?php

namespace Modules\ClientService\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CsVisitorBadge extends Model
{
    use SoftDeletes;

    protected $table = 'cs_visitor_badges';

    protected $fillable = [
        'company_id',
        'prefix',
        'badge_code',
        'status',
        'batch_number',
        'issued_to_visitor_id',
        'issued_at',
        'issued_by_user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function issuedToVisitor()
    {
        return $this->belongsTo(CsVisitor::class, 'issued_to_visitor_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // ── State helpers ──────────────────────────────────────────────

    public function isAvailable(): bool { return $this->status === 'available'; }
    public function isIssued(): bool    { return $this->status === 'issued'; }
    public function isVoid(): bool      { return $this->status === 'void'; }

    // ── Operations ─────────────────────────────────────────────────

    public function issueToVisitor(CsVisitor $visitor, ?int $issuedByUserId = null): void
    {
        if (! $this->isAvailable()) {
            throw new \Exception("Badge {$this->badge_code} is not available (status: {$this->status}).");
        }

        $this->update([
            'status'               => 'issued',
            'issued_to_visitor_id' => $visitor->id,
            'issued_at'            => now(),
            'issued_by_user_id'    => $issuedByUserId ?? auth()->id(),
        ]);

        $visitor->update(['badge_number' => $this->badge_code]);
    }

    public function revokeFromVisitor(?string $reason = null): void
    {
        if (! $this->isIssued()) {
            return;
        }

        $this->update([
            'status'               => 'available',
            'issued_to_visitor_id' => null,
            'issued_at'            => null,
            'issued_by_user_id'    => null,
            'notes'                => $reason,
        ]);
    }

    public function voidBadge(string $reason): void
    {
        $this->update([
            'status' => 'void',
            'notes'  => $reason,
        ]);
    }

    // ── Batch generation ───────────────────────────────────────────

    /**
     * Generate sequential badge codes for a company using its prefix.
     * e.g. prefix "KH" → KH-0001, KH-0002 …
     */
    public static function generateBatch(
        string $batchNumber,
        int    $quantity,
        string $prefix     = 'VB',
        ?int   $companyId  = null,
    ): array {
        $dashPrefix = strtoupper($prefix) . '-';

        $lastCode = static::withTrashed()
            ->where('badge_code', 'like', $dashPrefix . '%')
            ->orderByDesc('id')
            ->first();

        $startNumber = $lastCode
            ? ((int) substr($lastCode->badge_code, strlen($dashPrefix))) + 1
            : 1;

        $max = 9999;
        if (($startNumber + $quantity - 1) > $max) {
            $available = $max - $startNumber + 1;
            throw new \Exception(
                "Cannot generate {$quantity} badges for prefix {$dashPrefix}. Only {$available} slots remain."
            );
        }

        $codes = [];
        for ($i = 0; $i < $quantity; $i++) {
            $code = $dashPrefix . str_pad($startNumber + $i, 4, '0', STR_PAD_LEFT);

            if (! static::withTrashed()->where('badge_code', $code)->exists()) {
                $codes[] = static::create([
                    'company_id'   => $companyId,
                    'prefix'       => strtoupper($prefix),
                    'badge_code'   => $code,
                    'status'       => 'available',
                    'batch_number' => $batchNumber,
                ]);
            }
        }

        return $codes;
    }

    public static function nextBatchId(string $prefix = 'VB'): string
    {
        $year  = now()->year;
        $month = now()->format('m');
        $tag   = strtoupper($prefix) . "-BATCH-{$year}-{$month}-";
        $count = static::where('batch_number', 'like', $tag . '%')
            ->distinct('batch_number')
            ->count('batch_number');

        return $tag . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }
}
