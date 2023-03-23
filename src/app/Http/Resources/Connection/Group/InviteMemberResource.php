<?php

namespace App\Http\Resources\Connection\Group;

use Illuminate\Http\Resources\Json\JsonResource;

class InviteMemberResource extends JsonResource
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
            'full_name' => $this->full_name,
            'profile_image' => $this->rio_profile->profile_image,
            'invite_id' => $this->invite_id,
            'invite_status' => $this->invite_status,
        ];
    }
}
