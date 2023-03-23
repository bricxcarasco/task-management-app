<?php

namespace App\Http\Resources\Chat;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $imageDirectory = config('bphero.profile_image_directory');
        $defaultImage = config('bphero.profile_image_filename');
        $userImage = asset($imageDirectory . $defaultImage);

        return [
            'id' => $this->id,
            'participant_id' => $this->chat_participant_id,
            'entity_id' => $this->entity_id,
            'participant_type' => $this->participant_type,
            'name' => $this->name ?? null,
            'message' => $this->message,
            'attaches' => $this->attaches,
            'user_image' => $this->profile_photo,
            'attachments' => $this->file_attachments,
            'date' => Carbon::parse($this->created_at)->format('Y/m/d H:i'),
        ];
    }
}
