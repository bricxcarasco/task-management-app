<?php

namespace App\Policies;

use App\Models\ElectronicContract;
use App\Models\User;
use App\Traits\HandlesAuthorization;

class ElectronicContractPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create electronic contract.
     *
     * @param  \App\Models\User  $user
     * @param  object  $service
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function store(User $user, $service)
    {
        /** @var array */
        $availableSlot = ElectronicContract::availableSlot($service);

        if (empty($availableSlot['slot'])) {
            return $this->deny();
        }

        if ($availableSlot['expired'] ?? true) {
            return $this->deny();
        }

        return $this->allow();
    }
}
