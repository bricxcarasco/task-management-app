<?php

namespace App\Http\Resources\Form;

use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
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
            'name' => $this->name ?? $this->supplier_name,
            'owner' => $owner,
            'form_no' => $this->quotation_no,
            'title' => $this->title,
            'price' => CommonHelper::priceFormat($this->price),
            'issue_date' => $this->issue_date,
            'receipt_date' => $this->receipt_date,
            'expiration_date' => $this->expiration_date,
            'delivery_date' => $this->delivery_date,
            'delivery_deadline' => $this->delivery_deadline,
            'payment_date' => $this->payment_date,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
        ];
    }
}
