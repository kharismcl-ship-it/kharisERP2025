<?php

namespace Modules\Construction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Company;
use EduardoRibeiroDev\FilamentLeaflet\Concerns\HasGeoJsonFile;
use Modules\Construction\Events\ProjectBudgetOverrun;
use Modules\Construction\Events\ProjectCompleted;
use Modules\PaymentsChannel\Traits\HasPayments;
use App\Models\Concerns\BelongsToCompany;

class ConstructionProject extends Model
{
    use HasPayments, BelongsToCompany, HasGeoJsonFile;

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'location',
        'latitude',
        'longitude',
        'client_name',
        'client_contact',
        'client_email',
        'client_phone',
        'project_manager',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'contract_value',
        'budget',
        'total_spent',
        'payment_status',
        'amount_paid',
        'invoice_id',
        'status',
        'notes',
        'geometry',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'expected_end_date' => 'date',
        'actual_end_date'   => 'date',
        'contract_value'    => 'decimal:2',
        'budget'            => 'decimal:2',
        'total_spent'       => 'decimal:2',
        'latitude'          => 'float',
        'longitude'         => 'float',
        'geometry'          => 'array',
    ];

    const STATUSES = ['planning', 'active', 'on_hold', 'completed', 'cancelled'];

    protected static function booted(): void
    {
        static::creating(function (self $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }
        });

        static::updated(function (self $project) {
            // Dispatch ProjectCompleted when status changes to completed
            if ($project->isDirty('status') && $project->status === 'completed') {
                ProjectCompleted::dispatch($project);
            }
            // Dispatch ProjectBudgetOverrun when total_spent exceeds budget
            if ($project->isDirty('total_spent') && $project->budget > 0) {
                $overrun = (float) $project->total_spent - (float) $project->budget;
                if ($overrun > 0) {
                    ProjectBudgetOverrun::dispatch($project, $overrun);
                }
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class)->orderBy('order');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(ProjectBudgetItem::class);
    }

    public function materialUsages(): HasMany
    {
        return $this->hasMany(MaterialUsage::class);
    }

    public function workers(): HasMany
    {
        return $this->hasMany(ConstructionWorker::class);
    }

    public function siteMonitors(): HasMany
    {
        return $this->hasMany(SiteMonitor::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ConstructionDocument::class);
    }

    public function contractorRequests(): HasMany
    {
        return $this->hasMany(ContractorRequest::class);
    }

    public function monitoringReports(): HasMany
    {
        return $this->hasMany(MonitoringReport::class);
    }

    public function getPaymentDescription(): ?string
    {
        return "Construction Project: {$this->name}";
    }

    public function getPaymentAmount(): float
    {
        return max(0, (float) $this->contract_value - (float) $this->amount_paid);
    }

    public function getPaymentCustomerName(): ?string
    {
        return $this->client_name;
    }

    public function getPaymentCustomerEmail(): ?string
    {
        return $this->client_email;
    }

    public function getPaymentCustomerPhone(): ?string
    {
        return $this->client_phone;
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(\Modules\Finance\Models\Invoice::class, 'invoice_id');
    }

    public function getGeoJsonFileAttributeName(): string { return 'geometry'; }

    public function getGeoJsonUrl(): ?string
    {
        if (empty($this->geometry)) { return null; }
        $json = is_array($this->geometry) ? json_encode($this->geometry) : $this->geometry;
        return 'data:application/json;base64,' . base64_encode($json);
    }

    public function getBudgetVarianceAttribute(): float
    {
        return (float) $this->budget - (float) $this->total_spent;
    }

    public function getOverallProgressAttribute(): int
    {
        $phases = $this->phases;
        if ($phases->isEmpty()) {
            return 0;
        }
        return (int) $phases->avg('progress_percent');
    }
}
