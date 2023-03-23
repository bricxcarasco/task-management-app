<?php

namespace App\Http\Controllers\Task;

use App\Enums\Task\TaskStatusType;
use App\Enums\Task\Priorities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\SelectableTaskRequest;
use App\Http\Requests\Task\UpsertTaskRequest;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Objects\Tasks;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Session;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));

        // Get selections
        $prioritySelections = Priorities::asSelectArray();
        $serviceSelections = Tasks::getServiceSelections($rio);
        $taskSubjectSelection = Tasks::getServiceSelections($rio, __('All'));

        $timeSelections = Tasks::getTimeSelections();

        return view('tasks.index', compact(
            'rio',
            'service',
            'prioritySelections',
            'serviceSelections',
            'taskSubjectSelection',
            'timeSelections'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Task\UpsertTaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UpsertTaskRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get request data
        $requestData = $request->validated();

        DB::beginTransaction();

        try {
            // Register new task
            $task = new Task();
            $task->fill($requestData);
            $task->created_rio_id = $user->rio_id;
            $task->save();

            // Send notification to Neo task owner
            if (!empty($task->owner_neo_id)) {
                Task::sendNotification($task);
            }

            DB::commit();

            return response()->respondSuccess($task);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Task\UpsertTaskRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpsertTaskRequest $request, int $id)
    {
        // Get request data
        $requestData = $request->validated();

        // Get task object
        $task = Task::whereId($id)->first();

        // Guard clause for non-existing task
        if (empty($task)) {
            return response()->respondNotFound();
        }

        DB::beginTransaction();

        try {
            // Check if Neo owner has been updated
            $isUpdatedOwnerNeo = $task->owner_neo_id !== $requestData['owner_neo_id'];

            // Update task
            $task->update($requestData);

            // Send notification to Neo task owner
            if (!empty($task->owner_neo_id) && $isUpdatedOwnerNeo) {
                Task::sendNotification($task);
            }

            DB::commit();

            return response()->respondSuccess();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Get task lists.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getLists(Request $request)
    {
        $requestData = $request->all();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $task = Task::taskList()
            ->whereMonth('tasks.created_at', '=', (string) $currentMonth)
            ->whereYear('tasks.created_at', '=', $currentYear)
            ->commonConditions($requestData)
            ->paginate(config('bphero.paginate_count'));

        return TaskResource::collection($task);
    }

    /**
     * Fetch tasks list based on year and month.
     *
     * @param string $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTasksByMonth($date)
    {
        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));

        // Get tasks list
        $tasks = Task::getDueTasksPerMonth($service, $date)->get();

        return response()->respondSuccess($tasks);
    }

    /**
     * Fetch tasks list based on selected day.
     *
     * @param string $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTasksByDay($date)
    {
        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));

        // Get tasks list
        $tasks = Task::getDueTasksPerDay($service, $date)->get();

        return response()->respondSuccess($tasks);
    }

    /**
     * Mark selected tasks as complete.
     *
     * @param  \App\Http\Requests\Task\SelectableTaskRequest $request
     * @return \Illuminate\Http\Response
     */
    public function completeTasks(SelectableTaskRequest $request)
    {
        // Get validated data
        $requestData = $request->validated();

        if (!isset($requestData['ids'])) {
            return response()->respondNotFound();
        }

        DB::beginTransaction();

        try {
            $tasks = Task::whereIn('id', $requestData['ids'])
                ->whereFinished(false)
                ->update([
                    'finished' => true,
                    'completed_at' => Carbon::now()
                ]);

            DB::commit();

            return response()->respondSuccess($tasks);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Delete selected tasks.
     *
     * @param  \App\Http\Requests\Task\SelectableTaskRequest $request
     * @return \Illuminate\Http\Response
     */
    public function deleteTasks(SelectableTaskRequest $request)
    {
        // Get validated data
        $requestData = $request->validated();

        if (!isset($requestData['ids'])) {
            return response()->respondNotFound();
        }

        DB::beginTransaction();

        try {
            $tasks = Task::whereIn('id', $requestData['ids'])
                ->whereFinished(false)
                ->delete();

            DB::commit();

            return response()->respondSuccess($tasks);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Return task
     *
     * @param \App\Models\Task $task
     * @return mixed
     */
    public function returnTask(Task $task)
    {
        $task->update([
            'finished' => TaskStatusType::INCOMPLETE,
            'completed_at' => null,
        ]);

        return response()->respondSuccess();
    }
}
