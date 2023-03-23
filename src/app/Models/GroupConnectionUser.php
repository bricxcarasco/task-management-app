<?php

namespace App\Models;

use App\Enums\Connection\GroupStatuses;
use App\Enums\MailTemplates;
use App\Notifications\ConnectionGroupInvitationNotification;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;

/**
 * App\Models\GroupConnectionUser
 *
 * @property int $id id for Laravel
 * @property int $group_connection_id
 * @property int $rio_id
 * @property int $status 0:申請中 、1:つながり状態（否認の場合はdeleted_atをnot nullに設定）
 * @property \Illuminate\Support\Carbon $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\GroupConnection $group
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnectionUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnectionUser newQuery()
 * @method static \Illuminate\Database\Query\Builder|GroupConnectionUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnectionUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnectionUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnectionUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnectionUser whereGroupConnectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnectionUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnectionUser whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnectionUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnectionUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GroupConnectionUser withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GroupConnectionUser withoutTrashed()
 * @mixin \Eloquent
 */
class GroupConnectionUser extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'group_connection_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_connection_id',
        'rio_id',
        'status',
        'invite_message',
    ];

    /**
     * Define relationship with rio table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class, 'rio_id');
    }

    /**
     * Define relationship with group_connections table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(GroupConnection::class, 'group_connection_id');
    }

    /**
     * Scope a query to get logged-in RIO group connections list.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupConnections($query)
    {
        /** @var User */
        $user = auth()->user();

        $query
            ->whereRioId($user->rio_id)
            ->with([
                'group.rio',
                'rio'
            ])
            ->has('group.rio')
            ->has('rio');

        return $query;
    }

    /**
     * Scope a query to get connected group users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConnected($query)
    {
        return $query->whereStatus(GroupStatuses::CONNECTED);
    }

    /**
     * Scope a query to get users with pending invitation request.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInvitationRequests($query)
    {
        return $query->whereStatus(GroupStatuses::REQUESTING);
    }

    /**
     * Send email notification to connection group user
     *
     * @param \App\Models\GroupConnection $group
     * @param \App\Models\Rio $rio
     * @return bool|void
     */
    public function sendEmailToConnectionGroupUser($group, $rio)
    {
        // Check RIO receiver if allowed to receive email
        if (NotificationRejectSetting::isRejectedEmail($rio, MailTemplates::CONNECTION_GROUP_INVITATION)) {
            return false;
        }

        // Get user information
        $user = User::whereRioId($rio->id)->firstOrFail();

        // Send email to RIO receiver
        Notification::sendNow($user, new ConnectionGroupInvitationNotification($group, $rio));
    }
}
