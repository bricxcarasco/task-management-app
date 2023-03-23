<?php

namespace App\Http\Resources\Classified;

use App\Enums\Classified\SaleProductAccessibility;
use App\Enums\Classified\SaleProductVisibility;
use App\Helpers\CommonHelper;
use App\Objects\ClassifiedImages;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassifiedProductResource extends JsonResource
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
            'selling_rio_id' => $this->selling_rio_id,
            'selling_neo_id' => $this->selling_neo_id,
            'created_rio_id' => $this->created_rio_id,
            'selling_id' => $this->selling_id,
            'selling_type' => $this->selling_type,
            'selling_name' => $this->selling_name,
            'sale_category' => $this->sale_category,
            'sale_category_name' => $this->sale_category_name,
            'title' => $this->title,
            'detail' => $this->detail,
            'images' => $this->images,
            'main_photo' => ClassifiedImages::getMainPhotoUrl($this->images),
            'image_urls' => ClassifiedImages::getImageUrls($this->images),
            'price' => CommonHelper::priceFormat($this->price),
            'is_accept' => $this->is_accept,
            'is_public' => $this->is_public,
            'is_owned' => $this->is_owned,
            'is_favorite' => $this->favorites->count() > 0,
            'is_connected' => $this->isConnected($this->selling_type, $this->selling_id),
            'product_visibility' => SaleProductVisibility::getDescription($this->is_public),
            'product_accessibility' => SaleProductAccessibility::getDescription($this->is_accept),
            'registered_date' => Carbon::parse($this->created_at)->format('Y-m-d'),
        ];
    }
}
