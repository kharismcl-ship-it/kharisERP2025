<?php

namespace Modules\Requisition\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RequisitionAttachment extends Model
{
    protected $fillable = [
        'requisition_id',
        'uploaded_by_user_id',
        'label',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}