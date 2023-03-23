<?php

namespace App\Policies;

use App\Traits\HandlesAuthorization;
use App\Models\ChatMessage;
use App\Enums\ServiceSelectionTypes;

class ChatMessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user is the owner of chat message.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\ChatMessage $chatMessage
     * @param   mixed $service
     * @return  \Illuminate\Auth\Access\Response
     */
    public function delete($user, ChatMessage $chatMessage, $service)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        /** @var \App\Models\ChatParticipant */
        $chatParticipant = $chatMessage->participant;

        // Check if service selected is RIO owner
        if ($service->type === ServiceSelectionTypes::RIO) {
            return $chatParticipant->rio_id === $user->rio_id
                ? $this->allow()
                : $this->deny();
        }

        // Check if service selected is NEO owner/admin
        if ($service->type === ServiceSelectionTypes::NEO) {
            return $chatParticipant->neo_id === $service->data->id
                ? $this->allow()
                : $this->deny();
        }

        return $this->allow();
    }
}
