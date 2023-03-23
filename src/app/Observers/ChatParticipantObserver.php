<?php

namespace App\Observers;

use App\Models\ChatParticipant;

class ChatParticipantObserver
{
    /**
     * Handle the ChatParticipant "created" event.
     *
     * @param  \App\Models\ChatParticipant  $chatParticipant
     * @return void
     */
    public function creating(ChatParticipant $chatParticipant)
    {
        // Initialize existing participant query
        $query = ChatParticipant::query()
            ->whereChatId($chatParticipant->chat_id)
            ->whereNotNull('last_read_chat_message_id');

        // Inject RIO entity condition
        if (!empty($chatParticipant->rio_id)) {
            $query->whereRioId($chatParticipant->rio_id);
        } else {
            $query->whereNull('rio_id');
        }

        // Inject NEO entity condition
        if (!empty($chatParticipant->neo_id)) {
            $query->whereRioId($chatParticipant->neo_id);
        } else {
            $query->whereNull('neo_id');
        }

        // Fetch existing user with last message read
        $existingParticipant = $query
            ->orderBy('id', 'desc')
            ->withTrashed()
            ->first();

        // Inject previous last message read to new record but same user and chat
        if (!empty($existingParticipant)) {
            $chatParticipant->last_read_chat_message_id = $existingParticipant->last_read_chat_message_id ?? null;
        }
    }
}
