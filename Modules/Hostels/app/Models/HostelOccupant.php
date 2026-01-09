<?php

namespace Modules\Hostels\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\CommunicationCentre\Traits\HasCommunicationProfile;
use Modules\Hostels\Database\factories\HostelOccupantFactory;

class HostelOccupant extends Model
{
    use HasCommunicationProfile, HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hostel_id',
        'first_name',
        'last_name',
        'other_names',
        'full_name',
        'gender',
        'dob',
        'phone',
        'alt_phone',
        'email',
        'national_id_number',
        'student_id',
        'institution',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'id_card_front_photo',
        'id_card_back_photo',
        'profile_photo',
        'status',
        'company_id',
    ];

    protected $table = 'hostel_occupants';

    protected static function newFactory(): HostelOccupantFactory
    {
        return HostelOccupantFactory::new();
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function documents()
    {
        return $this->hasMany(HostelOccupantDocument::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'reported_by_hostel_occupant_id');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function visitorLogs()
    {
        return $this->hasMany(VisitorLog::class);
    }

    public function hostelOccupantUser()
    {
        return $this->hasOne(HostelOccupantUser::class);
    }

    /**
     * Check if hostel occupant has reached the maximum bookings for a semester
     *
     * @param  string  $academicYear
     * @param  string  $semester
     * @return bool
     */
    public function hasReachedSemesterBookingLimit($academicYear, $semester)
    {
        $bookingCount = $this->bookings()
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('channel', 'online') // Only count public bookings
            ->whereNotIn('status', ['cancelled', 'no_show']) // Exclude cancelled bookings
            ->count();

        return $bookingCount >= 3;
    }

    /**
     * Get the number of bookings for a semester
     *
     * @param  string  $academicYear
     * @param  string  $semester
     * @return int
     */
    public function getSemesterBookingCount($academicYear, $semester)
    {
        return $this->bookings()
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('channel', 'online') // Only count public bookings
            ->whereNotIn('status', ['cancelled', 'no_show']) // Exclude cancelled bookings
            ->count();
    }
}
