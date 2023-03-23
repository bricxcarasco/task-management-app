<?php

namespace App\Http\Resources\Neo;

use Illuminate\Http\Resources\Json\JsonResource;

class AdministratorMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'neo_id' => $this->neo_id,
            'rio_id' => $this->rio_id,
            'role' => $this->role,
            'full_name' => $this->rio->full_name,
            'full_name_kana' => $this->rio->full_name_kana,
            'profile_image' => $this->rio->rio_profile->profile_image
        ];
    }
}
