<?php

namespace Modules\Hostels\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Database\factories\HostelOccupantDocumentFactory;

class HostelOccupantDocument extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hostel_occupant_documents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hostel_occupant_id',
        'document_type',
        'file_path',
        'uploaded_by',
    ];

    protected static function newFactory(): HostelOccupantDocumentFactory
    {
        return HostelOccupantDocumentFactory::new();
    }

    public function hostelOccupant()
    {
        return $this->belongsTo(HostelOccupant::class, 'hostel_occupant_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
