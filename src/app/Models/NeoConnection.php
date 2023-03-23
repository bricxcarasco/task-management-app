<?php

namespace App\Models;

use App\Enums\Neo\ConnectionStatusType;
use App\Enums\Chat\ChatTargetTypes;
use App\Enums\ConnectionStatuses;
use App\Enums\MailTemplates;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Neo\NeoBelongStatusType;
use App\Enums\Neo\RoleType;
use App\Enums\NeoBelongStatuses;
use App\Enums\ServiceSelectionTypes;
use App\Notifications\ConnectionApplicationNotification;
use DB;
use Illuminate\Support\Facades\Notification;
use Session;

/**
 * App\Models\NeoConnection
 *
 * @property string|null $profilePhoto
 */

class NeoConnection extends Model
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
        'status',
        'message'
    ];

    /**
     * Scope a query to only include Neo affiliated.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeAffiliated($query)
    {
        $query->where('status', NeoBelongStatusType::AFFILIATE);
    }

    /**
     * Scope a query to include Neo with both connection.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param array $neo_ids
     * @return void
     */
    public function scopeBothConnections($query, $neo_ids)
    {
        $query->where(function ($query) use ($neo_ids) {
            $query->orWhereIn('neo_id', $neo_ids)
                    ->orWhereIn('connection_neo_id', $neo_ids);
        });
    }

    /**
     * Send email notification to NEO connection.
     *
     * @param \App\Models\Rio|\App\Models\Neo $connectionSender
     * @param \App\Models\Rio|\App\Models\Neo $connectionReceiver
     * @return bool|void
     */
    public function sendEmailToConnection($connectionSender, $connectionReceiver)
    {
        // If NEO to RIO connection application
        if ($connectionReceiver instanceof \App\Models\Rio) {
            // Check RIO receiver if allowed to receive email
            if (NotificationRejectSetting::isRejectedEmail(
                $connectionReceiver,
                MailTemplates::CONNECTION_APPLICATION
            )) {
                return false;
            }

            // Get user information
            $user = User::whereRioId($connectionReceiver->id)->first();

            // Send email to RIO receiver
            Notification::sendNow(
                $user,
                new ConnectionApplicationNotification(
                    $connectionSender,
                    $connectionReceiver
                )
            );
        }

        // If NEO to NEO or RIO to NEO connection application
        if ($connectionReceiver instanceof \App\Models\Neo) {
            // Send email to the NEO owner's email address (NOT to NEO's email)
            if (!empty($connectionReceiver->owner->rio)) {
                // Check NEO owner receiver if allowed to receive email
                if (NotificationRejectSetting::isRejectedEmail(
                    $connectionReceiver->owner->rio,
                    MailTemplates::CONNECTION_APPLICATION
                )) {
                    return false;
                }

                // Get user information
                $user = User::whereRioId($connectionReceiver->owner->rio_id)->first();

                // Send email to NEO owner receiver
                Notification::sendNow(
                    $user,
                    new ConnectionApplicationNotification(
                        $connectionSender,
                        $connectionReceiver
                    )
                );
            }
        }
    }

    /**
     * Check user both Neo connected
     *
     * @param Rio $rio
     * @return boolean
     */
    public static function isNeoConnected($rio)
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

        $userNeoConnections = self::affiliated()
            ->bothConnections($userNeos)
            ->pluck('id')
            ->toArray();

        $otherNeoConnections = self::affiliated()
            ->bothConnections($otherNeos)
            ->pluck('id')
            ->toArray();

        return count(array_intersect(array_unique($userNeoConnections), array_unique($otherNeoConnections))) > 0;
    }

    /**
     * Scope a query to get Neo Connection base on session.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @param bool $isRequester
     * @return mixed
     */
    public function scopeIsExistingNeoConnection($query, int $id, $isRequester = false)
    {
        $serviceSelected = json_decode(Session::get('ServiceSelected'));

        if (!$isRequester) {
            if ($serviceSelected->type === ServiceSelectionTypes::NEO) {
                $query = NeoConnection::whereConnectionNeoId($serviceSelected->data->id)
                    ->whereNeoId($id);
            } else {
                $query = NeoConnection::whereConnectionRioId($serviceSelected->data->id)
                    ->whereNeoId($id);
            }
        } else {
            $query = NeoConnection::whereConnectionNeoId($id)
                ->whereNeoId($serviceSelected->data->id);
        }

        return $query;
    }

    /**
     * Scope a query to get All Neo message participants
     *
     * @param mixed $query
     * @param mixed $session
     * @return mixed
     */
    public function scopeNeoMessageRecipients($query, $session)
    {
        $entity = $session->data->id ?? $session;

        return $query
            ->select([
                'neo_connections.*',
            ])
            // Get Rio or Neo name
            ->selectRaw('
                (CASE
                    WHEN neo_connections.connection_rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    WHEN neo_connections.connection_neo_id IS NOT NULL
                        THEN neos.organization_name
                    ELSE NULL
                END) AS name
            ')
            ->selectRaw('
                (CASE
                    WHEN neo_connections.connection_rio_id IS NOT NULL
                        THEN neo_connections.connection_rio_id
                    WHEN neo_connections.connection_neo_id IS NOT NULL
                        THEN neo_connections.connection_neo_id
                    ELSE NULL
                END) AS connection_id
            ')
            // Get Rio or Neo profile photo
            ->selectRaw('
                (CASE
                    WHEN neo_connections.connection_rio_id IS NOT NULL
                        THEN rio_profiles.profile_photo
                    WHEN neo_connections.connection_neo_id IS NOT NULL
                        THEN neo_profiles.profile_photo
                    ELSE NULL
                END) AS profile_photo
            ')
            ->leftJoin('rios', 'rios.id', '=', 'neo_connections.connection_rio_id')
            ->leftJoin('rio_profiles', 'rio_profiles.rio_id', '=', 'neo_connections.connection_rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'neo_connections.connection_neo_id')
            ->leftJoin('neo_profiles', 'neo_profiles.neo_id', '=', 'neo_connections.connection_neo_id')
            ->where('neo_connections.neo_id', '=', $entity)
            ->where('neo_connections.status', ConnectionStatuses::CONNECTED);
    }

    /**
     * Scope a query to search in NEO message recipients
     *
     * @param mixed $query
     * @param string $text
     * @return mixed
     */
    public function scopeSearchRecipient($query, $text)
    {
        $searchName = '%' . mb_strtolower($text) . '%';

        return $query->having('name', 'LIKE', $searchName);
    }

    /**
     * Scope a query to filter chat participants
     *
     * @param mixed $query
     * @param mixed $session
     * @param mixed $searchTarget
     * @return mixed
     */
    public function scopefilterRecipient($query, $session, $searchTarget)
    {
        switch ($searchTarget) {
            case ChatTargetTypes::RIO:
                $lists = $query->neoMessageRecipients($session)
                    ->where('connection_neo_id', '=', null);
                break;
            case ChatTargetTypes::NEO:
                $lists = $query->neoMessageRecipients($session)
                    ->where('connection_rio_id', '=', null);
                break;
            default:
                $lists = $query->neoMessageRecipients($session);
                break;
        }

        return $lists;
    }

    /**
     * Scope a query to get Neo Connection with neo belongs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return mixed
     */
    public function scopeConnectionWithNeoBelong($query, int $id)
    {
        $defaultProfileDirectory = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');

        $query = DB::table('rios')
            ->select([
                'rios.id As rio_id',
                DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileDirectory . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo")
            ])
            ->selectRaw('TRIM(CONCAT(rios.family_name, " ", rios.first_name)) AS name')
            ->leftJoin('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
            ->leftJoin('neo_belongs', 'rios.id', '=', 'neo_belongs.rio_id')
            ->where('neo_belongs.neo_id', '=', $id)
            ->where('neo_belongs.deleted_at', '=', null);

        return $query;
    }

    /**
     * Scope a query to get Neo Connection with neo belongs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @param string|null $text
     * @return mixed
     */
    public function scopeRioConnectionWithNeoBelong($query, int $id, string $text = null)
    {
        $validateText = $text ? mb_strtolower($text) : null;
        $searchName = '%' . $validateText . '%';

        $defaultProfileImage = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');

        //Get all existing data in neo_belong
        $exists = DB::table('neo_connections')
            ->select(
                'neo_connections.*',
                'rios.id as rio_id',
                'neo_belongs.status as invitation_status',
                DB::raw("CASE
                            WHEN rio_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                            ELSE '" . $defaultProfileImage . "'
                        END AS profile_photo"),
                DB::raw("CONCAT(family_name,' ',first_name) as name")
            )
            ->join('rios', "neo_connections.connection_rio_id", "=", "rios.id")
            ->join('rio_profiles', 'neo_connections.connection_rio_id', '=', 'rio_profiles.rio_id')
            ->join('neo_belongs', 'neo_connections.connection_rio_id', '=', 'neo_belongs.rio_id')
            ->where('neo_connections.neo_id', '=', $id)
            ->where('neo_belongs.neo_id', '=', $id)
            ->where('neo_belongs.deleted_at', '=', null)
            ->where('neo_connections.deleted_at', '=', null)
            ->where('neo_connections.status', '=', ConnectionStatusType::CONNECTION_STATUS);

        //Get all RIO for compare
        $notInNeoBelong = NeoBelong::where('neo_id', '=', $id)
            ->pluck('rio_id')
            ->all();

        //Get all non existing data in neo_belong
        $notExists = DB::table('neo_connections')
            ->select(
                'neo_connections.*',
                'rios.id as rio_id',
                DB::raw('null as invitation_status'),
                DB::raw("CASE
                            WHEN rio_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                            ELSE '" . $defaultProfileImage . "'
                        END AS profile_photo"),
                DB::raw("CONCAT(family_name,' ',first_name) as name")
            )
            ->join('rios', "neo_connections.connection_rio_id", "=", "rios.id")
            ->join('rio_profiles', 'neo_connections.connection_rio_id', '=', 'rio_profiles.rio_id')
            ->where('neo_connections.neo_id', '=', $id)
            ->whereNotIn('connection_rio_id', $notInNeoBelong)
            ->where('neo_connections.deleted_at', '=', null)
            ->where('neo_connections.status', '=', ConnectionStatusType::CONNECTION_STATUS);

        if (empty($validateText)) {
            $query = $notExists->union($exists)
                ->orderBy('rio_id');
        } else {
            $searchInBelong = $exists->where(DB::raw("LOWER(CONCAT(family_name,' ',first_name))"), 'LIKE', $searchName);

            $query = $notExists->where(DB::raw("LOWER(CONCAT(family_name,' ',first_name))"), 'LIKE', $searchName)
                ->union($searchInBelong)
                ->orderBy('rio_id');
        }

        return $query;
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
     * Scope a query to get Neo Connection with neo belongs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeAllInvitesForNeoBelong($query)
    {
        /** @var User */
        $user = auth()->user();

        $defaultProfileImage = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $neoProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.neo_profile_image');

        return DB::table('neo_belongs')
            ->select([
                'rios.id As rio_id',
                'neo_belongs.id As neo_belong_id',
                'neos.id As neo_profile_id',
                DB::raw("CASE
                            WHEN neo_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                            ELSE '" . $defaultProfileImage . "'
                        END AS profile_photo"),
                'neos.organization_name'
            ])
            ->selectRaw('TRIM(CONCAT(rios.family_name, " ", rios.first_name)) AS name')
            ->leftJoin('rios', "neo_belongs.rio_id", "=", "rios.id")
            ->leftJoin('rio_profiles', 'neo_belongs.rio_id', '=', 'rio_profiles.rio_id')
            ->leftJoin('neo_profiles', 'neo_belongs.neo_id', '=', 'neo_profiles.neo_id')
            ->leftJoin('neos', 'neo_belongs.neo_id', '=', 'neos.id')
            ->leftJoin('neo_connections', function ($join) {
                $join->on('neo_connections.connection_rio_id', '=', 'neo_belongs.rio_id');
                $join->on('neo_connections.neo_id', '=', 'neo_belongs.neo_id');
            })
            ->whereNull('neo_connections.deleted_at')
            ->whereNull('neo_belongs.deleted_at')
            ->where('neo_belongs.rio_id', '=', $user->rio->id)
            ->where('neo_belongs.status', '=', NeoBelongStatuses::INVITING);
    }

    /**
     * Scope a query to get RIO users that can be invited by NEO
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @param string|null $text
     * @return mixed
     */
    public function scopePossibleRioInviteesForNeoBelong($query, int $id, string $text = null)
    {
        $validateText = $text ? mb_strtolower($text) : null;
        $searchName = '%' . $validateText . '%';

        $defaultProfileDirectory = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');

        //Get all rios that are currently pending/approved for pariticipation in a NEO
        $exists = DB::table('rios')
            ->select(
                'rios.id as rio_id',
                'neo_belongs.status as invitation_status',
                DB::raw("CASE
                            WHEN rio_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                            ELSE '" . $defaultProfileDirectory . "'
                        END AS profile_photo"),
                DB::raw("CONCAT(family_name,' ',first_name) as name")
            )
            ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
            ->join('neo_belongs', 'rios.id', '=', 'neo_belongs.rio_id')
            ->where('neo_belongs.role', '!=', RoleType::OWNER)
            ->where('neo_belongs.neo_id', '=', $id)
            ->where('neo_belongs.deleted_at', '=', null);

        //Get all RIO for compare
        $notInNeoBelong = NeoBelong::where('neo_id', '=', $id)
            ->pluck('rio_id')
            ->all();

        //Get all rios that are not currently pending for pariticipation in a NEO
        $notExists = DB::table('rios')
            ->select(
                'rios.id as rio_id',
                DB::raw('null as invitation_status'),
                DB::raw("CASE
                            WHEN rio_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                            ELSE '" . $defaultProfileDirectory . "'
                        END AS profile_photo"),
                DB::raw("CONCAT(family_name,' ',first_name) as name")
            )
            ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
            ->whereNotIn('rios.id', $notInNeoBelong);

        if (empty($validateText)) {
            $query = $notExists->union($exists)
                ->orderBy('rio_id');
        } else {
            $searchInBelong = $exists->where(DB::raw("LOWER(CONCAT(family_name,' ',first_name))"), 'LIKE', $searchName);

            $query = $notExists->where(DB::raw("LOWER(CONCAT(family_name,' ',first_name))"), 'LIKE', $searchName)
                ->union($searchInBelong)
                ->orderBy('rio_id');
        }

        return $query;
    }
}
