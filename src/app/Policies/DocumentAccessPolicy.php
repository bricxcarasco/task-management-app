<?php

namespace App\Policies;

use App\Enums\ServiceSelectionTypes;
use App\Models\DocumentAccess;
use App\Traits\HandlesAuthorization;

class DocumentAccessPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the document.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\DocumentAccess $documentAccess
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function unshare($user, DocumentAccess $documentAccess, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if service selected is RIO owner
        if ($service->type === ServiceSelectionTypes::RIO) {
            return $documentAccess->isDocumentOwner($service)
                ? $this->allow()
                : $this->deny();
        }

        // Check if service selected is NEO owner/admin
        if ($service->type === ServiceSelectionTypes::NEO) {
            return $documentAccess->isAuthorizedNeoUser($service)
                ? $this->allow()
                : $this->deny();
        }

        return $this->allow();
    }
}
