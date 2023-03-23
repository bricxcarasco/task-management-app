<?php

namespace App\Events\Chat;

use App\Http\Resources\Chat\MessageResource;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Receive messages.
 */
class ReceiveMessages implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Chat ID
     *
     * @var int
     */
    public $chatId;

    /**
     * Create a new event instance.
     *
     * @param int $chatId
     * @return void
     */
    public function __construct($chatId)
    {
        $this->chatId = $chatId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat.room.' . $this->chatId);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.receive';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        try {
            /** @var \App\Models\User */
            $user = auth()->user();

            // Get chat
            $chat = Chat::whereId($this->chatId)->firstOrFail();

            // Get chat messages
            $messages = ChatMessage::messageList($chat->id)->get();

            return [
                'status' => 200,
                'user' => $user,
                'chat' => $chat,
                'messages' => MessageResource::collection($messages),
            ];
        } catch (\Exception $e) {
            report($e);

            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }
}
