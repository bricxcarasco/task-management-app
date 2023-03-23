<?php

namespace App\Http\Resources\Knowledge;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'knowledge_id' => $this->knowledge_id,
            'rio_id' => $this->rio_id,
            'name' => $this->name ?? null,
            'comment' => $this->comment,
            'user_image' => $this->profile_photo,
            'date' => Carbon::parse($this->created_at)->format('Y/m/d'),
        ];
    }
}
