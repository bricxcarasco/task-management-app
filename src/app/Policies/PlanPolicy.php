<?php

namespace App\Policies;

use App\Enums\ServiceSelectionTypes;
use App\Models\Plan;
use App\Models\User;
use App\Traits\HandlesAuthorization;

class PlanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param   \App\Models\User $user
     * @param  \App\Models\Plan  $plan
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function view($user, Plan $plan, $service)
    {
        if (
            (
                $service->type === ServiceSelectionTypes::RIO
                && $plan->id < 5
            )
            ||
            (
                $service->type === ServiceSelectionTypes::NEO
                && $plan->id > 4
            )
        ) {
            return $this->allow();
        }

        return $this->deny();
    }
}
