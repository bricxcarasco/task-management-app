<?php

namespace App\Http\Resources\Classified;

use App\Enums\Classified\MessageSender;
use App\Objects\ClassifiedImages;
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
        $defaultImageFile = config('bphero.profile_image_filename');
        $defaultImage = asset($imageDirectory . $defaultImageFile);

        $name = $this->sender == MessageSender::BUYER
            ? $this->buyer_name
            : $this->seller_name;
        $image = $this->sender == MessageSender::BUYER
            ? $this->buyer_photo
            : $this->seller_photo;
        $ownerId = $this->sender == MessageSender::BUYER
            ? $this->buyer_id
            : $this->seller_id;
        $ownerType = $this->sender == MessageSender::BUYER
            ? $this->buyer_type
            : $this->seller_type;

        return [
            'id' => $this->id,
            'classified_contact_id' => $this->classified_contact_id,
            'sender' => $this->sender,
            'message' => $this->message,
            'attaches' => $this->attaches,
            'seller_id' => $this->seller_id,
            'seller_name' => $this->seller_name,
            'seller_photo' => $this->seller_photo,
            'seller_type' => $this->seller_type,
            'buyer_id' => $this->buyer_id,
            'buyer_name' => $this->buyer_name,
            'buyer_photo' => $this->buyer_photo,
            'buyer_type' => $this->buyer_type,
            'name' => $name ?? null,
            'owner_id' => $ownerId ?? null,
            'owner_type' => $ownerType ?? null,
            'image' => !empty($image) ? $image : $defaultImage,
            'image_urls' => ClassifiedImages::getChatImages($this->attaches),
            'created_at' => $this->created_at,
            'date' => Carbon::parse($this->created_at)->format('Y/m/d H:i'),
        ];
    }
}
