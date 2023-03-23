<?php

namespace App\Http\Resources\Neo;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $productDescription = !empty($this->getAttributes()['additional'])
            ? $this->getAttributes()['additional']
            : '';

        return [
            'id' => $this->id,
            'content' => $this->content ?? '',
            'additional' => $productDescription,
            'reference_url' => json_decode($this->information)->reference_url ?? '',
            'image_link' => json_decode($this->information)->image_link ?? '',
        ];
    }
}
