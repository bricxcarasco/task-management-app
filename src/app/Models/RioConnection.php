<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Enums\Rio\ConnectionStatusType;
use App\Enums\ConnectionStatusType as RioConnectionStatusType;
use App\Enums\Connection\ListFilters;
use App\Enums\Connection\ConnectionStatuses;
use App\Enums\ConnectionStatuses as EnumsConnectionStatuses;
use App\Enums\MailTemplates;
use App\Enums\Neo\AcceptConnectionType;
use App\Enums\Rio\AcceptConnectionByNeoType;
use App\Enums\ServiceSelectionTypes;
use App\Notifications\ConnectionApplicationNotification;
use Illuminate\Support\Facades\Notification;

/**
 * App\Models\RioConnection
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
class RioConnection extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_rio_id',
        'status',
        'message'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleted(function ($rioConnection) {
            $rioConnection->rio_connection_users()->delete();
        });
    }

    /**
     * Define relationship with rio connecton users model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rio_connection_users()
    {
        return $this->hasMany(RioConnectionUser::class);
    }

    /**
     * Send email notification to RIO connection.
     *
     * @param \App\Models\Rio $connectionSender
     * @param \App\Models\Rio $connectionReceiver
     * @return bool|void
     */
    public function sendEmailToConnection($connectionSender, $connectionReceiver)
    {
        // Check receiver if allowed to receive email
        if (NotificationRejectSetting::isRejectedEmail(
            $connectionReceiver,
            MailTemplates::CONNECTION_APPLICATION
        )) {
            return false;
        }

        // Get user information
        $user = User::whereRioId($connectionReceiver->id)->firstOrFail();

        // Send email to RIO receiver
        Notification::sendNow(
            $user,
            new ConnectionApplicationNotification(
                $connectionSender,
                $connectionReceiver
            )
        );
    }

    /**
     * Check RIO's connection type status
     *
     * @param Rio $rio
     * @param boolean $cancelDisconnect
     *
     * @return mixed
     */
    public static function connectionStatus($rio, $cancelDisconnect = true)
    {
        /** @var User */
        $user = auth()->user();
        $connectionUsers = RioConnectionUser::getRioConnectionUsersPair($rio);
        $status['connection_id'] = key((array)$connectionUsers);

        $connection = $user->rio->rio_connections->where('id', key((array)$connectionUsers))->first();

        if (empty($connection)) {
            $connection = $rio->rio_connections->where('id', key((array)$connectionUsers))->first();
        }

        if ($connection && $cancelDisconnect) {
            $status['status'] = $connection->status == 0 ? ConnectionStatusType::CANCELLATION : ConnectionStatusType::DISCONNECTION;
            return $status;
        }

        $status['status'] = RioPrivacy::checkPrivacySettingStatus($rio);

        return $status;
    }

    /**
     * Check NEO's connection type status
     *
     * @param mixed $rio
     * @param mixed $neo
     * @param bool $cancelDisconnect
     *
     * @return int
     */
    public static function neoConnectionStatus($rio, $neo, $cancelDisconnect = true)
    {
        $privacy = $rio->rio_privacy;
        $isBelongToApplyingNeo = NeoBelong::isRioBelongsToApplyingNeo($rio, $neo);
        $isHaveRioMutualConnection = RioConnection::isMutualConnectionExists($rio);
        $isNeoConnected = NeoConnection::isNeoConnected($rio);

        $connection = NeoConnection::whereNeoId($neo->id)
            ->whereConnectionRioId($rio->id)
            ->whereStatus(EnumsConnectionStatuses::APPLYING_BY_NEO)
            ->first();

        if ($connection && $cancelDisconnect) {
            return ($connection->status == ConnectionStatusType::NOT_ALLOWED ? ConnectionStatusType::CANCELLATION : ConnectionStatusType::DISCONNECTION);
        }

        $alreadyConnected = NeoConnection::whereNeoId($neo->id)
            ->whereConnectionRioId($rio->id)
            ->whereStatus(EnumsConnectionStatuses::CONNECTED)
            ->exists();

        if ($alreadyConnected) {
            return ConnectionStatusType::DISCONNECTION;
        }

        $status = ConnectionStatusType::NOT_ALLOWED;

        $isAcceptApplication = ($privacy->accept_connections == AcceptConnectionType::ACCEPT_APPLICATION);
        $isAcceptConnection = ($privacy->accept_connections == AcceptConnectionType::ACCEPT_CONNECTION);
        $isNeoAcceptApplication = ($privacy->accept_connections_by_neo == AcceptConnectionByNeoType::ACCEPT_APPLICATION);
        $isNeoAcceptConnection = ($privacy->accept_connections_by_neo == AcceptConnectionByNeoType::ACCEPT_CONNECTION);
        $isNeoAcceptUnselected = ($privacy->accept_connections_by_neo == AcceptConnectionByNeoType::UNSELECTED);
        $isPrivateConnection = ($privacy->accept_connections == AcceptConnectionType::PRIVATE_CONNECTION);


        switch ($privacy) {
                // Allowed to connect with Rio
            case ($isAcceptApplication && $isNeoAcceptApplication):
            case ($isAcceptApplication && $isNeoAcceptConnection):
            case ($isAcceptApplication && $isNeoAcceptUnselected):
                $status = ConnectionStatusType::ALLOWED;
                break;

                // Allowed if there is mutual RIO connection and RIO is part of NEO which other RIO also belongs
            case ($isAcceptConnection && $isNeoAcceptApplication):
                if ($isHaveRioMutualConnection && $isBelongToApplyingNeo) {
                    $status = ConnectionStatusType::ALLOWED;
                }
                break;

                // Allowed if there is mutual RIO connection and if theres a Neo connection in both Rio Neo's
            case ($isAcceptConnection && $isNeoAcceptConnection):
                if ($isHaveRioMutualConnection && $isNeoConnected) {
                    $status = ConnectionStatusType::ALLOWED;
                }
                break;

                // Allowed if there is mutual RIO connection
            case ($isAcceptConnection && $isNeoAcceptUnselected):
                if ($isHaveRioMutualConnection) {
                    $status = ConnectionStatusType::ALLOWED;
                }
                break;

                // Allowed to connect if RIO is part of NEO
            case ($isPrivateConnection && $isNeoAcceptApplication):
                if ($isBelongToApplyingNeo) {
                    $status = ConnectionStatusType::ALLOWED;
                }
                break;

                // Allowed if theres a Neo connection in both Rio Neo's
            case ($isPrivateConnection && $isNeoAcceptConnection):
                if ($isNeoConnected) {
                    $status = ConnectionStatusType::ALLOWED;
                }
                break;

            case ($isPrivateConnection && $isNeoAcceptUnselected):
                $status = ConnectionStatusType::HIDDEN;
                break;

            default:
                $status = ConnectionStatusType::NOT_ALLOWED;
                break;
        }

        return $status;
    }

    /**
     * Check user Rio and other Rio if mutual connection exists
     *
     * @param Rio $rio
     * @return boolean
     */
    public static function isMutualConnectionExists($rio)
    {
        /** @var User */
        $user = auth()->user();

        /**
         * Subquery for all rio connections that includes the current user or target user
         *
         * Output columns:
         * `rio_connection_id`
         * `rio_id`
         */
        $participatingQuery = DB::table('rio_connection_users as participating_connection_users')
            ->select(
                'participating_connection_users.rio_connection_id',
                'participating_connection_users.rio_id',
            )
            ->join('rio_connections', 'participating_connection_users.rio_connection_id', '=', 'rio_connections.id')
            ->whereIn('participating_connection_users.rio_id', [$user->rio_id, $rio->id])
            ->where('rio_connections.status', RioConnectionStatusType::AFFILIATED)
            ->whereNull('rio_connections.deleted_at')
            ->whereNull('participating_connection_users.deleted_at');

        /**
         * Subquery for fetching the connected user to the current or target user
         *
         * Output columns:
         * `connected_user`
         * `participant_user`
         */
        $connectedParticipantsQuery = DB::table('rio_connection_users as connected_participants_users')
            ->select(
                'connected_participants_users.rio_id as connected_user',
                'participating_connection_users.rio_id as participant_user',
            )
            ->rightJoinSub($participatingQuery, 'participating_connection_users', function ($join) {
                $join->on('connected_participants_users.rio_connection_id', '=', 'participating_connection_users.rio_connection_id');
            })
            ->whereNotIn('connected_participants_users.rio_id', [$user->rio_id, $rio->id])
            ->whereNull('connected_participants_users.deleted_at')
            ->groupBy('connected_user', 'participant_user');

        /**
         * Main query for fetching if a connected user is duplicated from the list of pairs
         *
         * When there is a duplicated user, that means there us a
         * mutual user between current and target user. Filters out non-mutual records
         *
         * Output columns:
         * `connected_user`
         * `is_mutual`
         */
        return DB::query()
            ->fromSub($connectedParticipantsQuery, 'user_map_connections')
            ->select(
                'user_map_connections.connected_user'
            )
            ->selectRaw('
                (CASE
                    WHEN COUNT(user_map_connections.participant_user) > 1
                        THEN 1
                    ELSE 0
                END) AS is_mutual
            ')
            ->groupBy('user_map_connections.connected_user')
            ->having('is_mutual', '=', '1')
            ->exists();
    }

    /**
     * Scope a query to get the list of applications request to selected service.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed  $service
     * @param array  $options
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConnectionApplicationList($query, $service, $options)
    {
        /** @var User */
        $user = auth()->user();

        $defaultProfileImage = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.neo_profile_image');
        $neoCondition = false;
        $rioCondition = false;
        $mode = $options['mode'] ?? ListFilters::SHOW_ALL;

        switch ($mode) {
            case ListFilters::ONLY_NEO:
                $neoCondition = true;
                break;
            case ListFilters::ONLY_RIO:
                $rioCondition = true;
                break;
            default:
                $neoCondition = true;
                $rioCondition = true;
                break;
        }

        $neoConnectionNeo = DB::table('neo_connections')
            ->join('neos', 'neo_connections.connection_neo_id', '=', 'neos.id')
            ->join('neo_profiles', 'neos.id', '=', 'neo_profiles.neo_id')
            ->select(
                'neo_connections.id as connection_id',
                'neos.id as id',
                'neos.organization_name as name',
                DB::raw("'NEO' as service"),
                DB::raw("CASE
                            WHEN neo_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                            ELSE '" . $defaultProfileImage . "'
                        END AS profile_photo"),
                'neo_connections.message as message',
            )
            ->where('neo_connections.neo_id', $service->data->id)
            ->where('neo_connections.status', ConnectionStatuses::REQUESTING)
            ->whereNull('neo_connections.deleted_at');

        $neoConnectionRio = DB::table('neo_connections')
            ->join('rios', 'neo_connections.connection_rio_id', '=', 'rios.id')
            ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
            ->select(
                'neo_connections.id as connection_id',
                'rios.id as id',
                DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                DB::raw("'RIO' as service"),
                DB::raw("CASE
                            WHEN rio_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                            ELSE '" . $defaultProfileImage . "'
                        END AS profile_photo"),
                'neo_connections.message as message',
            )
            ->where('neo_connections.neo_id', $service->data->id)
            ->where('neo_connections.status', ConnectionStatuses::REQUESTING)
            ->whereNull('neo_connections.deleted_at');

        $neoInNeoconnection = DB::table('neo_connections')
            ->join('neos', 'neo_connections.neo_id', '=', 'neos.id')
            ->join('neo_profiles', 'neo_connections.neo_id', '=', 'neo_profiles.neo_id')
            ->select(
                'neo_connections.id as connection_id',
                'neos.id as id',
                'organization_name as name',
                DB::raw("'NEO' as service"),
                DB::raw("CASE
                            WHEN neo_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                            ELSE '" . $defaultProfileImage . "'
                        END AS profile_photo"),
                'neo_connections.message as message',
            )
            ->where('neo_connections.connection_rio_id', $service->data->id)
            ->where('neo_connections.status', '=', EnumsConnectionStatuses::APPLYING_BY_NEO)
            ->whereNull('neo_connections.deleted_at');

        $rioInRioConnection = DB::table('rio_connections')->join('rio_connection_users', 'rio_connections.id', '=', 'rio_connection_users.rio_connection_id')
            ->join('rios', 'rio_connections.created_rio_id', '=', 'rios.id')
            ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
            ->select(
                'rio_connections.id as connection_id',
                'rios.id as id',
                DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                DB::raw("'RIO' as service"),
                DB::raw("CASE
                            WHEN rio_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                            ELSE '" . $defaultProfileImage . "'
                        END AS profile_photo"),
                'rio_connections.message as message',
            )
            ->where('rio_connection_users.rio_id', $service->data->id)
            ->where('rio_connections.status', ConnectionStatuses::REQUESTING)
            ->where('rio_connections.created_rio_id', '<>', $service->data->id)
            ->whereNull('rio_connections.deleted_at');

        $query->join('rio_connection_users', 'rio_connections.id', '=', 'rio_connection_users.rio_connection_id')
            ->join('rios', 'rio_connections.created_rio_id', '=', 'rios.id')
            ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
            ->select(
                'rio_connections.id as connection_id',
                'rios.id as id',
                DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                DB::raw("'RIO' as service"),
                DB::raw("CASE
                            WHEN rio_profiles.profile_photo IS NOT NULL
                                THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                            ELSE '" . $defaultProfileImage . "'
                        END AS profile_photo"),
                'rio_connections.message as message',
            )
            ->when($rioCondition && $neoCondition && $service->type === ServiceSelectionTypes::RIO, function ($q1) use ($service, $neoInNeoconnection) {
                $q1->where('rio_connection_users.rio_id', $service->data->id)
                    ->where('rio_connections.status', ConnectionStatuses::REQUESTING)
                    ->where('rio_connections.created_rio_id', '<>', $service->data->id)
                    ->whereNull('rio_connections.deleted_at');

                $q1->union($neoInNeoconnection);
            }, function ($q1) {
                return $q1->where('rio_connection_users.rio_id', 0);
            })
            ->when($rioCondition && !$neoCondition && $service->type === ServiceSelectionTypes::RIO, function ($q4) use ($rioInRioConnection) {
                return $q4->union($rioInRioConnection);
            })
            ->when($neoCondition && $service->type === ServiceSelectionTypes::RIO, function ($q3) use ($neoInNeoconnection) {
                return $q3->union($neoInNeoconnection);
            })
            ->when($neoCondition && $service->type === ServiceSelectionTypes::NEO, function ($q1) use ($neoConnectionNeo) {
                return $q1->union($neoConnectionNeo);
            })
            ->when($rioCondition && $service->type === ServiceSelectionTypes::NEO, function ($q2) use ($neoConnectionRio) {
                return $q2->union($neoConnectionRio);
            });

        return $query;
    }

    /**
     * Scope a query to get connected RIO and NEO to selected service.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed  $service
     * @param array  $options
     * @param mixed $isImage
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConnectedList($query, $service, $options, $isImage = null)
    {
        /** @var User */
        $user = auth()->user();

        if ($isImage) {
            $defaultProfileDirectory = null;
        } else {
            $defaultProfileDirectory = config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        }

        $defaultProfileImage = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.neo_profile_image');

        $neoCondition = false;
        $rioCondition = false;
        $searchKey = isset($options['search']) && !is_null($options['search']);
        $kanaSearchKey = isset($options['kana_search']) && !is_null($options['kana_search']);
        $mode = $options['mode'] ?? ListFilters::SHOW_ALL;

        switch ($mode) {
            case ListFilters::ONLY_NEO:
                $neoCondition = true;
                break;
            case ListFilters::ONLY_RIO:
                $rioCondition = true;
                break;
            default:
                $neoCondition = true;
                $rioCondition = true;
                break;
        }

        if ($service->type === ServiceSelectionTypes::RIO) {
            //get connected NEO
            $neoConnectionNeo = DB::table('neo_connections')
                ->join('neos', 'neo_connections.neo_id', '=', 'neos.id')
                ->join('neo_profiles', 'neos.id', '=', 'neo_profiles.neo_id')
                ->select(
                    'neos.id as id',
                    'neos.organization_name as name',
                    'neos.organization_kana as kana',
                    DB::raw("'NEO' as service"),
                    DB::raw("CASE 
                            WHEN neo_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                        END AS profile_photo"),
                    DB::raw("CASE 
                        WHEN neo_profiles.profile_photo IS NULL
                        THEN '" . $defaultProfileImage . "'
                        ELSE CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                    END AS profile_picture"),
                    'neo_profiles.nationality as address_nationality',
                    'neo_profiles.prefecture as address_prefecture',
                    'neo_profiles.city as address_city',
                    'neo_profiles.address as address',
                    'neo_profiles.building as address_building',
                )
                ->where('neo_connections.status', ConnectionStatuses::CONNECTED)
                ->where('neo_connections.connection_rio_id', $service->data->id)
                ->whereNull('neo_connections.deleted_at')
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->orHaving('name', 'LIKE', "%{$options['search']}%");
                })
                ->when($kanaSearchKey, function ($q3) use ($options) {
                    return $q3->orHaving('kana', 'LIKE', "%{$options['kana_search']}%");
                });

            //get connected RIO
            $rio1 = DB::table('rio_connections')
                ->join('rio_connection_users', 'rio_connections.id', '=', 'rio_connection_users.rio_connection_id')
                ->join('rios', 'rio_connections.created_rio_id', '=', 'rios.id')
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->select(
                    'rios.id as id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("CONCAT(rios.family_kana,' ',rios.first_kana) as kana"),
                    DB::raw("'RIO' as service"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_picture"),
                    'rio_profiles.present_address_nationality as address_nationality',
                    'rio_profiles.present_address_prefecture as address_prefecture',
                    'rio_profiles.present_address_city as address_city',
                    'rio_profiles.present_address as address',
                    'rio_profiles.present_address_building as address_building',
                )
                ->where('rio_connections.status', ConnectionStatuses::CONNECTED)
                ->where('rio_connections.created_rio_id', '<>', $service->data->id)
                ->where('rio_connection_users.rio_id', $service->data->id)
                ->whereNull('rio_connections.deleted_at')
                ->whereNull('rio_connection_users.deleted_at')
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->orHaving('name', 'LIKE', "%{$options['search']}%");
                })
                ->when($kanaSearchKey, function ($q3) use ($options) {
                    return $q3->orHaving('kana', 'LIKE', "%{$options['kana_search']}%");
                });

            $query->join('rio_connection_users', 'rio_connections.id', '=', 'rio_connection_users.rio_connection_id')
                ->join('rios', 'rio_connection_users.rio_id', '=', 'rios.id')
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->select(
                    'rios.id as id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("CONCAT(rios.family_kana,' ',rios.first_kana) as kana"),
                    DB::raw("'RIO' as service"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo"),
                    DB::raw("CASE 
                        WHEN rio_profiles.profile_photo IS NULL
                        THEN '" . $defaultProfileImage . "'
                        ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                    END AS profile_picture"),
                    'rio_profiles.present_address_nationality as address_nationality',
                    'rio_profiles.present_address_prefecture as address_prefecture',
                    'rio_profiles.present_address_city as address_city',
                    'rio_profiles.present_address as address',
                    'rio_profiles.present_address_building as address_building',
                )
                ->when($rioCondition, function ($q1) use ($service, $rio1, $searchKey, $kanaSearchKey, $options) {
                    $q1->where('rio_connections.status', ConnectionStatuses::CONNECTED)
                        ->where('rio_connection_users.rio_id', '<>', $service->data->id)
                        ->where('rio_connections.created_rio_id', $service->data->id)
                        ->whereNull('rio_connections.deleted_at')
                        ->whereNull('rio_connection_users.deleted_at')

                        ->when($searchKey, function ($q3) use ($options) {
                            return $q3->orHaving('name', 'LIKE', "%{$options['search']}%");
                        })
                        ->when($kanaSearchKey, function ($q3) use ($options) {
                            return $q3->orHaving('kana', 'LIKE', "%{$options['kana_search']}%");
                        })
                        ->union($rio1);
                }, function ($q1) {
                    return $q1->where('rio_connection_users.rio_id', 0);
                })
                ->when($neoCondition, function ($q2) use ($neoConnectionNeo) {
                    return $q2->union($neoConnectionNeo);
                });
        }

        if ($service->type === ServiceSelectionTypes::NEO) {
            //get connected NEO
            $neoConnectionNeo1 = DB::table('neo_connections')
                ->join('neos', 'neo_connections.neo_id', '=', 'neos.id')
                ->join('neo_profiles', 'neos.id', '=', 'neo_profiles.neo_id')
                ->select(
                    'neos.id as id',
                    'neos.organization_name as name',
                    'neos.organization_kana as kana',
                    DB::raw("'NEO' as service"),
                    DB::raw("CASE 
                            WHEN neo_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                        END AS profile_photo"),
                    DB::raw("CASE 
                        WHEN neo_profiles.profile_photo IS NULL
                        THEN '" . $defaultProfileImage . "'
                        ELSE CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                    END AS profile_picture"),
                    'neo_profiles.nationality as address_nationality',
                    'neo_profiles.prefecture as address_prefecture',
                    'neo_profiles.city as address_city',
                    'neo_profiles.address as address',
                    'neo_profiles.building as address_building',
                )
                ->where('neo_connections.status', ConnectionStatuses::CONNECTED)
                ->where('neo_connections.connection_neo_id', $service->data->id)
                ->whereNull('neo_connections.deleted_at')
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->orHaving('name', 'LIKE', "%{$options['search']}%");
                })
                ->when($kanaSearchKey, function ($q3) use ($options) {
                    return $q3->orHaving('kana', 'LIKE', "%{$options['kana_search']}%");
                });

            $neoConnectionNeo2 = DB::table('neo_connections')
                ->join('neos', 'neo_connections.connection_neo_id', '=', 'neos.id')
                ->join('neo_profiles', 'neos.id', '=', 'neo_profiles.neo_id')
                ->select(
                    'neos.id as id',
                    'neos.organization_name as name',
                    'neos.organization_kana as kana',
                    DB::raw("'NEO' as service"),
                    DB::raw("CASE 
                            WHEN neo_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                        END AS profile_photo"),
                    DB::raw("CASE 
                        WHEN neo_profiles.profile_photo IS NULL
                        THEN '" . $defaultProfileImage . "'
                        ELSE CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                    END AS profile_picture"),
                    'neo_profiles.nationality as address_nationality',
                    'neo_profiles.prefecture as address_prefecture',
                    'neo_profiles.city as address_city',
                    'neo_profiles.address as address',
                    'neo_profiles.building as address_building',
                )
                ->where('neo_connections.status', ConnectionStatuses::CONNECTED)
                ->where('neo_connections.neo_id', $service->data->id)
                ->whereNull('neo_connections.deleted_at')
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->orHaving('name', 'LIKE', "%{$options['search']}%");
                })
                ->when($kanaSearchKey, function ($q3) use ($options) {
                    return $q3->orHaving('kana', 'LIKE', "%{$options['kana_search']}%");
                });

            //get connected RIO
            $neoConnectionRio = DB::table('neo_connections')
                ->join('rios', 'neo_connections.connection_rio_id', '=', 'rios.id')
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->select(
                    'rios.id as id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("CONCAT(rios.family_kana,' ',rios.first_kana) as kana"),
                    DB::raw("'RIO' as service"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo"),
                    DB::raw("CASE 
                        WHEN rio_profiles.profile_photo IS NULL
                        THEN '" . $defaultProfileImage . "'
                        ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                    END AS profile_picture"),
                    'rio_profiles.present_address_nationality as address_nationality',
                    'rio_profiles.present_address_prefecture as address_prefecture',
                    'rio_profiles.present_address_city as address_city',
                    'rio_profiles.present_address as address',
                    'rio_profiles.present_address_building as address_building',
                )
                ->where('neo_connections.status', ConnectionStatuses::CONNECTED)
                ->where('neo_connections.neo_id', $service->data->id)
                ->whereNull('neo_connections.deleted_at')
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->orHaving('name', 'LIKE', "%{$options['search']}%");
                })
                ->when($kanaSearchKey, function ($q3) use ($options) {
                    return $q3->orHaving('kana', 'LIKE', "%{$options['kana_search']}%");
                });

            $query->join('rio_connection_users', 'rio_connections.id', '=', 'rio_connection_users.rio_connection_id')
                ->join('rios', 'rio_connection_users.rio_id', '=', 'rios.id')
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->select(
                    'rios.id as id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("CONCAT(rios.family_kana,' ',rios.first_kana) as kana"),
                    DB::raw("'RIO' as service"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo"),
                    DB::raw("CASE 
                        WHEN rio_profiles.profile_photo IS NULL
                        THEN '" . $defaultProfileImage . "'
                        ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                    END AS profile_picture"),
                    'rio_profiles.present_address_nationality as address_nationality',
                    'rio_profiles.present_address_prefecture as address_prefecture',
                    'rio_profiles.present_address_city as address_city',
                    'rio_profiles.present_address as address',
                    'rio_profiles.present_address_building as address_building',
                )
                ->where('rio_connection_users.rio_id', 0)
                ->when($rioCondition, function ($q2) use ($neoConnectionRio) {
                    return $q2->union($neoConnectionRio);
                })
                ->when($neoCondition, function ($q2) use ($neoConnectionNeo1) {
                    return $q2->union($neoConnectionNeo1);
                })
                ->when($neoCondition, function ($q2) use ($neoConnectionNeo2) {
                    return $q2->union($neoConnectionNeo2);
                });
        }

        return $query->orderBy('kana', 'asc');
    }

    /**
     * Scope a query to get connected RIO and NEO to selected service.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed  $service
     * @param array  $options
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConnectedNoAccessList($query, $service, $options)
    {
        /** @var User */
        $user = auth()->user();

        $defaultProfileDirectory = config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $searchKey = isset($options['search']) && !is_null($options['search']);

        $defaultProfileImage = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.neo_profile_image');

        // Get permitted NEO
        $neoPermitted = DB::table('document_accesses')
                ->join('neos', 'document_accesses.neo_id', '=', 'neos.id')
                ->join('neo_profiles', 'neos.id', '=', 'neo_profiles.neo_id')
                ->select(
                    'document_accesses.document_id as document_id',
                    'document_accesses.id as access_id',
                    'neos.id as id',
                    'neos.organization_name as name',
                    DB::raw("'NEO' as service"),
                    DB::raw("CASE 
                            WHEN neo_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                        END AS profile_photo")
                )
                ->where('document_accesses.document_id', $options['id'])
                ->whereNull('document_accesses.deleted_at')
                ->pluck('id');

        // Get permitted RIO
        $rioPermitted = DB::table('document_accesses')
                ->join('rios', 'document_accesses.rio_id', '=', 'rios.id')
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->select(
                    'document_accesses.document_id as document_id',
                    'document_accesses.id as access_id',
                    'rios.id as id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("'RIO' as service"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo")
                )
                ->where('document_accesses.document_id', $options['id'])
                ->whereNull('document_accesses.deleted_at')
                ->pluck('id');

        // Get permitted NEO Groups
        $neoGroupPermitted = DB::table('document_accesses')
            ->join('neo_groups', 'document_accesses.neo_group_id', '=', 'neo_groups.id')
            ->select(
                'document_accesses.document_id as document_id',
                'document_accesses.id as access_id',
                'neo_groups.id as id',
                'neo_groups.group_name as name',
                DB::raw("'NEO_Group' as service"),
                DB::raw('"' . $defaultProfileImage. '" as profile_photo')
            )
            ->where('document_accesses.document_id', $options['id'])
            ->whereNull('document_accesses.deleted_at')
            ->pluck('id');

        if ($service->type === ServiceSelectionTypes::RIO) {
            //get connected NEO
            $neoConnectionNeo = DB::table('neo_connections')
                ->join('neos', 'neo_connections.neo_id', '=', 'neos.id')
                ->join('neo_profiles', 'neos.id', '=', 'neo_profiles.neo_id')
                ->select(
                    'neos.id as id',
                    'neos.organization_name as name',
                    DB::raw("'NEO' as service"),
                    DB::raw("CASE 
                            WHEN neo_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                        END AS profile_photo")
                )
                ->where('neo_connections.status', ConnectionStatuses::CONNECTED)
                ->where('neo_connections.connection_rio_id', $service->data->id)
                ->whereNull('neo_connections.deleted_at')
                ->whereNotIn('neos.id', $neoPermitted)
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->having('name', 'LIKE', "%{$options['search']}%");
                });

            //get connected RIO
            $rio1 = DB::table('rio_connections')
                ->join('rio_connection_users', 'rio_connections.id', '=', 'rio_connection_users.rio_connection_id')
                ->join('rios', 'rio_connections.created_rio_id', '=', 'rios.id')
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->select(
                    'rios.id as id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("'RIO' as service"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo")
                )
                ->where('rio_connections.status', ConnectionStatuses::CONNECTED)
                ->where('rio_connections.created_rio_id', '<>', $service->data->id)
                ->where('rio_connection_users.rio_id', $service->data->id)
                ->whereNull('rio_connection_users.deleted_at')
                ->whereNotIn('rios.id', $rioPermitted)
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->having('name', 'LIKE', "%{$options['search']}%");
                });

            //Get Neo Group where Rio is a member
            $neoGroupMember = DB::table('neo_group_users')
                ->join('neo_groups', 'neo_group_users.neo_group_id', '=', 'neo_groups.id')
                ->select(
                    'neo_groups.id as id',
                    DB::raw("neo_groups.group_name as name"),
                    DB::raw("'NEO_Group' as service"),
                    DB::raw('"' . $defaultProfileImage. '" as profile_photo')
                )
                ->where('neo_group_users.rio_id', $user->rio->id)
                ->whereNull('neo_groups.deleted_at')
                ->whereNotIn('neo_groups.id', $neoGroupPermitted)
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->having('name', 'LIKE', "%{$options['search']}%");
                });

            $query->join('rio_connection_users', 'rio_connections.id', '=', 'rio_connection_users.rio_connection_id')
                ->join('rios', 'rio_connection_users.rio_id', '=', 'rios.id')
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->select(
                    'rios.id as id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("'RIO' as service"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo")
                )
                ->where('rio_connections.status', ConnectionStatuses::CONNECTED)
                ->where('rio_connection_users.rio_id', '<>', $service->data->id)
                ->where('rio_connections.created_rio_id', $service->data->id)
                ->whereNotIn('rios.id', $rioPermitted)
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->having('name', 'LIKE', "%{$options['search']}%");
                })
                ->union($rio1)
                ->union($neoConnectionNeo)
                ->union($neoGroupMember);
        }

        if ($service->type === ServiceSelectionTypes::NEO) {
            //get participating RIO in NEO
            $participatingNeo = DB::table('neo_belongs')
                ->join('rios', 'neo_belongs.rio_id', '=', 'rios.id')
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->select(
                    'rios.id as id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("'NEO_BELONG' as service"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo")
                )
                ->where('neo_belongs.neo_id', $service->data->id)
                ->where('neo_belongs.status', 1)
                ->whereNotIn('rios.id', $rioPermitted)
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->having('name', 'LIKE', "%{$options['search']}%");
                });

            $neoConnectionNeo = DB::table('neo_connections')
                ->join('neos', 'neo_connections.connection_neo_id', '=', 'neos.id')
                ->join('neo_profiles', 'neos.id', '=', 'neo_profiles.neo_id')
                ->select(
                    'neos.id as id',
                    'neos.organization_name as name',
                    DB::raw("'NEO' as service"),
                    DB::raw("CASE 
                                WHEN neo_profiles.profile_photo IS NULL
                                THEN '" . $defaultProfileImage . "'
                                ELSE CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                            END AS profile_photo")
                )
                ->where('neo_connections.status', ConnectionStatuses::CONNECTED)
                ->where('neo_connections.neo_id', $service->data->id)
                ->whereNull('neo_connections.deleted_at')
                ->whereNotIn('neos.id', $neoPermitted)
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->having('name', 'LIKE', "%{$options['search']}%");
                });

            //get connected RIO
            $neoConnectionRio = DB::table('neo_connections')
                ->join('rios', 'neo_connections.connection_rio_id', '=', 'rios.id')
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->select(
                    'rios.id as id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("'RIO' as service"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo")
                )
                ->where('neo_connections.status', ConnectionStatuses::CONNECTED)
                ->where('neo_connections.neo_id', $service->data->id)
                ->whereNull('neo_connections.deleted_at')
                ->whereNotIn('rios.id', $rioPermitted)
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->having('name', 'LIKE', "%{$options['search']}%");
                });

            //Get Neo Group created by Rio
            $neoGroupOwner = DB::table('neo_groups')
                ->select(
                    'neo_groups.id as id',
                    DB::raw("neo_groups.group_name as name"),
                    DB::raw("'NEO_Group' as service"),
                    DB::raw('"' . $defaultProfileImage. '" as profile_photo')
                )
                ->where('neo_groups.rio_id', $user->rio->id)
                ->where('neo_groups.neo_id', $service->data->id)
                ->whereNull('neo_groups.deleted_at')
                ->whereNotIn('neo_groups.id', $neoGroupPermitted)
                ->when($searchKey, function ($q3) use ($options) {
                    return $q3->having('name', 'LIKE', "%{$options['search']}%");
                });

            $query->join('rio_connection_users', 'rio_connections.id', '=', 'rio_connection_users.rio_connection_id')
                ->join('rios', 'rio_connection_users.rio_id', '=', 'rios.id')
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->select(
                    'rios.id as id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("'RIO' as service"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileImage . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo")
                )
                ->whereNotIn('rios.id', $rioPermitted)
                ->where('rio_connection_users.rio_id', 0)
                ->union($participatingNeo)
                ->union($neoConnectionRio)
                ->union($neoConnectionNeo)
                ->union($neoGroupOwner);
        }

        return $query;
    }
}
