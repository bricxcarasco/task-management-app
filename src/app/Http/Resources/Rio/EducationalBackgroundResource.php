<?php

namespace App\Http\Resources\Rio;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationalBackgroundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $graduationDisplay = !empty($this->getAttributes()['additional'])
            ? Carbon::parse($this->getAttributes()['additional'])->format('Y年m月')
            : '';

        return [
            'id' => $this->id,
            'rio_id' => $this->rio_id,
            'school_name' => $this->content ?? '',
            'graduation_date' => $this->getAttributes()['additional'] ?? '',
            'graduation_date_formatted' => $graduationDisplay,
        ];
    }
}
