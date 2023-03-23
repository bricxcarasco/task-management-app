<?php

namespace App\Observers;

use App\Enums\Workflow\ReactionTypes;
use App\Enums\Workflow\StatusTypes;
use App\Models\NeoBelong;
use App\Models\WorkFlow;
use App\Models\WorkFlowAction;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class NeoBelongObserver
{
    /**
     * Handle the NeoBelong "deleted" event.
     *
     * @return mixed
     */
    public function deleted(NeoBelong $neoBelong)
    {
        DB::beginTransaction();

        try {
            // Applicant update status
            Workflow::where('created_rio_id', $neoBelong->rio_id)
                ->where('owner_neo_id', $neoBelong->neo_id)
                ->whereIn('status', [StatusTypes::APPLYING, StatusTypes::REMANDED])
                ->update([
                    'status' => StatusTypes::CANCELLED
                ]);

            // Approver update status
            Workflow::whereHas('actions', function (Builder $query) use ($neoBelong) {
                $query->where('rio_id', $neoBelong->rio_id);
            })
                ->where('owner_neo_id', $neoBelong->neo_id)
                ->whereIn('status', [StatusTypes::APPLYING, StatusTypes::REMANDED])
                ->update([
                    'status' => StatusTypes::REJECTED
                ]);

            // Approver update reaction
            WorkflowAction::where('rio_id', $neoBelong->rio_id)
                ->whereHas('workflow', function (Builder $query) use ($neoBelong) {
                    $query->where('owner_neo_id', $neoBelong->neo_id);
                })
                ->where('reaction', ReactionTypes::PENDING)
                ->update([
                    'reaction' => StatusTypes::REJECTED
                ]);

            DB::commit();
        } catch (Exception) {
            DB::rollback();
        }
    }
}
