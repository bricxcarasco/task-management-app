<?php

namespace App\Models;

use App\Enums\MailTemplates;
use App\Enums\ServiceSelectionTypes;
use App\Notifications\ScheduleInvitationNotification;
use App\Traits\ModelUpdatedTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;

/**
 * App\Models\Schedule
 *
 * @property int $id id for Laravel
 * @property int|null $owner_rio_id ↓どちらかのみセット
 * @property int|null $owner_neo_id ↑どちらかのみセット
 * @property int $created_rio_id
 * @property string $schedule_title
 * @property string $start_date
 * @property string|null $start_time
 * @property string $end_date
 * @property string|null $end_time
 * @property string|null $meeting_url
 * @property string|null $caption
 * @property boolean $all_day
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newQuery()
 * @method static \Illuminate\Database\Query\Builder|Schedule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereMeetingUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereOwnerNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereOwnerRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereScheduleTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Schedule withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Schedule withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\Rio $created_rio
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScheduleGuest[] $guests
 * @property-read int|null $guests_count
 * @property-read \App\Models\Neo|null $owner_neo
 * @property-read \App\Models\Rio|null $owner_rio
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScheduleNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule getSchedulesPerDay($service, $date)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule getSchedulesPerMonth($service, $date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule scheduleOwnerOrGuest($service)
 */
class Schedule extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'schedules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_rio_id',
        'owner_neo_id',
        'created_rio_id',
        'schedule_title',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'meeting_url',
        'caption',
    ];

    /**
     * Define relationship with schedule guests model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function guests()
    {
        return $this->hasMany(ScheduleGuest::class);
    }

    /**
     * Define relationship with schedule notifications model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(ScheduleNotification::class);
    }

    /**
     * Define relationship for RIO owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner_rio()
    {
        return $this->belongsTo(Rio::class, 'owner_rio_id');
    }

    /**
     * Define relationship for NEO owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner_neo()
    {
        return $this->belongsTo(Neo::class, 'owner_neo_id');
    }

    /**
     * Define relationship for RIO creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_rio()
    {
        return $this->belongsTo(Rio::class, 'created_rio_id');
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($schedule) {
            // Softdelete associated schedule guests
            $schedule->guests()->each(function ($guest) {
                $guest->delete();
            });

            // Softdelete associated schedule notifications
            $schedule->notifications()->each(function ($notification) {
                $notification->delete();
            });
        });
    }

    /**
     * Send email notification on schedule invitation
     *
     * @param \App\Models\Rio|\App\Models\Neo $sender
     * @param array $receivers
     * @return void
     */
    public static function sendEmailToScheduleInvitee($sender, $receivers)
    {
        foreach ($receivers as $receiver) {
            // Check RIO receiver if allowed to receive email
            if ($receiver instanceof \App\Models\Rio) {
                if (NotificationRejectSetting::isRejectedEmail(
                    $receiver,
                    MailTemplates::SCHEDULE_INVITATION
                )) {
                    // Skip and proceed to next email receiver
                    continue;
                }

                // Get email information
                $user = User::whereRioId($receiver->id)->firstOrFail();

                // Send email to RIO receiver
                Notification::sendNow($user, new ScheduleInvitationNotification($sender, $receiver));
            }

            // Check NEO owner receiver if allowed to receive email
            if ($receiver instanceof \App\Models\Neo) {
                // Get NEO owner NeoBelong information
                /** @var \App\Models\NeoBelong */
                $neoBelongs = $receiver->owner;

                // Guard clause for non-existing NEO owner
                if (empty($neoBelongs)) {
                    // Skip and proceed to next email receiver
                    continue;
                }

                // Get NEO owner RIO information
                /** @var \App\Models\Rio */
                $neoOwnerReceiver = $neoBelongs->rio;

                if (NotificationRejectSetting::isRejectedEmail(
                    $neoOwnerReceiver,
                    MailTemplates::SCHEDULE_INVITATION
                )) {
                    // Skip and proceed to next email receiver
                    continue;
                }

                // Get email information
                $user = User::whereRioId($neoOwnerReceiver->id)->firstOrFail();

                // Send email to NEO receiver
                Notification::sendNow($user, new ScheduleInvitationNotification($sender, $receiver));
            }
        }
    }

    /**
     * Scope a query to get schedule list based on year and month.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Schedule> $query
     * @param mixed $service Selected service
     * @param string $date Selected date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeGetSchedulesPerMonth($query, $service, $date = null)
    {
        // Initialize query
        $query = $query->scheduleOwnerOrGuest($service);

        if (!empty($date)) {
            // Get start and end day of the month
            $startOfMonth = Carbon::parse($date)->startOfMonth();
            $endOfMonth = Carbon::parse($date)->endOfMonth();

            $query = $query
                ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                ->whereNotBetween('end_date', [$startOfMonth, $endOfMonth]);
        }

        return $query->orderBy('start_date', 'ASC');
    }

    /**
     * Scope a query to get schedule list based on specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Schedule> $query
     * @param mixed $service Selected service
     * @param string $date Selected date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeGetSchedulesPerDay($query, $service, $date)
    {
        // Initialize query
        $query = $query->scheduleOwnerOrGuest($service);

        // Parse date
        $date = Carbon::parse($date);

        return $query
            ->whereRaw('? between start_date and end_date', [$date])
            ->orderBy('start_time', 'ASC');
    }

    /**
     * Scope a query to check if selected service is the owner or guest.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Schedule> $query
     * @param mixed $service Selected service
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeScheduleOwnerOrGuest($query, $service)
    {
        // Setup initial query
        $query = ScheduleGuest::select(
            'schedules.id AS id',
            'schedule_guests.id AS schedule_guest_id',
            'schedules.owner_rio_id AS owner_rio_id',
            'schedules.owner_neo_id AS owner_neo_id',
            'schedules.created_rio_id AS created_rio_id',
            'schedules.schedule_title AS schedule_title',
            'schedules.start_date AS start_date',
            'schedules.start_time AS start_time',
            'schedules.end_date AS end_date',
            'schedules.end_time AS end_time',
            'schedules.meeting_url AS meeting_url',
            'schedules.caption AS caption',
            'schedule_guests.schedule_id AS schedule_id',
            'schedule_guests.rio_id AS rio_id',
            'schedule_guests.neo_id AS neo_id',
            'schedule_guests.status AS status',
        )
            ->leftJoin(
                'schedules',
                'schedules.id',
                '=',
                'schedule_guests.schedule_id'
            );

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $query->where('rio_id', $service->data->id);
            case ServiceSelectionTypes::NEO:
                return $query->where('neo_id', $service->data->id);
            default:
                return $query;
        }
    }
}
