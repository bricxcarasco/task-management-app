<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Rio\AcceptConnectionType;
use App\Enums\Rio\AcceptConnectionByNeoType;
use App\Enums\Rio\ConnectionStatusType;

/**
 * App\Models\RioPrivacy
 *
 * @property int $id id for Laravel
 * @property int $rio_id
 * @property int $accept_connections
 * @property int $accept_connections_by_neo
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Rio $rio
 * @method static \Illuminate\Database\Eloquent\Builder|RioPrivacy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RioPrivacy newQuery()
 * @method static \Illuminate\Database\Query\Builder|RioPrivacy onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RioPrivacy query()
 * @method static \Illuminate\Database\Eloquent\Builder|RioPrivacy whereAcceptConnections($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioPrivacy whereAcceptConnectionsByNeo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioPrivacy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioPrivacy whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioPrivacy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioPrivacy whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioPrivacy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|RioPrivacy withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RioPrivacy withoutTrashed()
 * @mixin \Eloquent
 */
class RioPrivacy extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rio_privacies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rio_id',
        'accept_connections',
        'accept_connections_by_neo',
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
     * Check connection status using Rio privacy settings
     *
     * @param Rio $rio
     * @return int
     */
    public static function checkPrivacySettingStatus($rio)
    {
        $status = ConnectionStatusType::NOT_ALLOWED;

        /** @var RioPrivacy */
        $privacy = $rio->rio_privacy;

        $isHaveRioMutualConnection = RioConnection::isMutualConnectionExists($rio);
        $isInTheSameNeo = NeoBelong::isBothRioBelongsToNeo($rio);

        switch ($privacy) {
            // Allowed to connect with Rio
            case $privacy->accept_connections == AcceptConnectionType::ACCEPT_APPLICATION && $privacy->accept_connections_by_neo == AcceptConnectionByNeoType::ACCEPT_APPLICATION:
            case $privacy->accept_connections == AcceptConnectionType::ACCEPT_APPLICATION && $privacy->accept_connections_by_neo == AcceptConnectionByNeoType::ACCEPT_CONNECTION:
            case $privacy->accept_connections == AcceptConnectionType::ACCEPT_APPLICATION && $privacy->accept_connections_by_neo == AcceptConnectionByNeoType::UNSELECTED:
                $status = ConnectionStatusType::ALLOWED;
                break;

            // Allowed if there is mutual RIO connection and RIO is part of NEO which other RIO also belongs
            case $privacy->accept_connections == AcceptConnectionType::ACCEPT_CONNECTION && $privacy->accept_connections_by_neo == AcceptConnectionByNeoType::ACCEPT_APPLICATION:
                if ($isHaveRioMutualConnection && $isInTheSameNeo) {
                    $status = ConnectionStatusType::ALLOWED;
                }
                break;

            // Allowed if there is mutual RIO connection and if theres a Neo connection in both Rio Neo's
            case $privacy->accept_connections == AcceptConnectionType::ACCEPT_CONNECTION && $privacy->accept_connections_by_neo == AcceptConnectionByNeoType::ACCEPT_CONNECTION:
                $isNeoConnected = NeoConnection::isNeoConnected($rio);
                if ($isHaveRioMutualConnection && $isNeoConnected) {
                    $status = ConnectionStatusType::ALLOWED;
                }
                break;

            // Allowed if there is mutual RIO connection
            case $privacy->accept_connections == AcceptConnectionType::ACCEPT_CONNECTION && $privacy->accept_connections_by_neo == AcceptConnectionByNeoType::UNSELECTED:
                if ($isHaveRioMutualConnection) {
                    $status = ConnectionStatusType::ALLOWED;
                }
                break;

            // Allowed to connect if RIO is part of NEO which other RIO also belongs
            case $privacy->accept_connections == AcceptConnectionType::PRIVATE_CONNECTION && $privacy->accept_connections_by_neo == AcceptConnectionByNeoType::ACCEPT_APPLICATION:
                if ($isInTheSameNeo) {
                    $status = ConnectionStatusType::ALLOWED;
                }
                break;

            // Allowed if theres a Neo connection in both Rio Neo's
            case $privacy->accept_connections == AcceptConnectionType::PRIVATE_CONNECTION && $privacy->accept_connections_by_neo == AcceptConnectionByNeoType::ACCEPT_CONNECTION:
                $isNeoConnected = NeoConnection::isNeoConnected($rio);
                if ($isNeoConnected) {
                    $status = ConnectionStatusType::ALLOWED;
                }
                break;

            // Private connection
            case $privacy->accept_connections == AcceptConnectionType::PRIVATE_CONNECTION && $privacy->accept_connections_by_neo == AcceptConnectionByNeoType::UNSELECTED:
                $status = ConnectionStatusType::HIDDEN;
                break;

            default:
                $status = ConnectionStatusType::NOT_ALLOWED;
                break;
        }

        return $status;
    }
}
