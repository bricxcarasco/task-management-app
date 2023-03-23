<?php

namespace App\Models;

use App\Enums\NotificationTypes;
use App\Traits\ModelUpdatedTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Notification
 *
 * @property int $id id for Laravel
 * @property int $rio_id
 * @property int|null $receive_neo_id
 * @property string $notification_content
 * @property string|null $destination_url
 * @property int $is_announcement
 * @property int $is_notification_read Unread: 0, Read:1
 * @property string|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read string|null $notification_date
 * @property-read string|null $notification_recipient
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Rio $rio
 * @method static \Illuminate\Database\Eloquent\Builder|Notification allNotifications()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification announcementNotifications()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Query\Builder|Notification onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification unreadNotifications()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereDestinationUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereIsAnnouncement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereIsNotificationRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereNotificationContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereReceiveNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Notification withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Notification withoutTrashed()
 * @mixin \Eloquent
 */
class Notification extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rio_id',
        'receive_neo_id',
        'notification_content',
        'destination_url',
        'is_notification_read',
        'read_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'notification_recipient',
    ];

    /**
     * Define relationship with RIO model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class, 'rio_id');
    }

    /**
     * Define relationship with NEO model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class, 'receive_neo_id');
    }

    /**
     * Get notification recipient attribute
     *
     * @return string|null
     */
    public function getNotificationRecipientAttribute()
    {
        /** @var \App\Models\Neo */
        $neo = $this->neo;

        /** @var \App\Models\Rio */
        $rio = $this->rio;

        return empty($neo)
            ? "{$rio->family_name} {$rio->first_name}"
            : $neo->organization_name;
    }

    /**
     * Get notification date in Y/m/d attribute
     *
     * @return string|null
     */
    public function getNotificationDateAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y/m/d');
    }

    /**
     * Register new notification.
     *
     * @param array $data Should contain the following array keys:
     *      - rio_id => required
     *      - receive_neo_id => optional
     *      - notification_content => required
     *      - destination_url => optional
     * @return object
     */
    public static function createNotification($data)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return self::create([
            'rio_id' => $data['rio_id'] ?? $user->rio_id,
            'receive_neo_id' => $data['receive_neo_id'] ?? null,
            'notification_content' => $data['notification_content'] ?? '-',
            'destination_url' => $data['destination_url'] ?? null,
        ]);
    }

    /**
     * Scope a query to get all logged-in user notifications.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Notification> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeAllNotifications($query)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return $query
            ->with([
                'rio',
                'neo',
            ])
            ->whereRioId($user->rio_id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to get all logged-in user's unread notifications.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Notification> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeUnreadNotifications($query)
    {
        return $query
            ->allNotifications()
            ->whereIsNotificationRead(false);
    }

    /**
     * Scope a query to only retrieve announcement notifications.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Notification> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAnnouncementNotifications($query)
    {
        return $query->whereIsAnnouncement(NotificationTypes::FOR_ANNOUNCEMENT);
    }
}
