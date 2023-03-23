<?php

namespace App\Policies;

use App\Enums\ServiceSelectionTypes;
use App\Models\Document;
use App\Traits\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can rename the document.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Document $document
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function rename($user, Document $document, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if service selected is RIO owner
        if ($service->type === ServiceSelectionTypes::RIO) {
            return $document->isDocumentOwner($service)
                ? $this->allow()
                : $this->deny();
        }

        // Check if service selected is NEO owner/admin
        if ($service->type === ServiceSelectionTypes::NEO) {
            return $document->isAuthorizedNeoUser($service)
                ? $this->allow()
                : $this->deny();
        }

        return $this->deny();
    }

    /**
     * Determine whether the user can delete the document.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Document $document
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function delete($user, Document $document, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if service selected is RIO owner
        if ($service->type === ServiceSelectionTypes::RIO) {
            return $document->isDocumentOwner($service)
                ? $this->allow()
                : $this->deny();
        }

        // Check if service selected is NEO owner/admin
        if ($service->type === ServiceSelectionTypes::NEO) {
            return $document->isAuthorizedNeoUser($service)
                ? $this->allow()
                : $this->deny();
        }

        return $this->deny();
    }

    /**
     * Determine whether the user can create new document.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Document $document
     * @return  \Illuminate\Auth\Access\Response
     */
    public function allowed($user, Document $document)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return $document->isAllowedInDocumentManagement()
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can access document.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Document $document
     * @return  \Illuminate\Auth\Access\Response
     */
    public function documentAccess($user, Document $document)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return $document->isAllowedDocumentAccess()
            ? $this->allow()
            : $this->deny();
    }

    /**
     * It checks if the user is allowed to view a file.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Document $document
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function viewFile($user, Document $document, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return $document->isFileViewable($document, $service)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can share the document.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Document $document
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function share($user, Document $document, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        switch ($service->type) {
            case (ServiceSelectionTypes::RIO):
                return $document->isDocumentOwner($service)
                    ? $this->allow()
                    : $this->deny();
            case (ServiceSelectionTypes::NEO):
                return $document->isAuthorizedNeoUser($service)
                    ? $this->allow()
                    : $this->deny();
            default:
                return $this->deny();
        }
    }

    /**
     * Determine whether the user can share the file/folder.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Document $document
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function download($user, Document $document, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        $documentAccess = $document->isUserAllowed($document->id, $service);

        if ($documentAccess) {
            return $this->allow();
        }

        switch ($service->type) {
            case (ServiceSelectionTypes::RIO):
                return $document->isDocumentOwner($service)
                    ? $this->allow()
                    : $this->deny();
            case (ServiceSelectionTypes::NEO):
                return $document->isNeoBelong($service)
                    ? $this->allow()
                    : $this->deny();
            default:
                return $this->deny();
        }
    }

    /**
     * Determine whether the user can upload files based on target directory.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Document $document
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function uploadFile($user, Document $document, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Checks if user allowed to create new document
        if (!$document->isAllowCreateToDocument()) {
            return $this->deny();
        }

        // Checks if target directory is owned by the uploader
        return $document->isDirectoryOwner($document, $service)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the user can delete the document.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\Document $document
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function viewPermittedList($user, Document $document, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        // Check if service selected is RIO owner
        if ($service->type === ServiceSelectionTypes::RIO) {
            return $document->isDocumentOwner($service)
                ? $this->allow()
                : $this->deny();
        }

        // Check if service selected is NEO owner/admin
        if ($service->type === ServiceSelectionTypes::NEO) {
            return $document->isAuthorizedNeoUser($service)
                ? $this->allow()
                : $this->deny();
        }

        return $this->deny();
    }
}
