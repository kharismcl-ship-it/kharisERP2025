<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Concerns\BelongsToCompany;

class FarmDocument extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_documents';

    protected $fillable = [
        'farm_id',
        'company_id',
        'documentable_type',
        'documentable_id',
        'title',
        'document_type',
        'file_path',
        'file_size',
        'mime_type',
        'description',
        'tags',
        'uploaded_by',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    const DOCUMENT_TYPES = ['photo', 'video', 'document', 'report', 'contract', 'other'];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}
