<?php

namespace App\Http\Resources\Connection\Search;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchListResource extends JsonResource
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
            'profile_image' => $this->profile_image,
        ];
    }
}
