<?php

namespace App\Http\Resources\Rio;

use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateResource extends JsonResource
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
            'is_display' => $this->is_display,
            'organization_name' => $this->organization_name,
        ];
    }
}
