<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\ServiceSelectionTypes;
use App\Objects\ServiceSelected;

class DocumentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // 'documents.id as documentId'
        // 'documents.owner_rio_id as ownerRioId'
        // 'documents.owner_neo_id as ownerNeoId'
        // 'documents.directory_id as fileDirectoryId'
        // 'documents.document_type as documentType'
        // 'documents.document_name as name',
        // 'documents.storage_path as filePath'
        // 'documents.mime_type as fileType'

        /** @var \App\Models\User */
        $user = auth()->user();

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $link = route('document.shared-file-preview-route', $this->documentId);

        $isOwnerRio = ($this->ownerRioId == $user->rio_id);
        $isOwnerNeo = ($service->type === ServiceSelectionTypes::NEO && $service->data->id == $this->ownerNeoId);

        if ($isOwnerRio || $isOwnerNeo) {
            $link = route('document.file-preview-route', $this->documentId);
        }

        return [
            'document_id' => $this->documentId,
            'owner_rio_id' => $this->ownerRioId,
            'owner_neo_id' => $this->ownerNeoId,
            'file_directory_id' => $this->fileDirectoryId,
            'document_type' => $this->documentType,
            'name' => $this->name,
            'file_path' => $this->filePath,
            'file_type' => $this->fileType,
            'link' => $link
        ];
    }
}
