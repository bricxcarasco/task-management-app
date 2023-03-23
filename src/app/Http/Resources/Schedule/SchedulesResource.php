<?php

namespace App\Http\Resources\Schedule;

use Illuminate\Http\Resources\Json\JsonResource;

class SchedulesResource extends JsonResource
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
            'schedule_guest_id' => $this->schedule_guest_id,
            'owner_rio_id' => $this->owner_rio_id,
            'owner_neo_id' => $this->owner_neo_id,
            'created_rio_id' => $this->created_rio_id,
            'schedule_title' => $this->schedule_title,
            'start_date' => $this->start_date,
            'start_time' => $this->start_time,
            'end_date' => $this->end_date,
            'end_time' => $this->end_time,
            'meeting_url' => $this->meeting_url,
            'caption' => $this->caption,
            'schedule_id' => $this->schedule_id,
            'rio_id' => $this->rio_id,
            'neo_id' => $this->neo_id,
            'status' => $this->status,
            'owner_rio' => $this->schedule->owner_rio,
            'owner_neo' => $this->schedule->owner_neo,
        ];
    }
}
