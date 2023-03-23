<?php

namespace App\Policies;

use App\Enums\ServiceSelectionTypes;
use App\Models\User;
use App\Objects\ServiceSelected;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassifiedSettingPolicy
{
    use HandlesAuthorization;

    /**
    * Determine whether the user can view
    *
    * @param  \App\Models\User  $user
    * @return \Illuminate\Auth\Access\Response|bool
    */
    public function view($user)
    {
        $service = ServiceSelected::getSelected();

        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return false;
        };

        return ($service->type === ServiceSelectionTypes::NEO && !$service->data->is_member || $service->type === ServiceSelectionTypes::RIO);
    }
}
