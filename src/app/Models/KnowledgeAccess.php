<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\KnowledgeAccess
 *
 * @property int $id id for Laravel
 * @property int $knowledge_id ↓どちらかのみセット
 * @property int|null $rio_id ↓どれかのみセット
 * @property int|null $neo_id どれかのみセット
 * @property int|null $neo_group_id ↑どれかのみセット
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Knowledge $knowledge
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Neo|null $neo_group
 * @property-read \App\Models\Rio|null $rio
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeAccess newQuery()
 * @method static \Illuminate\Database\Query\Builder|KnowledgeAccess onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeAccess whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeAccess whereKnowledgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeAccess whereNeoGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeAccess whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeAccess whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeAccess whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|KnowledgeAccess withTrashed()
 * @method static \Illuminate\Database\Query\Builder|KnowledgeAccess withoutTrashed()
 * @mixin \Eloquent
 */
class KnowledgeAccess extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'knowledge_accesses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'knowledge_id',
        'rio_id',
        'neo_id',
        'neo_group_id'
    ];

    /**
     * Define relationship with Knowledge
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function knowledge()
    {
        return $this->belongsTo(Knowledge::class, 'knowledge_id');
    }

    /**
     * Define relationship with RIO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class, 'rio_id');
    }

    /**
     * Define relationship with NEO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class, 'neo_id');
    }

    /**
     * Define relationship with NEO group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo_group()
    {
        return $this->belongsTo(Neo::class, 'neo_group_id');
    }
}
