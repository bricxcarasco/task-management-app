<?php

namespace App\Http\Controllers\Workflow;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Workflow\WorkflowResource;
use Illuminate\Http\Request;
use App\Http\Requests\Workflow\WorkflowInitialRequest;
use App\Http\Requests\Workflow\WorkflowApproverRequest;
use App\Http\Requests\Workflow\WorkflowRequest;
use App\Http\Requests\Document\ProcessUploadRequest;
use App\Enums\ServiceSelectionTypes;
use App\Enums\Workflow\ApproverStatusTypes;
use App\Enums\Workflow\PriorityTypes;
use App\Enums\Workflow\ReactionTypes;
use App\Enums\Workflow\StatusTypes;
use App\Objects\FilepondFile;
use App\Objects\ServiceSelected;
use App\Models\Document;
use App\Traits\FilePondUploadable;
use App\Models\Neo;
use App\Models\NeoBelong;
use DB;
use Illuminate\Http\UploadedFile;
use App\Models\WorkFlow;
use App\Models\WorkFlowAction;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkflowController extends Controller
{
    use FilePondUploadable;

    /**
     * Display workflow specific details
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show($id)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        /** @var Workflow */
        $workflow = WorkFlow::detail()
            ->withActionDetails()
            ->findOrFail($id);

        $this->authorize('show', [WorkFlow::class, $workflow]);

        //get Selection
        $reactionsSelection = ReactionTypes::getReactionWithoutPending();

        // Get documents
        $attachments = $workflow->displayAttachments;

        $userType = $workflow->getUserApprover($rio->id);

        return view('workflows.show', compact(
            'workflow',
            'attachments',
            'reactionsSelection',
            'userType',
            'rio',
        ));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        // Get selected service
        $service = ServiceSelected::getSelected();
        // Guard clause for workflow
        $this->authorize('index', [WorkFlow::class, $service]);

        return view('workflows.index', compact('service'));
    }

    /**
     * Validate workflow
     *
     * @param Neo $neo
     *
     * @return mixed
     */
    public function getApproverList(Neo $neo)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        if ($service->type !== ServiceSelectionTypes::NEO) {
            return response()->respondForbidden();
        }

        $approverList = NeoBelong::where('neo_id', $neo->id)
            ->select('rios.id')
            ->selectRaw('(TRIM(CONCAT(rios.family_name, " ", rios.first_name))) AS text')
            ->join('rios', 'neo_belongs.rio_id', 'rios.id')
            ->where('rio_id', '<>', $user->rio_id)
            ->get();

        return response()->respondSuccess($approverList);
    }

    /**
     * Validate workflow
     *
     * @param \App\Http\Requests\Workflow\WorkflowInitialRequest $request
     * @return mixed
     */
    public function validateWorkflow(WorkflowInitialRequest $request)
    {
        $requestData = $request->validated();

        return response()->respondSuccess($requestData);
    }

    /**
     * Validate workflow
     *
     * @param \App\Http\Requests\Workflow\WorkflowApproverRequest $request
     * @return mixed
     */
    public function validateWorkflowApprover(WorkflowApproverRequest $request)
    {
        $requestData = $request->validated();

        return response()->respondSuccess($requestData);
    }

    /**
     * Save workflow
     *
     * @param \App\Http\Requests\Workflow\WorkflowRequest $request
     * @return mixed
     */
    public function saveWorkflow(WorkflowRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        if ($service->type !== ServiceSelectionTypes::NEO) {
            return response()->respondForbidden();
        }

        // Get request data
        $requestData = $request->validated();

        DB::beginTransaction();

        try {
            $workflow = new Workflow();
            $workflow->fill($request->formAttributes());

            // Create attachment records for uploaded files
            $uploadCodes = $requestData['upload_file'] ?? [];
            $documentIds = Document::createWorkflowAttachments($uploadCodes);

            if ($service->type === ServiceSelectionTypes::NEO) {
                $workflow->owner_neo_id = $service->data->id;
            }

            // Assign values
            $workflow->priority = $requestData['priority'] === PriorityTypes::NONE ? null : $requestData['priority'];
            $workflow->status = StatusTypes::APPLYING;
            $workflow->created_rio_id = $user->rio_id;

            // Prepare attachments
            $attachments = array_merge($requestData['attaches'] ?? [], $documentIds);
            $workflow->attaches = json_encode($attachments, JSON_FORCE_OBJECT) ?: '';

            // Save workflow
            $workflow->save();

            $workflowActionArray = [];
            if ($requestData['approvers']) {
                foreach ($requestData['approvers'] as $approver) {
                    if ($approver['id']) {
                        array_push($workflowActionArray, [
                            "workflow_id" => $workflow->id,
                            "rio_id" => $approver['id'],
                            "reaction" => ReactionTypes::PENDING
                        ]);
                    }
                }
                $workflow->actions()->createMany($workflowActionArray);
            }

            DB::commit();

            return response()->respondSuccess([], __('The workflow was issued'));
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Get created workflow lists.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getCreatedWorkflowLists(Request $request)
    {
        $requestData = $request->all();
        // Created workflow list
        $workflow = WorkFlow::registeredList()
            ->filterList($requestData)
            ->commonConditions($requestData)
            ->paginate(config('bphero.paginate_count'));
        // Count returned workflow
        $count = WorkFlow::registeredList()
            ->returned()
            ->count();

        return [
            'data' => WorkflowResource::collection($workflow),
            'count' => $count
        ];
    }

    /**
     * Get workflow for you lists.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getWorkflowForYouLists(Request $request)
    {
        $requestData = $request->all();

        $workflow = WorkFlow::forYouLists()
            ->filterForYouList($requestData)
            ->commonConditions($requestData)
            ->with(['actions'])
            ->paginate(config('bphero.paginate_count'));
        // Count pending workflow actions
        $count = WorkFlow::forYouLists()
            ->pending()
            ->count();

        return [
            'data' => WorkflowResource::collection($workflow),
            'count' => $count
        ];
    }

    /**
     * Process Upload API Endpoint
     *
     * @param \App\Http\Requests\Document\ProcessUploadRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function processUpload(ProcessUploadRequest $request)
    {
        // Get request data
        $requestData = $request->validated();
        $files = $requestData['upload_file'];

        // Check if data sent is a file
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $result = FilepondFile::storeTemporaryFile($file);

                // Return failed save
                if ($result === false) {
                    return response()->respondInternalServerError(['Could not save file.']);
                }

                // Return server ID
                return response($result, 200, [
                    'Content-Type' => 'text/plain',
                ]);
            }

            // Handle Filepond File Metadata (usually on chunk upload)
            if (CommonHelper::isJson($file)) {
                $path = FilepondFile::generateTemporaryDirectory();
                $serverCode = FilepondFile::toServerCode($path);

                // Return server ID
                return response($serverCode, 200, [
                    'Content-Type' => 'text/plain',
                ]);
            }
        }

        // Return failed save
        return response()->respondInternalServerError(['Could not save file.']);
    }

    /**
     * update cancel application of workflow
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function cancelApplication($id)
    {
        try {
            DB::beginTransaction();

            /** @var Workflow */
            $workflow = WorkFlow::findOrFail($id);
            /** @var WorkflowAction */
            $workflowAction = WorkFlowAction::where('workflow_id', $id)->firstOrFail();

            //update workflow status
            $workflow->update(['status' => StatusTypes::CANCELLED]);

            //update workflow action reaction
            $workflowAction->update(['reaction' => ApproverStatusTypes::CANCELLED]);

            DB::commit();

            return response()->respondSuccess();
        } catch (NotFoundHttpException $e) {
            DB::rollBack();

            return response()->respondNotFound();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }
}
