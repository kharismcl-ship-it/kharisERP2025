<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedAssetDocument extends Model
{
    protected $fillable = [
        'fixed_asset_id',
        'title',
        'document_type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'uploaded_by_user_id',
        'notes',
    ];

    public const DOCUMENT_TYPES = [
        'contract'   => 'Contract',
        'invoice'    => 'Purchase Invoice',
        'photo'      => 'Photo',
        'manual'     => 'Manual / Datasheet',
        'warranty'   => 'Warranty Certificate',
        'insurance'  => 'Insurance Policy',
        'inspection' => 'Inspection Report',
        'other'      => 'Other',
    ];

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by_user_id');
    }

    public function formattedFileSize(): string
    {
        if (! $this->file_size) {
            return '—';
        }

        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 1) . ' MB';
        }

        return number_format($this->file_size / 1024, 1) . ' KB';
    }
}