<?php

namespace App\Models;

use App\Enums\Neo\RoleType;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\NeoGroup
 *
 * @property int $id id for Laravel
 * @property int $neo_id 0:申請中 、1:つながり状態
 * @property int $rio_id
 * @property int $chat_id
 * @property string $group_name
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Chat|null $chat
 * @property-read bool $is_member
 * @property-read \App\Models\Neo $neo
 * @property-read \App\Models\Rio $rio
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoGroupUser[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|NeoGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroup whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroup whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroup whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroup whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NeoGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NeoGroup withoutTrashed()
 * @mixin \Eloquent
 */
class NeoGroup extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'neo_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'neo_id',
        'rio_id',
        'chat_id',
        'group_name',
    ];

    /**
     * Define relationship with rios table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class, 'rio_id');
    }

    /**
     * Define relationship with neos table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class, 'neo_id');
    }

    /**
     * Define relationship with neo_group_users table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(NeoGroupUser::class);
    }

    /**
     * Define relationship with chats table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function chat()
    {
        return $this->hasOne(Chat::class, 'id');
    }

    /**
     * Get neo group is member checker attribute.
     *
     * @return bool
     */
    public function getIsMemberAttribute()
    {
        /** @var User */
        $user = auth()->user();

        // Check if RIO exists in neo_group_users
        return $this->users()
            ->whereRioId($user->rio_id)
            ->exists();
    }

    /**
     * Check if requester is an NEO owner
     *
     * @param \App\Models\NeoGroup $group
     *
     * @return bool
     */
    public function isMemberOwner($group)
    {
        /** @var User */
        $user = auth()->user();

        // Check if RIO exists and also an owner in neo_belongs
        return NeoBelong::whereNeoId($group->neo_id)
            ->whereRioId($user->rio_id)
            ->whereRole(RoleType::OWNER)
            ->exists();
    }

    /**
     * Check if requester is an NEO owner or administrator
     *
     * @param \App\Models\NeoGroup $group
     *
     * @return bool
     */
    public function isMemberAdministrator($group)
    {
        /** @var User */
        $user = auth()->user();

        // Check if RIO exists and also an admin in neo_belongs
        return NeoBelong::whereNeoId($group->neo_id)
            ->whereRioId($user->rio_id)
            ->whereRole(RoleType::ADMINISTRATOR)
            ->exists();
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
            // Softdelete associated NEO group users
            $group->users()->each(function ($user) {
                $user->delete();
            });
        });
    }
}
