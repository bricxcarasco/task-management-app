<?php

namespace App\Http\Resources\Task;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $owner = null;
        if (!empty($this->owner_rio_id)) {
            $owner = 'rio_' . $this->owner_rio_id;
        }
        if (!empty($this->owner_neo_id)) {
            $owner = 'neo_' . $this->owner_neo_id;
        }

        $limitDatetime = null;
        if (!empty($this->limit_date) && !empty($this->limit_time)) {
            $datetime = $this->limit_date . ' ' . $this->limit_time;
            $limitDatetime = Carbon::parse($datetime)->format('Y/m/d H:i');
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'owner_rio_id' => $this->owner_rio_id,
            'owner_neo_id' => $this->owner_neo_id,
            'owner' => $owner,
            'limit_date' => $this->limit_date,
            'limit_time' => Carbon::parse($this->limit_time)->format('H:i'),
            'limit_datetime' => $limitDatetime,
            'finished' => $this->finished,
            'priority' => $this->priority,
            'created_at' => $this->created_at,
            'task_title' => $this->task_title,
            'remarks' => $this->remarks,
        ];
    }
}
