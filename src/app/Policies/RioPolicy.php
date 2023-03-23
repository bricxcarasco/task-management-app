<?php

namespace App\Policies;

use App\Models\Rio;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RioPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can connect to a Rio.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rio  $rio
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function connect($user, Rio $rio)
    {
        return $user->rio_id !== $rio->id;
    }

    /**
     * Determine whether the user can update and delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rio  $rio
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateDelete($user, Rio $rio)
    {
        return $user->rio_id === $rio->id;
    }
}
