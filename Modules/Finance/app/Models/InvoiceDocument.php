<?php

namespace Modules\Finance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDocument extends Model
{
    use HasFactory;

    protected $table = 'fin_invoice_documents';

    protected $fillable = [
        'invoice_id',
        'title',
        'document_type',
        'file_path',
        'file_name',
        'mime_type',
        'uploaded_by_user_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}