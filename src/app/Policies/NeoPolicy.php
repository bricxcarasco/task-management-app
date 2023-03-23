<?php

namespace App\Policies;

use App\Enums\Neo\RoleType;
use App\Enums\Neo\AcceptConnectionType;
use App\Enums\NeoBelongStatuses;
use App\Models\Neo;
use App\Models\NeoBelong;
use App\Models\User;
use App\Models\NeoPrivacy;
use App\Traits\HandlesAuthorization;

class NeoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can access specific NEO model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Neo  $neo
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function owner(User $user, Neo $neo)
    {
        // Early return if rio not exists
        if (empty($user->rio)) {
            return $this->deny();
        }

        $neoOwner = $neo->owner;

        // Early return if neo owner not exists
        if (!$neoOwner) {
            return $this->deny();
        }

        return (($neoOwner->rio_id === $user->rio_id) && ($neoOwner->neo_id === $neo->id));
    }

    /**
     * Determine whether the user can access specific NEO model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Neo  $neo
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Neo $neo)
    {
        // Early return if rio not exists
        if (empty($user->rio)) {
            return $this->deny();
        }

        $neoPrivacy = NeoPrivacy::where('neo_id', $neo->id)
            ->where('accept_connections', '<>', AcceptConnectionType::PRIVATE_CONNECTION)
            ->exists();

        // Early return if neo belong not exists
        if (!$neoPrivacy) {
            return $this->deny();
        }

        return $neoPrivacy;
    }

    /**
     * Determine whether the user can view the neo.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Neo $neo
     * @return  \Illuminate\Auth\Access\Response
     */
    public function get($user, Neo $neo)
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
     * Determine whether the user can view the edit page of the neo.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Neo $neo
     * @return  \Illuminate\Auth\Access\Response
     */
    public function edit($user, Neo $neo)
    {
        /** @var int */
        $role = NeoBelong::whereNeoId($neo->id)
            ->whereRioId($user->rio_id)
            ->whereStatus(NeoBelongStatuses::AFFILIATED)
            ->value('role');

        // Guard clause for non-existing rio
        if (empty($user->rio) || !in_array($role, [RoleType::OWNER, RoleType::ADMINISTRATOR])) {
            return $this->deny();
        }

        return $neo->isUserAccessible()
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can update the neo.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Neo $neo
     * @return  \Illuminate\Auth\Access\Response
     */
    public function update($user, Neo $neo)
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
    * Determine whether the user can Manage Participants
    *
    * @param  \App\Models\User  $user
    * @param  \App\Models\Neo  $neo
    * @return \Illuminate\Auth\Access\Response|bool
    */
    public function manage(User $user, Neo $neo)
    {
        // Early return if rio not exists
        if (empty($user->rio)) {
            return $this->deny();
        }

        $neoBelong = NeoBelong::where('rio_id', $user->rio_id)
            ->where('neo_id', $neo->id)
            ->whereRole(RoleType::OWNER)
            ->first();

        // Early return if neo belong not exists
        if (!$neoBelong) {
            return $this->deny();
        }
        return (($neoBelong->neo_id === $neo->id) && ($neoBelong->rio_id === $user->rio_id));
    }


    /**
    * Determine whether the user can access privacy page
    *
    * @param  \App\Models\User $user
    * @param  \App\Models\Neo $neo
    * @return \Illuminate\Auth\Access\Response|bool
    */

    public function privacy(User $user, Neo $neo)
    {
        // Early return if rio not exists
        if (empty($user->rio)) {
            return $this->deny();
        }

        $neoBelong = NeoBelong::where('rio_id', $user->rio_id)
            ->where('neo_id', $neo->id)
            ->where('role', '!=', RoleType::MEMBER)
            ->first();

        // Early return if neo belong not exists
        if (!$neoBelong) {
            return $this->deny();
        }
        return (($neoBelong->neo_id === $neo->id) && ($neoBelong->rio_id === $user->rio_id));
    }
}
