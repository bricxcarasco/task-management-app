<?php

namespace App\Models;

use App\Enums\Schedule\GuestStatuses;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ScheduleGuest
 *
 * @property int $id id for Laravel
 * @property int $schedule_id
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property int $status 0:返答待ち 、1:参加、-1:不参加
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleGuest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleGuest newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScheduleGuest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleGuest query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleGuest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleGuest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleGuest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleGuest whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleGuest whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleGuest whereScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleGuest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleGuest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ScheduleGuest withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScheduleGuest withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Rio|null $rio
 * @property-read \App\Models\Schedule $schedule
 * @property-read \App\Models\RioProfile|null $rio_profile
 */
class ScheduleGuest extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'schedule_guests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'schedule_id',
        'rio_id',
        'neo_id',
        'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'equivalent_status',
    ];


    /**
     * Define relationship with schedule model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    /**
     * Define relationship with rio model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class, 'rio_id');
    }

    /**
     * Define relationship with rio model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class, 'neo_id');
    }

    /**
     * Convert status to string status
     *
     * @return array|string|null
     */
    public function getEquivalentStatusAttribute()
    {
        /** @var Schedule */
        $schedule = Schedule::whereId($this->schedule_id)
            ->first();

        if (($this->rio_id && $this->rio_id === $schedule->owner_rio_id) || $this->neo_id && $this->neo_id === $schedule->owner_neo_id) {
            return __('Owner');
        }

        switch ($this->status) {
            case GuestStatuses::WAITING_FOR_RESPONSE:
                $status = __('Inviting');
                break;
            case GuestStatuses::PARTICIPATE:
                $status = __('Attendance');
                break;
            case GuestStatuses::NOT_PARTICIPATE:
                $status = __('Absence');
                break;
            default:
                $status = null;
                break;
        }

        return $status;
    }

    /**
     * Get selected in schedule guests
     *
     * @param mixed $query
     * @param mixed $id
     * @return mixed
     */
    public function scopeSelectedGuests($query, $id)
    {
        $defaultProfileDirectory = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.neo_profile_image');

        return $query->selectRaw('
            (CASE
                WHEN schedule_guests.rio_id IS NOT NULL
                    THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                WHEN schedule_guests.neo_id IS NOT NULL
                    THEN neos.organization_name
                ELSE NULL
            END) AS name
        ')
            ->selectRaw('
            (CASE
                WHEN schedule_guests.rio_id IS NOT NULL
                    THEN "RIO"
                WHEN schedule_guests.neo_id IS NOT NULL
                    THEN "NEO"
                ELSE NULL
            END) AS type
        ')
            ->selectRaw('
            (CASE
                WHEN schedule_guests.rio_id IS NOT NULL
                    THEN schedule_guests.rio_id
                WHEN schedule_guests.neo_id IS NOT NULL
                    THEN schedule_guests.neo_id
                ELSE NULL
            END) AS id
        ')
            ->selectRaw("
            (CASE
                WHEN schedule_guests.rio_id IS NOT NULL
                    THEN 
                        CASE
                            WHEN rio_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                            ELSE '" . $defaultProfileDirectory . "'
                        END
                WHEN schedule_guests.neo_id IS NOT NULL
                    THEN
                        CASE
                            WHEN neo_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                            ELSE '" . $defaultProfileDirectory . "'
                        END
                
            END) AS profile_picture
        ")
            ->leftJoin('rios', 'rios.id', '=', 'schedule_guests.rio_id')
            ->leftJoin('rio_profiles', 'rio_profiles.rio_id', '=', 'schedule_guests.rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'schedule_guests.neo_id')
            ->leftJoin('neo_profiles', 'neo_profiles.neo_id', '=', 'schedule_guests.neo_id')
            ->whereScheduleId($id);
    }

    /**
     * Get profile photo in null attribute for vue
     *
     * @return null|string
     */
    public function getProfilePhotoAttribute()
    {
        // Set profile photo as null to maintain selected guests
        if (is_null($this->rio_profile)) {
            return null;
        }

        $profilePhoto = is_null($this->rio_profile->profile_photo) ? config('bphero.profile_image_filename') : $this->rio_profile->profile_photo;

        return asset(config('bphero.profile_image_directory') . $profilePhoto);
    }

    /**
     * Get profile photo attribute for laravel
     *
     * @return null|string
     */
    public function getProfileImageAttribute()
    {
        $profilePhoto = is_null($this->profilePhoto) ? config('bphero.profile_image_filename') : $this->profilePhoto;

        return asset(config('bphero.profile_image_directory') . $profilePhoto);
    }
}
