<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RioConnectionUser extends Model
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
        'rio_connection_id',
        'rio_id'
    ];

    /**
     * Define relationship with rio connecton model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio_connection()
    {
        return $this->belongsTo(RioConnection::class);
    }

    /**
     * Scope a query to get rio connection user data except given Rio.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $rio_id
     *
     * @return mixed
     */
    public function scopeExceptRio($query, $rio_id)
    {
        return $query->where('rio_id', "!=", $rio_id)->first();
    }

    /**
     * Get rio connection users pair per auth user and other rio
     *
     * @param mixed $rio
     * @return mixed
     */
    public static function getRioConnectionUsersPair($rio)
    {
        /** @var User */
        $user = auth()->user();

        $connections = $user->rio->rio_connections;
        $connectionUsers = self::checkConnections($connections, $rio->id);

        if (empty($connectionUsers)) {
            $connections = $rio->rio_connections;
            $connectionUsers = self::checkConnections($connections, $user->id);
        }

        return $connectionUsers;
    }

    /**
     * Get rio connection users pair per auth user and other rio
     *
     * @param int $id
     * @param mixed $connections
     *
     * @return mixed
     */
    public static function checkConnections($connections, $id)
    {
        $connectionUsers = [];

        foreach ($connections as $connection) {
            $checkConnectedRios = $connection->rio_connection_users->where('rio_id', $id)->first();
            if ($checkConnectedRios) {
                $connectionUsers[$connection->id] = $connection->rio_connection_users;
            }
        }

        return $connectionUsers;
    }
}
