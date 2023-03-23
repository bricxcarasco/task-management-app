<?php

namespace App\Events\Classified;

use App\Http\Resources\Classified\MessageResource;
use App\Models\ClassifiedContact;
use App\Models\ClassifiedContactMessage;
use App\Objects\ServiceSelected;
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
     * Contact ID
     *
     * @var int
     */
    public $contactId;

    /**
     * Create a new event instance.
     *
     * @param int $contactId
     * @return void
     */
    public function __construct($contactId)
    {
        $this->contactId = $contactId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('inquiry.conversation.' . $this->contactId);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'inquiry.receive';
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

            // Get subject selected session
            $service = ServiceSelected::getSelected();

            // Get contact
            $contact = ClassifiedContact::whereId($this->contactId)->firstOrFail();

            // Get inquiry messages
            $messages = ClassifiedContactMessage::messageList($this->contactId)->get();

            return [
                'status' => 200,
                'user' => $user,
                'service' => $service,
                'contact' => $contact,
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
