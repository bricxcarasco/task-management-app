<?php

namespace App\Http\Resources\ElectronicContract;

use Illuminate\Http\Resources\Json\JsonResource;

class NeoEmailListResource extends JsonResource
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
            'attribute_code' => $this->attribute_code,
            'email' => $this->content,
        ];
    }
}
