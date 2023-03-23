<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\KnowledgeComment
 *
 * @property int $id id for Laravel
 * @property int $knowledge_id
 * @property int $rio_id
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Knowledge $knowledge
 * @property-read \App\Models\Rio $rio
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeComment newQuery()
 * @method static \Illuminate\Database\Query\Builder|KnowledgeComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeComment whereKnowledgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeComment whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|KnowledgeComment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|KnowledgeComment withoutTrashed()
 * @mixin \Eloquent
 */
class KnowledgeComment extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'knowledge_comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'knowledge_id',
        'rio_id',
        'comment',
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
     * Scope query for fetching comment of a article
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommentList($query)
    {
        $defaultProfileImage = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');

        $query
            ->select([
                'knowledge_comments.*',
            ])
            ->selectRaw('
                (CASE
                    WHEN knowledge_comments.rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    ELSE NULL
                END) AS name
            ')
            ->selectRaw("
                (CASE
                    WHEN knowledge_comments.rio_id IS NOT NULL
                        THEN
                            CASE
                                WHEN rio_profiles.profile_photo IS NOT NULL
                                    THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                                ELSE '" . $defaultProfileImage . "'
                            END
                    ELSE '" . $defaultProfileImage . "'
                END) AS profile_photo
            ")
            ->leftJoin('rios', 'rios.id', '=', 'knowledge_comments.rio_id')
            ->leftJoin('rio_profiles', 'rio_profiles.rio_id', '=', 'knowledge_comments.rio_id')
            ->orderBy('created_at', 'DESC');

        return $query;
    }
}
