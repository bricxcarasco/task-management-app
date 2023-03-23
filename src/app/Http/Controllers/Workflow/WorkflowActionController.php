<?php

namespace App\Http\Controllers\Workflow;

use App\Models\WorkFlow;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkflowAction\UpdateReactionRequest;
use App\Models\WorkFlowAction;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkflowActionController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\WorkflowAction\UpdateReactionRequest  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateReaction(UpdateReactionRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Get workflow object
            $workflow = WorkFlow::findorfail($id);

            $action = $workflow->actions()
                ->whereRioId($request->rio_id)
                ->first();

            $this->authorize('update', [WorkFlowAction::class, $action]);

            // Guard clause for non-existing action
            if (empty($action)) {
                return response()->respondNotFound();
            }

            // // Update task reaction and comment
            $action->update($request->only(['reaction', 'comment']));

            //update workflow status
            $workflow->update(['status' => $workflow->getStatus($request->reaction)]);

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
