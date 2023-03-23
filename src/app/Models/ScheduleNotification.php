<?php

namespace App\Models;

use App\Enums\Schedule\GuestStatuses;
use App\Enums\ServiceSelectionTypes;
use App\Traits\ModelUpdatedTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ScheduleNotification
 *
 * @property int $id id for Laravel
 * @property int $schedule_id
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property int $status 0:返答待ち 、1:参加、-1:不参加
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read string $end_datetime
 * @property-read string $start_datetime
 * @property-read \App\Models\Rio|null $neo
 * @property-read \App\Models\Rio|null $rio
 * @property-read \App\Models\Schedule $schedule
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification deletableNotifications()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification notifications($service)
 * @method static \Illuminate\Database\Query\Builder|ScheduleNotification onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification whereScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleNotification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ScheduleNotification withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScheduleNotification withoutTrashed()
 * @mixin \Eloquent
 */
class ScheduleNotification extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'schedule_notifications';

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
     * Define relationship with schedules table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    /**
     * Define relationship for rios table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class, 'rio_id');
    }

    /**
     * Define relationship for neos table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Rio::class, 'neo_id');
    }

    /**
     * Get Start Datetime attribute
     *
     * @return string
     */
    public function getStartDatetimeAttribute()
    {
        /** @phpstan-ignore-next-line */
        if ($this->start_time) {
            /** @phpstan-ignore-next-line */
            $date = $this->start_date . ' ' . $this->start_time;

            return Carbon::parse($date)->format('Y/m/d H:i');
        }

        /** @phpstan-ignore-next-line */
        return Carbon::parse($this->start_date)->format('Y/m/d');
    }

    /**
     * Get End Datetime attribute
     *
     * @return string
     */
    public function getEndDatetimeAttribute()
    {
        /** @phpstan-ignore-next-line */
        if ($this->end_time) {
            /** @phpstan-ignore-next-line */
            $date = $this->end_date . ' ' . $this->end_time;

            return Carbon::parse($date)->format('Y/m/d H:i');
        }

        /** @phpstan-ignore-next-line */
        return Carbon::parse($this->end_date)->format('Y/m/d');
    }

    /**
     * Scope a query to get schedule service notifications.
     *
     * @param \Illuminate\Database\Eloquent\Builder<ScheduleNotification> $query
     * @param mixed $service Selected service
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeNotifications($query, $service)
    {
        // Initialize query
        $query = $query
            ->select(
                'schedules.*',
                'schedule_notifications.*',
                'schedules.id AS id',
                'schedule_guests.id AS guest_id',
                'schedule_notifications.id AS notification_id',
                'schedule_guests.status',
            )
            ->selectRaw('
                (CASE
                    WHEN schedules.owner_rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    WHEN schedules.owner_neo_id IS NOT NULL
                        THEN neos.organization_name
                    ELSE NULL
                END) AS host_name
            ')
            ->leftJoin('schedules', 'schedules.id', '=', 'schedule_notifications.schedule_id')
            ->leftJoin('schedule_guests', 'schedule_guests.schedule_id', '=', 'schedule_notifications.schedule_id')
            ->leftJoin('rios', 'rios.id', '=', 'schedules.owner_rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'schedules.owner_neo_id')

            // Fetch undeleted data from joined tables
            ->whereNull('schedules.deleted_at')
            ->whereNull('schedule_guests.deleted_at')
            ->whereNull('rios.deleted_at')
            ->whereNull('neos.deleted_at')

            // Fetch only unconfirmed invitations
            ->where('schedule_guests.status', GuestStatuses::WAITING_FOR_RESPONSE);

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                $query
                    ->where('schedule_guests.rio_id', $service->data->id)
                    ->where('schedule_notifications.rio_id', $service->data->id)
                    ->where('schedules.created_rio_id', '!=', $service->data->id);
                break;
            case ServiceSelectionTypes::NEO:
                $query
                    ->where('schedule_guests.neo_id', $service->data->id)
                    ->where('schedule_notifications.neo_id', $service->data->id);
                break;
            default:
                break;
        }

        return $query->orderBy('schedule_notifications.created_at', 'DESC');
    }

    /**
     * Scope a query to get all batch deletable notifications.
     *
     * @param \Illuminate\Database\Eloquent\Builder<ScheduleNotification> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeDeletableNotifications($query)
    {
        return $query
            ->select(
                'schedule_notifications.id AS id',
                'schedules.id AS schedule_id',
                'schedules.*',
                'schedule_notifications.*',
            )
            ->selectRaw('
                (CASE
                    WHEN schedules.start_time IS NOT NULL
                        THEN TRIM(CONCAT(schedules.start_date, " ", schedules.start_time))
                    ELSE CONCAT(schedules.start_date, " 00:00:00")
                END) AS start_datetime
            ')
            ->selectRaw('
                (CASE
                    WHEN schedules.end_time IS NOT NULL
                        THEN TRIM(CONCAT(schedules.end_date, " ", schedules.end_time))
                    ELSE CONCAT(schedules.end_date, " 00:00:00")
                END) AS end_datetime
            ')
            ->leftJoin('schedules', 'schedules.id', '=', 'schedule_notifications.schedule_id')
            ->where('schedule_notifications.status', GuestStatuses::WAITING_FOR_RESPONSE)
            ->having('end_datetime', '<', Carbon::now()->subHours(72));
    }
}
