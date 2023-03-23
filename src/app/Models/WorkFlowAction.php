<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\WorkFlowAction
 *
 * @property int $id id for Laravel
 * @property int $workflow_id
 * @property int $rio_id
 * @property int $reaction 1: 対応待ち、2: 承認、3: 差戻、4: 否認
 * @property string|null $comment 1: 対応待ち、2: 承認、3: 差戻、4: 否認
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Rio $rio
 * @property-read \App\Models\WorkFlow $workflow
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction newQuery()
 * @method static \Illuminate\Database\Query\Builder|WorkFlowAction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction whereReaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction whereWorkflowId($value)
 * @method static \Illuminate\Database\Query\Builder|WorkFlowAction withTrashed()
 * @method static \Illuminate\Database\Query\Builder|WorkFlowAction withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlowAction workflowActionDetail()
 * @mixin \Eloquent
 */
class WorkFlowAction extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'workflow_actions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'workflow_id',
        'rio_id',
        'reaction',
        'comment',
    ];

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
     * Define relationship with WORKFLOW
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workflow()
    {
        return $this->belongsTo(WorkFlow::class, 'workflow_id');
    }

    /**
     * Scope a query of Specific Workflow Details
     *
     * @param mixed $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeWorkflowActionDetail($query)
    {
        return $query
            ->select([
                    'workflow_actions.*'
            ])
            // Get Rio or Neo name
            ->selectRaw('
                TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                AS name
            ')
            ->selectRaw('rios.family_name')
            ->leftJoin('rios', 'rios.id', '=', 'workflow_actions.rio_id');
    }
}
