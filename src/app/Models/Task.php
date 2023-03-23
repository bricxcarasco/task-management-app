<?php

namespace App\Models;

use App\Enums\NeoBelongStatuses;
use App\Enums\ServiceSelectionTypes;
use App\Enums\Task\TaskSortType;
use App\Enums\Task\TaskStatusType;
use App\Traits\ModelUpdatedTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Task
 *
 * @property int $id id for Laravel
 * @property int|null $owner_rio_id ↓どちらかのみセット
 * @property int|null $owner_neo_id ↑どちらかのみセット
 * @property int $created_rio_id
 * @property string $task_title
 * @property string|null $limit_date
 * @property string|null $limit_time
 * @property int $finished 0: 未完了、1: 完了
 * @property string|null $priority (null): 設定なし、high: 高、mid: 中、low: 低
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Rio $created_rio
 * @property-read \App\Models\Neo|null $owner_neo
 * @property-read \App\Models\Rio|null $owner_rio
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Query\Builder|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereFinished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereLimitDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereLimitTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereOwnerNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereOwnerRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTaskTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Task withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Task withoutTrashed()
 * @mixin \Eloquent
 */
class Task extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_rio_id',
        'owner_neo_id',
        'created_rio_id',
        'task_title',
        'limit_date',
        'limit_time',
        'finished',
        'priority',
        'completed_at',
        'remarks'
    ];

    /**
     * Define relationship for RIO owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner_rio()
    {
        return $this->belongsTo(Rio::class, 'owner_rio_id');
    }

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
     * Scope a query of tasks list
     *
     * @param mixed $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeTaskList($query)
    {
        return $query
            ->select([
                'tasks.*',
                DB::raw("CONCAT(limit_date,' ',limit_time) as due_date")
            ])
            // Get Rio or Neo name
            ->selectRaw('
                (CASE
                    WHEN tasks.owner_rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    WHEN tasks.owner_neo_id IS NOT NULL
                        THEN neos.organization_name
                    ELSE NULL
                END) AS name
            ')
            ->selectRaw('
                (CASE
                    WHEN tasks.owner_rio_id IS NOT NULL
                        THEN tasks.owner_rio_id
                    WHEN tasks.owner_neo_id IS NOT NULL
                        THEN tasks.owner_neo_id
                    ELSE NULL
                END) AS owner_id
            ')
            ->leftJoin('rios', 'rios.id', '=', 'tasks.owner_rio_id')
            ->leftJoin('rio_profiles', 'rio_profiles.rio_id', '=', 'tasks.owner_rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'tasks.owner_neo_id')
            ->leftJoin('neo_profiles', 'neo_profiles.neo_id', '=', 'tasks.owner_neo_id');
    }

    /**
     * Scope a query of finished tasks
     *
     * @param \Illuminate\Database\Eloquent\Builder<Task> $query
     * @return mixed
     */
    public static function scopeFinished($query)
    {
        return $query->where('finished', '=', TaskStatusType::COMPLETION);
    }

    /**
     * Scope a query based on filter conditions
     *
     * @param \Illuminate\Database\Eloquent\Builder<Task> $query
     * @param mixed $options
     * @return mixed
     */
    public static function scopeCommonConditions($query, $options = null)
    {
        // Initialize variables
        /** @var \App\Models\User */
        $user = auth()->user();
        $rio = $user->rio;
        $id = null;
        $targetEntity = null;
        $isFinishedOnly = $options['finished'] ?? false;

        // Prepare specified target entity values
        if (isset($options['filter'])) {
            $explode = explode('_', $options['filter']);
            $targetEntity = strtoupper($explode[0]);
            $id = $explode[1];
        }

        // Filter query by target entity
        switch ($targetEntity) {
            case ServiceSelectionTypes::RIO:
                $query->where('owner_rio_id', $rio->id);
                break;
            case ServiceSelectionTypes::NEO:
                $query->where('owner_neo_id', $id);
                break;
            default:
                // Get NEOs of current rio
                $neoSelections = $rio->neos()
                    ->whereStatus(NeoBelongStatuses::AFFILIATED)
                    ->pluck('neos.id');

                $query->where(function ($query) use ($rio, $neoSelections) {
                    $query->where('tasks.owner_rio_id', $rio->id)
                        ->orWhereIn('tasks.owner_neo_id', $neoSelections);
                });
                break;
        }

        // Fetch finished tasks
        if ($isFinishedOnly) {
            $query->finished();

            $options['sortBy'] = TaskSortType::COMPLETED_AT;
        }

        // Search by keyword
        if (isset($options['keyword']) && !is_null($options['keyword'])) {
            $searchKeyword = '%' . mb_strtolower($options['keyword']) . '%';
            $query->where(DB::raw("LOWER(tasks.task_title)"), 'LIKE', $searchKeyword);
        }

        // Sort tasks records
        if (isset($options['sortBy'])) {
            switch ($options['sortBy']) {
                case TaskSortType::REGISTRATION_NEWEST:
                    $query->orderBy('created_at', 'DESC');
                    break;
                case TaskSortType::NEWEST_DUE_DATE:
                    $query->orderBy('due_date', 'DESC');
                    break;
                case TaskSortType::OLDEST_DUE_DATE:
                    $query->orderByRaw('ISNULL(due_date), due_date ASC');
                    break;
                case $options['sortBy'] === TaskSortType::COMPLETED_AT:
                    $query->orderBy('completed_at', 'DESC');
                    break;
                default:
                    break;
            }
        }

        return $query;
    }

    /**
     * Scope a query to check if selected service is the owner.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Task> $query
     * @param mixed $service Selected service
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeListByOwner($query, $service)
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        $rio = $user->rio;

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                // Get NEOs of current rio
                $neoSelections = $rio->neos()
                    ->whereStatus(NeoBelongStatuses::AFFILIATED)
                    ->pluck('neos.id');

                return $query->where(function ($query) use ($rio, $neoSelections) {
                    $query->where('owner_rio_id', $rio->id)
                        ->orWhereIn('owner_neo_id', $neoSelections);
                });
            case ServiceSelectionTypes::NEO:
                return $query->where('owner_neo_id', $service->data->id);
            default:
                return $query;
        }
    }

    /**
     * Scope a query to get list of due tasks based on year and month.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Task> $query
     * @param mixed $service Selected service
     * @param string $date Selected date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeGetDueTasksPerMonth($query, $service, $date = null)
    {
        // Set default month as current month
        $month = Carbon::now()->month;

        if (!empty($date)) {
            // Get month from selected month
            $date = Carbon::parse($date);
            $month = $date->month;
        }

        return $query
            ->taskList()
            ->listByOwner($service)
            ->whereNotNull('limit_date')
            ->whereNotNull('limit_time')
            ->whereMonth('tasks.limit_date', (string) $month)
            ->orderBy('limit_date', 'ASC');
    }

    /**
     * Scope a query to get list of due tasks based on specific duedate.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Task> $query
     * @param mixed $service Selected service
     * @param string $date Selected date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeGetDueTasksPerDay($query, $service, $date)
    {
        // Parse date
        $date = Carbon::parse($date);

        return $query
            ->taskList()
            ->listByOwner($service)
            ->whereDate('limit_date', $date)
            ->orderBy('limit_date', 'ASC')
            ->orderBy('limit_time', 'ASC');
    }

    /**
     * Send notification to NEO task owner.
     *
     * @param object $task
     * @return void
     */
    public static function sendNotification($task)
    {
        /** @var Neo */
        $neoReceiver = Neo::whereId($task->owner_neo_id)->first();

        /** @var int @phpstan-ignore-next-line */
        $rioReceiverId = $neoReceiver->owner ? $neoReceiver->owner->rio_id : null;

        if (!empty($rioReceiverId)) {
            Notification::createNotification([
                'rio_id' => $rioReceiverId,
                'receive_neo_id' => $neoReceiver->id,
                'notification_content' => __('Notification Content - Added Task', [
                    'neo_name' => $neoReceiver->organization_name
                ]),
                'destination_url' => route('tasks.index')
            ]);
        }
    }
}
