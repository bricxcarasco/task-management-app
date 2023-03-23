<?php

namespace App\Policies;

use App\Enums\ServiceSelectionTypes;
use App\Objects\ServiceSelected;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\ClassifiedSale;
use Session;

class ClassifiedSalePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the classified product.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\ClassifiedSale $product
     * @return \Illuminate\Auth\Access\Response
     */
    public function update($user, ClassifiedSale $product)
    {
        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));

        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if able to update based on selected service
        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return ($service->data->id === $product->selling_rio_id)
                    ? $this->allow()
                    : $this->deny();
            case ServiceSelectionTypes::NEO:
                return ($service->data->id === $product->selling_neo_id)
                    ? $this->allow()
                    : $this->deny();
            default:
                return $this->deny();
        }
    }

    /**
     * Determine whether the user can delete the classified product.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\ClassifiedSale $product
     * @return \Illuminate\Auth\Access\Response
     */
    public function delete($user, ClassifiedSale $product)
    {
        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));

        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if able to delete based on selected service
        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return ($service->data->id === $product->selling_rio_id)
                    ? $this->allow()
                    : $this->deny();
            case ServiceSelectionTypes::NEO:
                return ($service->data->id === $product->selling_neo_id)
                    ? $this->allow()
                    : $this->deny();
            default:
                return $this->deny();
        }
    }

    /**
     * Determine whether the user can favorite.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function favorite($user)
    {
        $service = ServiceSelected::getSelected();
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return false;
        };

        return ($service->type === ServiceSelectionTypes::NEO && $service->data->is_owner || $service->type === ServiceSelectionTypes::RIO);
    }

    /**
     * Determine whether the user can register.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function register($user)
    {
        $service = ServiceSelected::getSelected();
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return false;
        };

        return ($service->type === ServiceSelectionTypes::NEO && !$service->data->is_member || $service->type === ServiceSelectionTypes::RIO);
    }
}
