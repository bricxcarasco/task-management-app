<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use App\Enums\Chat\ChatTypes;
use App\Objects\TalkSubject;

class ChatListResource extends JsonResource
{
    /**
     * Talk subject session
     *
     * @var object|null
     */
    private $talkSubject = null;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->talkSubject = TalkSubject::getSelected();
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $chatTypeLabel = ChatTypes::getDescription($this->chat_type);
        $chatLastMessageDate = !is_null($this->last_message_date) ?
            date_time_slash_format($this->last_message_date) : null;

        return [
            'chat_id' => $this->id,
            'chat_type_id' => $this->chat_type,
            'chat_type' => $chatTypeLabel,
            'chat_name' => $this->display_name,
            'chat_image' => $this->display_icon,
            'last_message' => $this->getLastMessageDisplay(),
            'last_message_date' => $chatLastMessageDate,
            'unread_messages_count' => $this->unread_messages_count ?: 0,
        ];
    }

    /**
     * Get parsed last message display
     *
     * @return string|null
     */
    public function getLastMessageDisplay()
    {
        // Display last message
        if (!empty($this->message)) {
            return Str::limit($this->message, config('bphero.chat_text_limit'));
        }

        // Display attachment message
        if (!empty($this->last_message_attachment)) {
            /** @var string */
            $attachmentMessage = __('Sent attachment', ['name' => $this->last_message_name]);

            return Str::limit($attachmentMessage, config('bphero.chat_text_limit'));
        }

        return null;
    }
}
