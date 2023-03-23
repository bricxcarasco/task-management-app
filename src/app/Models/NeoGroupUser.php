<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\NeoGroupUser
 *
 * @property int $id id for Laravel
 * @property int $neo_group_id
 * @property int $rio_id
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\NeoGroup $group
 * @property-read \App\Models\Rio $rio
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroupUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroupUser newQuery()
 * @method static \Illuminate\Database\Query\Builder|NeoGroupUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroupUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroupUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroupUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroupUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroupUser whereNeoGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroupUser whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoGroupUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NeoGroupUser withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NeoGroupUser withoutTrashed()
 * @mixin \Eloquent
 */
class NeoGroupUser extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'neo_group_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'neo_group_id',
        'rio_id',
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
     * Define relationship with neo_groups table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(NeoGroup::class, 'neo_group_id');
    }
}
