<?php

namespace App\Http\Resources\Classified;

use App\Enums\ServiceSelectionTypes;
use App\Helpers\ConnectionHelper;
use App\Objects\ServiceSelected;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class ClassifiedContactResource extends JsonResource
{
    /**
     * Service Session
     *
     * @var object
     */
    private $session;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->session = ServiceSelected::getSelected();
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $lastMessageDate = !is_null($this->last_message_date) ?
            date_time_slash_format($this->last_message_date) : null;

        return [
            'id' => $this->id,
            'product_title' => $this->product_title,
            'display_name' => $this->display_name,
            'display_photo' => $this->getProfilePhoto(),
            'last_message' => $this->getLastMessageDisplay(),
            'last_message_date' => $lastMessageDate,
            'is_connected' => ConnectionHelper::isConnectedToSession(
                $this->display_entity_type,
                $this->display_entity_id,
                $this->session
            ),
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
            return Str::limit($this->message, config('bphero.inquiry_text_limit'));
        }

        // Display attachment message
        if (!empty($this->last_message_attachment)) {
            /** @var string */
            $attachmentMessage = __('Sent attachment', ['name' => $this->display_name]);

            return Str::limit($attachmentMessage, config('bphero.inquiry_text_limit'));
        }

        return null;
    }

    /**
     * Get profile photo
     *
     * @return string|null
     */
    public function getProfilePhoto()
    {
        $defaultImage = config('bphero.profile_image_directory') . config('bphero.profile_image_filename');

        // Display default image when no photo
        if (empty($this->display_photo)) {
            return asset($defaultImage);
        }

        switch ($this->display_entity_type) {
            case ServiceSelectionTypes::NEO:
                $imagePath = config('bphero.neo_profile_image') . $this->display_entity_id . '/' . $this->display_photo;

                return asset('storage/' . $imagePath);
            case ServiceSelectionTypes::RIO:
                $imagePath = config('bphero.rio_profile_image') . $this->display_entity_id . '/' . $this->display_photo;

                return asset('storage/' . $imagePath);
        }

        return asset($defaultImage);
    }
}
