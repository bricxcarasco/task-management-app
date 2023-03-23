<?php

namespace App\Http\Resources\ElectronicContract;

use Illuminate\Http\Resources\Json\JsonResource;

class RioEmailListResource extends JsonResource
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
            'email' => $this->email,
        ];
    }
}
