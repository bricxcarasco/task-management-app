<?php

namespace App\Http\Resources\Neo;

use App\Enums\YearsOfExperiences;
use Illuminate\Http\Resources\Json\JsonResource;

class IndustryResource extends JsonResource
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
            'business_category_id' => $this->business_category_id,
            'additional' => $this->getAttributes()['additional'],
            'value' => $this->content ?? '',
            'years' => YearsOfExperiences::getDescription($this->getAttributes()['additional']),
        ];
    }
}
