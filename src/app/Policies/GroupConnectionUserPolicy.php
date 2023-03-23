<?php

namespace App\Policies;

use App\Models\GroupConnectionUser;
use App\Traits\HandlesAuthorization;

class GroupConnectionUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the group connection user.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\GroupConnectionUser $group
     * @return  \Illuminate\Auth\Access\Response
     */
    public function update($user, GroupConnectionUser $group)
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
     * Determine whether the user can delete the group connection user.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\GroupConnectionUser $group
     * @return  \Illuminate\Auth\Access\Response
     */
    public function delete($user, GroupConnectionUser $group)
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
     * Determine whether the user can delete the invite to the group connection.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\GroupConnectionUser $invite
     * @return  \Illuminate\Auth\Access\Response
     */
    public function deleteInvite($user, GroupConnectionUser $invite)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Guard clause for non-existing group
        if (empty($invite->group)) {
            return $this->deny();
        }

        return ($user->rio_id === $invite->group->rio_id)
            ? $this->allow()
            : $this->deny();
    }
}
