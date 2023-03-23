<?php

namespace App\Http\Resources\ElectronicContract;

use Illuminate\Http\Resources\Json\JsonResource;

class ConnectionListResource extends JsonResource
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
            'kana' => $this->kana,
            'service' => $this->service,
            'profile_photo' => $this->profile_photo,
        ];
    }
}
