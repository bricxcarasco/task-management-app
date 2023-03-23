<?php

namespace App\Http\Resources\Document;

use App\Enums\ServiceSelectionTypes;
use App\Models\Document;
use Illuminate\Http\Resources\Json\JsonResource;
use Session;

class DocumentPermittedListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $service = json_decode(Session::get('ServiceSelected'));

        return [
            'access_id' => $this->access_id,
            'name' => $this->name,
            'profile_photo' => $this->profile_photo,
            'ownership' => $this->when(self::isOwner($this->service, $this->id, $this->document_id), 'OWNER'),
        ];
    }

    /**
     * Determine if NEO/RIO is owner
     *
     * @param string $serviceType
     * @param int $serviceID
     * @param int $documentID
     * @return bool
     */
    public function isOwner($serviceType, $serviceID, $documentID)
    {
        if ($serviceType === 'NEO_Group') {
            return false;
        }

        return Document::where('id', $documentID)
            ->when($serviceType === ServiceSelectionTypes::RIO, function ($q1) use ($serviceID) {
                return $q1->where('owner_rio_id', $serviceID);
            })
            ->when($serviceType === ServiceSelectionTypes::NEO, function ($q1) use ($serviceID) {
                return $q1->where('owner_neo_id', $serviceID);
            })
            ->exists();
    }
}
