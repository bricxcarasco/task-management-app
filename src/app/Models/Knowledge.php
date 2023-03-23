<?php

namespace App\Models;

use App\Enums\Knowledge\ArticleTypes;
use App\Enums\Knowledge\Types;
use App\Enums\ServiceSelectionTypes;
use App\Objects\ServiceSelected;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Knowledge
 *
 * @property int $id id for Laravel
 * @property int|null $owner_rio_id ↓どちらかのみセット
 * @property int|null $owner_neo_id ↑どちらかのみセット
 * @property int $created_rio_id
 * @property int|null $directory_id
 * @property int $type 1:Folder（フォルダ）,2:Article（記事)
 * @property string $task_title
 * @property string|null $contents WYSIWYGエディタによるHTML形式で保存
 * @property string|null $urls json形式で5つまで登録可能
 * @property int|null $is_draft 0: 公開、1: 下書き
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\KnowledgeAccess[] $accesses
 * @property-read int|null $accesses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\KnowledgeComment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Rio $created_rio
 * @property-read \App\Models\Neo|null $owner_neo
 * @property-read \App\Models\Rio|null $owner_rio
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge newQuery()
 * @method static \Illuminate\Database\Query\Builder|Knowledge onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge query()
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereContents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereDirectoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereIsDraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereOwnerNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereOwnerRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereTaskTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Knowledge whereUrls($value)
 * @method static \Illuminate\Database\Query\Builder|Knowledge withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Knowledge withoutTrashed()
 * @mixin \Eloquent
 */
