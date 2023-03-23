<?php

namespace App\Models;

use App\Helpers\CommonHelper;
use App\Enums\Workflow\SortType;
use App\Objects\ServiceSelected;
use App\Traits\ModelUpdatedTrait;
use App\Enums\Workflow\StatusTypes;
use App\Enums\ServiceSelectionTypes;
use App\Enums\Workflow\ReactionTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Enums\Workflow\UserApproverType;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Workflow\ApproverStatusTypes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\WorkFlow
 *
 * @property int $id id for Laravel
 * @property int $created_rio_id ↓どちらかのみセット
 * @property int|null $owner_neo_id ↑どちらかのみセット
 * @property string $workflow_title
 * @property string $caption
 * @property string $attaches 最大5ファイルまでjson形式で指定可能…セットするIDは 文章管理.ID
 * @property int $status 1: 申請中、2: 承認完了、3: 差戻中、4: 否認済、5: 申請取消
 * @property string $priority (null): 設定なし、high: 高、mid: 中、low: 低
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WorkFlowAction[] $actions
 * @property-read int|null $actions_count
 * @property-read \App\Models\Rio $created_rio
 * @property-read mixed $attachments
 * @property-read \App\Models\Neo|null $owner_neo
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow newQuery()
 * @method static \Illuminate\Database\Query\Builder|WorkFlow onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow whereAttaches($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow whereOwnerNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow whereWorkflowTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow withActionDetails()
 * @method static \Illuminate\Database\Query\Builder|WorkFlow withTrashed()
 * @method static \Illuminate\Database\Query\Builder|WorkFlow withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkFlow workflowDetail()
 * @mixin \Eloquent
 * @property-read mixed $display_links
 * @method static Builder|WorkFlow commonConditions($options = null)
 * @method static Builder|WorkFlow detail()
 * @method static Builder|WorkFlow filterForYouList($filter = null)
 * @method static Builder|WorkFlow filterList($filter = null)
 * @method static Builder|WorkFlow forYouLists()
 * @method static Builder|WorkFlow pending()
 * @method static Builder|WorkFlow registeredList()
 * @method static Builder|WorkFlow returned()
 */
class WorkFlow extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'workflows';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_neo_id',
        'created_rio_id',
        'workflow_title',
        'caption',
        'attaches',
        'status',
        'priority',
    ];

    /**
     * Define relationship for NEO owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner_neo()
    {
        return $this->belongsTo(Neo::class, 'owner_neo_id');
    }

    /**
     * Define relationship for RIO creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_rio()
    {
        return $this->belongsTo(Rio::class, 'created_rio_id');
    }

    /**
     * Define relationship with workflowActions model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function actions()
    {
        return $this->hasMany(WorkFlowAction::class, 'workflow_id', 'id');
    }

    /**
     * Scope a query of Specific Workflow Details
     *
     * @param mixed $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeDetail($query)
    {
        return $query
            ->select([
                'workflows.*'
            ])
            // Get Rio or Neo name
            ->selectRaw('
                (CASE
                    WHEN workflows.created_rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    WHEN workflows.owner_neo_id IS NOT NULL
                        THEN neos.organization_name
                    ELSE NULL
                END) AS name
            ')
            ->selectRaw('
                (CASE
                    WHEN workflows.created_rio_id IS NOT NULL
                        THEN workflows.created_rio_id
                    WHEN workflows.owner_neo_id IS NOT NULL
                        THEN workflows.owner_neo_id
                    ELSE NULL
                END) AS owner_id
            ')
            ->leftJoin('rios', 'rios.id', '=', 'workflows.created_rio_id')
            ->leftJoin('rio_profiles', 'rio_profiles.rio_id', '=', 'workflows.created_rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'workflows.owner_neo_id')
            ->leftJoin('neo_profiles', 'neo_profiles.neo_id', '=', 'workflows.owner_neo_id');
    }

    /**
     * Scope a query of Specific with workflowAction Details
     *
     * @param mixed $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeWithActionDetails($builder)
    {
        return $builder->with(['actions' => function ($query) {
            $query->workflowActionDetail();
        }]);
    }

    /**
     * Check if current entity allowed to access workflow
     *
     * @return bool
     */
    public function isAllowedWorkflowAccess()
    {
        // Get rio id of currently logged-in user
        /** @var User */
        $user = auth()->user();

        // Service selection session state values
        $service = ServiceSelected::getSelected();

        // Guard clause for non-existing rio
        if (empty($user->rio) || empty($service)) {
            return false;
        }

        // Check if NEO owner or admininstrator is the current user logged-in
        if ($service->type !== ServiceSelectionTypes::NEO) {
            return false;
        }


        if ($this->owner_neo_id !== $service->data->id) {
            return false;
        }

        // check if auth user the one created the workflow and check service is rio
        if ($this->created_rio_id === $user->rio_id) {
            return true;
        }

        // check if auth user is the current approver
        if ($this->isApprover($user->rio_id)) {
            return true;
        }

        return false;
    }

    /**
     * Get attachments attribute
     *
     * @return mixed
     */
    public function getAttachmentsAttribute()
    {
        return json_decode($this->attaches, true);
    }

    /**
     * Get workflow detail attachments
     *
     * @return mixed
     */
    public function getDisplayAttachmentsAttribute()
    {
        $displayAttachments = [];

        $attachmentsJson = $this->attachments;

        foreach ($attachmentsJson as $value) {
            $fileName = Document::withTrashed()->where('id', $value)->first();

            if (empty($fileName)) {
                continue;
            }

            $link = route('document.shared-file-preview-route', $value);
            array_push($displayAttachments, [
                "fileId" => $value,
                "fileName" => $fileName->document_name,
                "link" => $link,
                "isExist" => $this->isDocumentExists($value)
            ]);
        }

        return $displayAttachments;
    }

    /**
     * Check if the document is exists in document management
     * @param int $attachmentId
     * @return bool
     */
    private function isDocumentExists($attachmentId)
    {
        $document =  Document::whereId($attachmentId)->first();

        // Guard clause if non-existing document
        if (empty($document)) {
            return false;
        }

        $storagePath = CommonHelper::removeMainDirectoryPath($document->storage_path);

        //get file url in S3 Bucket
        return Storage::disk(config('bphero.private_bucket'))
            ->exists($storagePath);
    }

    /**
     * Get display link attribute
     *  @param int $id
     * @return bool
     */
    public function hasReacted($id)
    {
        return $this->actions()->whereRioId($id)
            ->whereNotIn('reaction', [ReactionTypes::PENDING])
            ->exists();
    }

    /**
     * Check if RIO user is approver
     *
     * @param int $id
     * @return bool
     */
    public function isApprover($id)
    {
        return $this->actions()
            ->whereRioId($id)
            ->exists();
    }

    /**
     * Get display link attribute
     *
     * @return mixed
     */
    public function currentApprover()
    {
        return $this->actions()
            ->whereReaction(ReactionTypes::PENDING)
            ->first();
    }


    /**
     *
     * Get user approver type
     *  @param int $rioId
     * @return mixed
     */
    public function getUserApprover($rioId)
    {
        if ($this->created_rio_id === $rioId) {
            return UserApproverType::OWNER;
        }

        if ($this->currentApprover()?->rio_id ===  $rioId) {
            return UserApproverType::CURRENT_APPROVER;
        }

        return UserApproverType::APPROVER;
    }

    /**
     * get status of the workflow to change it's proper status
     *
     * @param mixed $reaction
     *
     * @return mixed
     */
    public function getStatus($reaction)
    {
        //original value of status
        $status = '';

        switch ($reaction) {
            case ReactionTypes::APPROVED:
                //get the next approver form the workflow
                $nextApprover = $this->currentApprover();

                //check if it has next approver then assign status to its value
                $status = $nextApprover ? StatusTypes::APPLYING : StatusTypes::APPROVED;
                break;
            case ReactionTypes::RETURNED:
                $status = StatusTypes::REMANDED;
                break;
            case ReactionTypes::REJECTED:
                $status = StatusTypes::REJECTED;
                break;
        }

        return $status;
    }

    /**
     * Scope a query of created workflow list
     *
     * @param mixed $query
     * @return mixed
     */
    public static function scopeRegisteredList($query)
    {
        // Initialize variables
        /** @var \App\Models\User */
        $user = auth()->user();
        // Get selected service
        $service = ServiceSelected::getSelected();
        // Get list
        $query->where('workflows.owner_neo_id', $service->data->id)
            ->where('workflows.created_rio_id', $user->rio_id);

        return $query;
    }

    /**
     * Scope a query of returned workflow
     *
     * @param mixed $query
     * @return mixed
     */
    public static function scopeReturned($query)
    {
        // Get returned status
        return $query->where('status', StatusTypes::REMANDED);
    }

    /**
     * Scope a query of workflow for you tab list
     *
     * @param mixed $query
     * @return mixed
     */
    public static function scopeForYouLists($query)
    {
        // Initialize variables
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get selected service
        $service = ServiceSelected::getSelected();

        // Get list
        $query->where('workflows.owner_neo_id', $service->data->id)
            ->where('workflow_actions.rio_id', $user->rio_id);

        return $query
            ->select([
                'workflows.*',
            ])
            // Get applicant name
            ->selectRaw('
                (CASE
                    WHEN workflows.created_rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    ELSE NULL
                END) AS name
            ')
            // Get workflow status
            ->selectRaw('
                (CASE
                    WHEN workflow_actions.reaction = ' . ReactionTypes::PENDING . '
                        THEN ' . ApproverStatusTypes::PENDING . '
                    WHEN workflow_actions.reaction = ' . ReactionTypes::APPROVED . ' OR workflow_actions.reaction = ' . ReactionTypes::RETURNED . '
                        THEN ' . ApproverStatusTypes::DONE . '
                    WHEN workflows.status = ' . StatusTypes::APPROVED . '
                        THEN ' . ApproverStatusTypes::COMPLETED . '
                    WHEN workflows.status = ' . StatusTypes::REJECTED . '
                        THEN ' . ApproverStatusTypes::REJECTED . '
                    WHEN workflows.status = ' . StatusTypes::CANCELLED . '
                        THEN ' . ApproverStatusTypes::CANCELLED . '
                    ELSE NULL
                END) AS approver_status
            ')

            ->join('workflow_actions', 'workflow_actions.workflow_id', '=', 'workflows.id')
            ->leftJoin('rios', 'rios.id', '=', 'workflows.created_rio_id');
    }

    /**
     * Scope a query of pending workflow actions.
     *
     * @param mixed $query
     * @return mixed
     */
    public static function scopePending($query)
    {
        return $query->whereHas('actions', function (Builder $query) {
            $query->whereReaction(ReactionTypes::PENDING);
        });
    }

    /**
     * Scope a query of filtering created workflow list
     *
     * @param mixed $query
     * @param mixed $filter
     * @return mixed
     */
    public static function scopeFilterList($query, $filter = null)
    {
        if ($filter['status_type']) {
            $query->where('status', '=', $filter['status_type']);
        }

        return $query;
    }

    /**
     * Scope a query of filtering workflow for you status list
     *
     * @param mixed $query
     * @param mixed $filter
     * @return mixed
     */
    public static function scopeFilterForYouList($query, $filter = null)
    {
        if ($filter['approver_status_type']) {
            $query->where('status', '=', $filter['approver_status_type']);
        }

        return $query;
    }

    /**
     * Scope a query based on sort condition
     *
     * @param mixed $query
     * @param mixed $options
     * @return mixed
     */
    public static function scopeCommonConditions($query, $options = null)
    {
        if (isset($options['sort_by'])) {
            switch ($options['sort_by']) {
                case SortType::NEWEST_APPLICATION_DATE:
                    $query->orderBy('created_at', 'DESC');
                    break;
                case SortType::OLDEST_APPLICATION_DATE:
                    $query->orderBy('created_at', 'ASC');
                    break;
                default:
                    $query->orderBy('created_at', 'DESC');
                    break;
            }
        }

        return $query;
    }
}
