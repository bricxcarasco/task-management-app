<?php

namespace App\Policies;

use App\Models\GroupConnection;
use App\Traits\HandlesAuthorization;

class GroupConnectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the group connection.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\GroupConnection $group
     * @return  \Illuminate\Auth\Access\Response
     */
    public function view($user, GroupConnection $group)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return ($user->rio_id === $group->rio_id)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can update the group connection.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\GroupConnection $group
     * @return  \Illuminate\Auth\Access\Response
     */
    public function update($user, GroupConnection $group)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return ($user->rio_id === $group->rio_id)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can delete the group connection.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\GroupConnection $group
     * @return  \Illuminate\Auth\Access\Response
     */
    public function delete($user, GroupConnection $group)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return ($user->rio_id === $group->rio_id)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can invite to the group connection.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\GroupConnection $group
     * @return  \Illuminate\Auth\Access\Response
     */
    public function invite($user, GroupConnection $group)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return ($user->rio_id === $group->rio_id)
            ? $this->allow()
            : $this->deny();
    }
}