class Knowledge extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'knowledges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_rio_id',
        'owner_neo_id',
        'created_rio_id',
        'directory_id',
        'type',
        'task_title',
        'contents',
        'urls',
        'is_draft',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'list_url',
    ];

    /**
     * Define relationship with Knowledge comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(KnowledgeComment::class);
    }

    /**
     * Define relationship with Knowledge access
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accesses()
    {
        return $this->hasMany(KnowledgeAccess::class);
    }

    /**
     * Define relationship with RIO owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner_rio()
    {
        return $this->belongsTo(Rio::class, 'owner_rio_id');
    }
    /**
     * Define relationship with NEO owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner_neo()
    {
        return $this->belongsTo(Neo::class, 'owner_neo_id');
    }

    /**
     * Define relationship with RIO creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_rio()
    {
        return $this->belongsTo(Rio::class, 'created_rio_id');
    }

    /**
     * Define relationship with parent folder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentFolder()
    {
        return $this->belongsTo(self::class, 'directory_id');
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($knowledge) {
            // Soft delete associated folder and articles
            self::whereDirectoryId($knowledge->id)->get()
                ->each(function ($data) {
                    $data->delete();
                });
        });
    }

    /**
     * Check if able to access a knowledge folder.
     *
     * @param int $id Knowledge ID
     * @return bool
     */
    public function isFolderAccessible(int $id)
    {
        return $this
            ->folders()
            ->whereId($id)
            ->exists();
    }

    /**
     * Check if active service is the folder/article owner
     *
     * @return bool
     */
    public function isOwned()
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $service->data->id === $this->owner_rio_id;
            case ServiceSelectionTypes::NEO:
                return $service->data->id === $this->owner_neo_id;
            default:
                return false;
        }
    }

    /**
     * Scope a query to get knowledge folders only.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Knowledge> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFolders($query)
    {
        return $query->whereType(Types::FOLDER);
    }

    /**
     * Scope a query to get knowledge articles only.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeArticles($query)
    {
        return $query->whereType(Types::ARTICLE);
    }

    /**
     * Scope a query to get public knowledge only.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->whereIsDraft(ArticleTypes::PUBLIC);
    }

    /**
     * Scope a query to get owned knowledge only.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwned($query)
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        switch ($service->type) {
            case ServiceSelectionTypes::NEO:
                return $query->whereOwnerNeoId($service->data->id);
            default:
                return $query->whereOwnerRioId($service->data->id);
        }
    }


    /**
     * Scope a query to get all folders.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Knowledge> $query
     * @param int|null $id Directory ID
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetPublicFolders($query, $id = null)
    {
        // Fetch all folders
        $query
            ->folders()
            ->owned()
            ->orderBy('updated_at', 'DESC');

        if (empty($id)) {
            // Fetch folders within root directory
            $query->where('directory_id', null);
        } else {
            // Fetch only folders within the directory
            $query->where('directory_id', $id);
        }

        return $query;
    }

    /**
     * Scope a query to fetch articles list based on conditions
     *
     * @param \Illuminate\Database\Eloquent\Builder<Knowledge> $query
     * @param mixed $options
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetArticlesOnSearch($query, $options = null)
    {
        // Fetch all public articles
        $query
            ->public()
            ->articles()
            ->owned()
            ->orderBy('updated_at', 'DESC');

        // Search by keyword
        if (isset($options['keyword']) && !is_null($options['keyword'])) {
            $keyword = $options['keyword'];
            $query->where(function ($queries) use ($keyword) {
                $queries->where('task_title', 'LIKE', "%{$keyword}%")
                    ->orWhere('contents', 'LIKE', "%{$keyword}%");
            });
        }

        return $query;
    }

    /**
     * Scope a query to get all public articles.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Knowledge> $query
     * @param int|null $id Directory ID
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetPublicArticles($query, $id = null)
    {
        // Fetch all public articles
        $query
            ->public()
            ->articles()
            ->owned()
            ->orderBy('updated_at', 'DESC');

        if (empty($id)) {
            // Fetch articles within root directory
            $query->where('directory_id', null);
        } else {
            // Fetch only articles within the directory
            $query->where('directory_id', $id);
        }

        return $query;
    }

    /**
     * Scope a query to get article draft list
     *
     * @param \Illuminate\Database\Eloquent\Builder<Knowledge> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeDraftList($query)
    {
        return $query
            ->select([
                'knowledges.*',
            ])
            ->selectRaw('
                (CASE
                    WHEN owner_rio_id IS NOT NULL
                        THEN owner_rio_id
                    WHEN owner_neo_id IS NOT NULL
                        THEN owner_neo_id
                    ELSE NULL
                END) AS owner_id
            ')
            ->where('type', Types::ARTICLE)
            ->where('is_draft', ArticleTypes::DRAFT)
            ->owned()
            ->orderBy('updated_at', 'DESC');
    }

    /**
     * Check if service is a NEO owner/admin
     *
     * @return bool
     */
    public function isAuthorizedUser()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        switch ($service->type) {
            case ServiceSelectionTypes::NEO:
                $neo = $service->data;
                return $neo->is_authorized_user;
            default:
                return true;
        }
    }

    /**
     * Check if service is authorized to create articles/folders within a directory
     *
     * @param mixed $service
     * @return bool
     */
    public function isAuthorizedToCreate($service)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Knowledge record for current directory
        $knowledgeDirectory = Knowledge::whereId($this->directory_id)->firstOrFail();

        return $knowledgeDirectory->isOwned();
    }

    /**
    * Get the url attribute
    *
    * @return string
    */
    public function getListUrlAttribute()
    {
        $knowledge_url = "";

        if ($this->urls) {
            return $knowledge_url = json_decode($this->urls);
        }

        return $knowledge_url;
    }

    /**
     * Check if article detail page is viewable
     *
     * @param \App\Models\Knowledge $knowledge
     * @return bool
     */
    public function isViewable($knowledge)
    {
        $service = ServiceSelected::getSelected();

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                $isAccess = $knowledge->accesses()->where('rio_id', $service->data->id)->exists();
                $isBelongToNeo = $knowledge->owner_neo?->isUserAccessible();
                break;
            case ServiceSelectionTypes::NEO:
                $isAccess = $knowledge->accesses()->where('neo_id', $service->data->id)->exists();
                $isBelongToNeo = false;
                break;
            default:
                return false;
        }

        // Check if not folder or article draft
        if ($knowledge->type === Types::FOLDER || $knowledge->is_draft === ArticleTypes::DRAFT) {
            return false;
        }

        return $knowledge->isOwned() || $isAccess || $isBelongToNeo;
    }
}
