<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\Document\DocumentTypes;
use App\Models\Document;
use Carbon\Carbon;

class DocumentResource extends JsonResource
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
            'is_owner' => $this->isOwner(),
            'owner_rio_id' => $this->owner_rio_id,
            'owner_neo_id' => $this->owner_neo_id,
            'directory_id' => $this->directory_id,
            'document_type' => $this->document_type,
            'document_name' => $this->document_name,
            'mime_type' => $this->mime_type,
            'storage_type_id' => $this->storage_type_id,
            'storage_path' => $this->storage_path,
            'file_bytes' => $this->file_bytes,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i'),
            $this->mergeWhen(
                $this->document_type === DocumentTypes::FOLDER,
                [
                    'sub_documents' => self::collection(Document::folderDocuments($this->id)->get())
                ]
            ),
        ];
    }
}
