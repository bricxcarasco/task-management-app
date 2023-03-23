<?php

namespace App\Http\Resources\Knowledge;

use App\Enums\Knowledge\Types;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'id'=> $this->id,
            'owner_rio_id'=> $this->owner_rio_id,
            'owner_neo_id'=> $this->owner_neo_id,
            'created_rio_id'=> $this->created_rio_id,
            'is_owned' => $this->isOwned() && $this->isAuthorizedUser(),
            'directory_id'=> $this->directory_id,
            'type'=> Types::ARTICLE,
            'task_title'=> $this->task_title,
            'contents'=> $this->contents,
            'urls'=> $this->urls,
            'is_draft'=> $this->is_draft,
            'created_at'=> Carbon::parse($this->created_at)->format('Y-m-d'),
        ];
    }
}
