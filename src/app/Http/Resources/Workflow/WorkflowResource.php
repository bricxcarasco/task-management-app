<?php

namespace App\Http\Resources\Workflow;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowResource extends JsonResource
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
            'name' => $this->name,
            'owner_neo_id' => $this->owner_neo_id,
            'created_rio_id' => $this->created_rio_id,
            'workflow_title' => $this->workflow_title,
            'caption' => $this->caption,
            'status' => $this->status,
            'attaches' => $this->attaches,
            'approver_status' => $this->approver_status,
            'priority' => $this->priority,
            'actions' => $this->actions,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
