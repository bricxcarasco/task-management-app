<?php

namespace App\Policies;

use App\Models\Neo;
use App\Models\NeoExpert;
use App\Traits\HandlesAuthorization;

class NeoExpertPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can store expert to neo.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Neo $neo
     * @return  \Illuminate\Auth\Access\Response
     */
    public function store($user, Neo $neo)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return $neo->isUserAccessible()
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can update the neo expert.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\NeoExpert $expert
     * @return  \Illuminate\Auth\Access\Response
     */
    public function update($user, NeoExpert $expert)
    {
        // Guard clause for non-existing rio
        if (empty($expert->neo)) {
            return $this->deny();
        }

        return $expert->neo->isUserAccessible()
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can delete the neo expert.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\NeoExpert $expert
     * @return  \Illuminate\Auth\Access\Response
     */
    public function delete($user, NeoExpert $expert)
    {
        // Guard clause for non-existing rio
        if (empty($expert->neo)) {
            return $this->deny();
        }

        return $expert->neo->isUserAccessible()
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can fetch from neo expert.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\NeoExpert $neoExpert
     * @return  \Illuminate\Auth\Access\Response
     */

    public function get($user, NeoExpert $neoExpert)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        $neo = $neoExpert->neo ?? null;

        // Guard clause for non-existing neo
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        return $neo->isUserAccessible()
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can register from neo expert.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\NeoExpert $neoExpert
     * @return  \Illuminate\Auth\Access\Response
     */

    public function create($user, NeoExpert $neoExpert)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        $neo = $neoExpert->neo ?? null;

        // Guard clause for non-existing neo
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        return $neo->isUserAccessible()
            ? $this->allow()
            : $this->deny();
    }
}
