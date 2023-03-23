<?php

namespace App\Http\Resources\Classified;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassifiedSettingResource extends JsonResource
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
            'bank' => $this->bank ?? '',
            'branch' => $this->branch ?? '',
            'account_type' => $this->account_type ?? '',
            'account_number' => $this->account_number ?? '',
            'account_name' => $this->account_name ?? '',
        ];
    }
}
