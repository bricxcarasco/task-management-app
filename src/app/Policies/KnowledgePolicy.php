<?php

namespace App\Policies;

use App\Enums\ServiceSelectionTypes;
use App\Models\Knowledge;
use App\Traits\HandlesAuthorization;

class KnowledgePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user folder is accessible.
     *
     * @param   \App\Models\User $user
     * @param   int $id
     * @return  \Illuminate\Auth\Access\Response
     */
    public function accessibleFolder($user, int $id)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Get knowledge entity
        $knowledge = Knowledge::whereId($id)->firstOrFail();

        // Check if user is allowed to access folders/articles
        return $knowledge->isOwned()
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can view or download the article.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Knowledge $knowledge
     * @return  \Illuminate\Auth\Access\Response
     */
    public function viewable($user, Knowledge $knowledge)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return $knowledge->isViewable($knowledge)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can delete the folder or article.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Knowledge $knowledge
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function create($user, Knowledge $knowledge, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Return when in root directory
        if (empty($knowledge->directory_id)) {
            return $this->allow();
        }

        return $knowledge->isAuthorizedToCreate($service)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can modify the knowledge record.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Knowledge $knowledge
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function modifiable($user, Knowledge $knowledge, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if service selected is RIO owner
        if ($service->type === ServiceSelectionTypes::RIO) {
            return $knowledge->isOwned()
                ? $this->allow()
                : $this->deny();
        }

        // Check if service selected is NEO owner/admin
        if ($service->type === ServiceSelectionTypes::NEO) {
            return $knowledge->isAuthorizedUser() && $knowledge->isOwned()
                ? $this->allow()
                : $this->deny();
        }

        return $this->deny();
    }

    /**
     * Determine whether the user have access on article drafts
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Knowledge $knowledge
     * @return  \Illuminate\Auth\Access\Response
     */
    public function accessibleDraft($user, Knowledge $knowledge)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return $knowledge->isOwned()
            ? $this->allow()
            : $this->deny();
    }
}
