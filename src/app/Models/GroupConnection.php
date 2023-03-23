<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\GroupConnection
 *
 * @property int $id id for Laravel
 * @property string $group_name
 * @property int $status 0:申請中 、1:つながり状態
 * @property int $rio_id
 * @property \Illuminate\Support\Carbon $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupConnectionUser[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnection newQuery()
 * @method static \Illuminate\Database\Query\Builder|GroupConnection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnection query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnection whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnection whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnection whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupConnection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GroupConnection withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GroupConnection withoutTrashed()
 * @mixin \Eloquent
 */
class GroupConnection extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'group_connections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_name',
        'status',
        'rio_id',
        'chat_id'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_owner',
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
     * Define relationship with group_connection_users table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(GroupConnectionUser::class);
    }

    /**
     * Define relationship with chats table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Get group connection is owner checker attribute.
     *
     * @return bool
     */
    public function getIsOwnerAttribute()
    {
        /** @var User */
        $user = auth()->user();

        return ($user->rio_id === $this->rio_id);
    }

    /**
     * Check if group connection is full (invitee/participants)
     *
     * @return bool
     */
    public function isFull()
    {
        $inviteeCount = $this->users()
            ->where('group_connection_users.rio_id', '<>', $this->rio_id)
            ->count();

        return (config('bphero.group_connection_maximum_invitee') <= $inviteeCount);
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($group) {
            // Softdelete associated group connection users
            $group->users()->each(function ($user) {
                $user->delete();
            });
        });
    }
}
