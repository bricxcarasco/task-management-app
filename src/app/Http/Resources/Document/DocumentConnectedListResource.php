<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentConnectedListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'service' => $this->service,
            'profile_photo' => $this->profile_photo,
        ];
    }
}
