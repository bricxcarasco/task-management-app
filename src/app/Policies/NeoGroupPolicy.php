<?php

namespace App\Policies;

use App\Models\NeoGroup;
use App\Traits\HandlesAuthorization;

class NeoGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the group connection.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\NeoGroup $group
     * @return  \Illuminate\Auth\Access\Response
     */
    public function update($user, NeoGroup $group)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        if ($user->rio_id === $group->rio_id) {
            return $this->allow();
        }

        return ($group->isMemberOwner($group) || $group->isMemberAdministrator($group))
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can delete the group connection.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\NeoGroup $group
     * @return  \Illuminate\Auth\Access\Response
     */
    public function delete($user, NeoGroup $group)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        if ($user->rio_id === $group->rio_id) {
            return $this->allow();
        }

        return ($group->isMemberOwner($group) || $group->isMemberAdministrator($group))
            ? $this->allow()
            : $this->deny();
    }
}
