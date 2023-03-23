<?php

namespace App\Http\Resources\Form;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $owner = null;
        if (!empty($this->supplier_rio_id)) {
            $owner = 'rio_' . $this->supplier_rio_id;
        }
        if (!empty($this->supplier_neo_id)) {
            $owner = 'neo_' . $this->supplier_neo_id;
        }

        return [
            'id' => $this->id,
            'rio_id' => $this->rio_id,
            'neo_id' => $this->neo_id,
            'name' => $this->name,
            'owner' => $owner,
            'form_id' => $this->form_id,
            'operation_datetime' => $this->operation_datetime,
            'operation_details' => $this->operation_details,
            'operator_email' => $this->operator_email,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'deleted_at' => Carbon::parse($this->deleted_at)->format('Y-m-d H:i:s'),
        ];
    }
}
