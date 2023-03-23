<?php

namespace App\Http\Resources\Classified;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassifiedSalesCategoryResource extends JsonResource
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
            'sale_category_name' => $this->sale_category_name,
        ];
    }
}
