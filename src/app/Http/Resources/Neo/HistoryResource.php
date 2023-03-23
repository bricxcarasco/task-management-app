<?php

namespace App\Http\Resources\Neo;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $displayDate = !empty($this->getAttributes()['additional'])
            ? Carbon::parse($this->getAttributes()['additional'])->format('Y年m日')
            : '';

        return [
            'id' => $this->id,
            'content' => $this->content ?? '',
            'additional' => $displayDate,
        ];
    }
}
