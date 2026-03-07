<?php

namespace Modules\ClientService\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CsVisitorProfile extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'client_service_visitor_profiles';

    protected $fillable = [
        'company_id',
        'full_name',
        'phone',
        'email',
        'id_type',
        'id_number',
        'organization',
        'profile_token',
        'photo_path',
        'check_in_signature',
        'communication_opt_in',
    ];

    protected function casts(): array
    {
        return [
            'communication_opt_in' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (CsVisitorProfile $profile) {
            if (empty($profile->profile_token)) {
                $profile->profile_token = (string) Str::uuid();
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function visits()
    {
        return $this->hasMany(CsVisitor::class, 'visitor_profile_id');
    }
}
