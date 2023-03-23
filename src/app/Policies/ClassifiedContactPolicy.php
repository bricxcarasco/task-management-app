<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\ClassifiedContact;

class ClassifiedContactPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user access the inquiry conversation.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\ClassifiedContact $contact
     * @return \Illuminate\Auth\Access\Response
     */
    public function access($user, ClassifiedContact $contact)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if able to access based on selected service
        return $contact->isAllowedAccess()
            ? $this->allow()
            : $this->deny();
    }
}
