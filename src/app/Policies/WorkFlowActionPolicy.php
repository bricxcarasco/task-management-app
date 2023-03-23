<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkFlowAction;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\Workflow\ReactionTypes;

class WorkFlowActionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can access document.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\WorkFlowAction $workflowAction
     * @return  \Illuminate\Auth\Access\Response
     */
    public function update($user, WorkFlowAction $workflowAction)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return $workflowAction->rio_id === $user->rio->id && $workflowAction->reaction === ReactionTypes::PENDING
            ? $this->allow()
            : $this->deny();
    }
}
