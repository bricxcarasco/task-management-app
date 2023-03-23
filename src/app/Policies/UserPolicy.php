<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $currentUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update($user, User $currentUser)
    {
        // Check if authenticated user is the current user updating
        return ($user->id === $currentUser->id);
    }
}
