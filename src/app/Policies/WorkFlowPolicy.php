<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkFlow;
use App\Enums\ServiceSelectionTypes;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkFlowPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the workflow.
     *
     * @param   \App\Models\User $user
     * @param   mixed $service
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function index($user, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Validate current service
        if ($service->type !== ServiceSelectionTypes::NEO) {
            return $this->deny();
        }

        return $this->allow();
    }

    /**
     * Determine whether the user can access document.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\WorkFlow $workFlow
     * @return  \Illuminate\Auth\Access\Response
     */
    public function show($user, WorkFlow $workFlow)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return $workFlow->isAllowedWorkflowAccess()
            ? $this->allow()
            : $this->deny();
    }
}
