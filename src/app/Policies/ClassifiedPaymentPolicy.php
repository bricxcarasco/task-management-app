<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\ClassifiedPayment;

class ClassifiedPaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user access the Payment URL.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\ClassifiedPayment $payment
     * @return \Illuminate\Auth\Access\Response
     */
    public function accessUrl($user, ClassifiedPayment $payment)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if able to access based on selected service
        return $payment->isAllowedAccess()
            ? $this->allow()
            : $this->deny();
    }
}
