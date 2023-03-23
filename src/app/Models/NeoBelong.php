<?php

namespace App\Models;

use App\Enums\MailTemplates;
use App\Enums\NeoBelongStatuses;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Neo\NeoBelongStatusType;
use App\Notifications\NeoInvitationNotification;
use Illuminate\Support\Facades\Notification;
use Session;
use DB;

/**
 * App\Models\NeoBelong
 *
 * @property int $id id for Laravel
 * @property int $neo_id
 * @property int $rio_id
 * @property string|null $profilePhoto
 * @property int $status 0:申請中 、1:所属状態
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoBelong newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoBelong newQuery()
 * @method static \Illuminate\Database\Query\Builder|NeoBelong onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoBelong query()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoBelong whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoBelong whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoBelong whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoBelong whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoBelong whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoBelong whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoBelong whereIsDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoBelong whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NeoBelong withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NeoBelong withoutTrashed()
 * @mixin \Eloquent
 * @property int $is_display
 */

class NeoBelong extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'neo_id',
        'rio_id',
        'role',
        'status',
        'is_display',
    ];

    /**
     * Define relationship with rio model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class);
    }

    /**
     * Send email notification to RIO that NEO has invited to participate
     *
     * @param \App\Models\Neo $neo
     * @param \App\Models\Rio $rio
     * @return bool|void
     */
    public function sendEmailToInvitedRio($neo, $rio)
    {
        // Check RIO receiver if allowed to receive email
        if (NotificationRejectSetting::isRejectedEmail($rio, MailTemplates::NEO_INVITATION)) {
            return false;
        }

        // Get user information
        $user = User::whereRioId($rio->id)->firstOrFail();

        // Send email to RIO receiver
        Notification::sendNow($user, new NeoInvitationNotification($neo, $rio));
    }

    /**
     * Scope a query to get neo affiliation list for the authenticated rio.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAffiliateList($query)
    {
        /** @var User */
        $user = auth()->user();

        return $query->join('neos', 'neo_belongs.neo_id', '=', 'neos.id')
            ->whereNull('neos.deleted_at')
            ->where('neo_belongs.status', NeoBelongStatuses::AFFILIATED)
            ->where('neo_belongs.rio_id', '=', $user->id)
            ->select(
                'neo_belongs.id as id',
                'neos.organization_name as organization_name',
                'neo_belongs.is_display as is_display'
            );
    }

    /**
     * Scope a query to check if RIO is an existing NEO participant.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return bool
     */
    public function scopeIsExistingParticipant($query, int $id)
    {
        /** @var User */
        $user = auth()->user();

        return $query
            ->whereRioId($user->rio_id)
            ->whereNeoId($id)
            ->exists();
    }

    /**
     * Scope a query to get neo applying list for the authenticated rio.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApplyingList($query)
    {
        $defaultProfileDirectory = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');

        return $query->join('neos', 'neo_belongs.neo_id', '=', 'neos.id')
            ->join('rios', 'neo_belongs.rio_id', '=', 'rios.id')
            ->join('rio_profiles', 'neo_belongs.rio_id', '=', 'rio_profiles.rio_id')
            ->whereNull('neos.deleted_at')
            ->where('neo_belongs.status', NeoBelongStatuses::PENDING)
            ->select(
                'neo_belongs.id as id',
                'rios.id as rio_id',
                'rios.family_name',
                'rios.first_name',
                DB::raw("CASE
                            WHEN rio_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                            ELSE '" . $defaultProfileDirectory . "'
                        END AS profile_photo")
            );
    }

    /**
     * Scope a query to get neo applying list for the authenticated rio.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $role
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAffiliatedPerRole($query, $role)
    {
        return $query->with('rio', function ($q) {
            $q->with(['rio_profile']);
        })
            ->whereStatus(NeoBelongStatusType::AFFILIATE)
            ->whereRole($role)
            ->whereNull('deleted_at')
            ->latest('updated_at');
    }

    /**
     * Get the profile image attribute
     *
     * @return string
     */
    public function getProfileImageAttribute()
    {
        $profilePhoto = is_null($this->profilePhoto) ? config('bphero.profile_image_filename') : $this->profilePhoto;

        return asset(config('bphero.profile_image_directory') . $profilePhoto);
    }

    /**
     * Get rio connected
     *
     * @param int $neoId
     * @param int $rioId
     *
     * @return mixed
     */
    public static function getRioConnected($neoId, $rioId)
    {
        return self::whereNeoId($neoId)->whereRioId($rioId)->first();
    }

    /**
     * Check user Rio and other Rio if both belong in at least one Neo
     *
     * @param Rio $rio
     * @return boolean
     */
    public static function isBothRioBelongsToNeo($rio)
    {
        /** @var User */
        $user = auth()->user();

        $userNeos = $user->rio
            ->neos()
            ->where('neo_belongs.status', NeoBelongStatusType::AFFILIATE)
            ->pluck('neos.id')
            ->toArray();

        $otherNeos = $rio
            ->neos()
            ->where('neo_belongs.status', NeoBelongStatusType::AFFILIATE)
            ->pluck('neos.id')
            ->toArray();

        return count(array_intersect($userNeos, $otherNeos)) > 0;
    }

    /**
     * Check RIO is belong to Applying NEO
     *
     * @param Rio $rio
     * @param mixed $neo
     * @return boolean
     */
    public static function isRioBelongsToApplyingNeo($rio, $neo)
    {
        $userNeos = $rio
            ->neos()
            ->where('neo_belongs.status', NeoBelongStatusType::AFFILIATE)
            ->pluck('neos.id')
            ->toArray();

        return count(array_intersect($userNeos, $neo->toArray())) > 0;
    }

    /**
     * Check NEO is belong to Participating RIO
     *
     * @param \App\Http\Requests\Neo\ParticipationRequest $requestData
     * @param Neo $sessionData
     *
     * @return boolean
     */
    public static function isParticipationRequestBelongToNeo($requestData, $sessionData)
    {
        return $sessionData
            ->applying_members()
            ->where('neo_belongs.id', $requestData['id'])
            ->where('neo_belongs.neo_id', Session::get('neoProfileId'))
            ->where('neo_belongs.rio_id', $requestData['rio_id'])
            ->exists();
    }

    /**
     * Check if Participation Invitation belongs to Invited RIO
     *
     * @param \App\Http\Requests\Neo\ParticipationInvitationRequest $requestData
     * @param Neo $sessionData
     *
     * @return boolean
     */
    public static function isParticipationInvitationBelongToNeo($requestData, $sessionData)
    {
        return $sessionData
            ->inviting_members()
            ->where('neo_belongs.neo_id', Session::get('neoProfileId'))
            ->where('neo_belongs.rio_id', $requestData['id'])
            ->exists();
    }
}
