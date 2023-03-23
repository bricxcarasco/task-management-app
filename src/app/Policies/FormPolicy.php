<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Form;

class FormPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user edit the form.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Form $form
     * @param   int $type. Defaults to 1 (Quotation)
     * @return \Illuminate\Auth\Access\Response
     */
    public function edit($user, Form $form, $type = 1)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if able to delete based on selected service
        return $form->isOwned($form, $type)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user delete the form.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Form $form
     * @param   int $type. Defaults to 1 (Quotation)
     * @return \Illuminate\Auth\Access\Response
     */
    public function delete($user, Form $form, $type = 1)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if able to delete based on selected service
        return $form->isOwned($form, $type)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can duplicate the form.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Form $form
     * @param   int $type. Defaults to 1 (Quotation)
     * @return \Illuminate\Auth\Access\Response
     */
    public function duplicate($user, Form $form, $type = 1)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if able to duplicate based on selected service
        return $form->isOwned($form, $type)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can create from diff form types.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Form $form
     * @param   int $type. Defaults to 1 (Quotation)
     * @return \Illuminate\Auth\Access\Response
     */
    public function createFromDiffForm($user, Form $form, $type = 1)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if able to duplicate based on selected service
        return $form->isFormCreatable($form, $type)
            ? $this->allow()
            : $this->deny();
    }
}
