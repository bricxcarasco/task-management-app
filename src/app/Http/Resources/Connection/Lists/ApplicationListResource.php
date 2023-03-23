<?php

namespace App\Http\Resources\Connection\Lists;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationListResource extends JsonResource
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
            'connection_id' => $this->connection_id,
            'id' => $this->id,
            'name' => $this->name,
            'service' => $this->service,
            'profile_photo' => $this->profile_photo,
            'message' => $this->message,
        ];
    }
}
