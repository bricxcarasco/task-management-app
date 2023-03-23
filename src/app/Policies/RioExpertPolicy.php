<?php

namespace App\Policies;

use App\Models\RioExpert;
use Illuminate\Auth\Access\HandlesAuthorization;

class RioExpertPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the rio expert.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\RioExpert $expert
     * @return  bool
     */
    public function update($user, RioExpert $expert)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return false;
        }

        // Check if expert belongs to rio user
        return ($user->rio_id === $expert->rio_id);
    }

    /**
     * Determine whether the user can delete the rio expert.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\RioExpert $expert
     * @return  bool
     */
    public function delete($user, RioExpert $expert)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return false;
        }

        // Check if expert belongs to rio user
        return ($user->rio_id === $expert->rio_id);
    }
}
