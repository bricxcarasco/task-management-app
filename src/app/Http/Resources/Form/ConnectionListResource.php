<?php

namespace App\Http\Resources\Form;

use App\Enums\PrefectureTypes;
use Illuminate\Http\Resources\Json\JsonResource;

class ConnectionListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $prefectures = PrefectureTypes::asSelectArray();
        $combinedAddress = (($this->address_prefecture > 0)
            ? $prefectures[$this->address_prefecture] .
                $this->address_city .
                $this->address .
                $this->address_building
            : $this->address_nationality .
                (($this->address_city != '') ? ', ' . $this->address_city : '') .
                (($this->address != '') ? ', ' . $this->address : '') .
                (($this->address_building != '') ? ', ' . $this->address_building : ''));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'kana' => $this->kana,
            'service' => $this->service,
            'profile_photo' => $this->profile_photo,
            'address' => $combinedAddress,
        ];
    }
}
