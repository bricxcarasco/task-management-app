<?php

namespace App\Http\Resources\Rio;

use Illuminate\Http\Resources\Json\JsonResource;

class AwardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $awardYear = !empty($this->getAttributes()['additional'])
            ? "{$this->getAttributes()['additional']}年"
            : '';

        return [
            'id' => $this->id,
            'content' => $this->content ?? '',
            'award_year' => $this->getAttributes()['additional'] ?? '',
            'award_year_display' => $awardYear,
        ];
    }
}
