<?php

namespace App\Models;

use App\Enums\Chat\ChatTypes;
use App\Enums\Document\DocumentTypes;
use App\Enums\MailTemplates;
use App\Enums\Document\StorageTypes;
use App\Enums\EntityType;
use App\Enums\Neo\RoleType;
use App\Enums\ServiceSelectionTypes;
use App\Helpers\CommonHelper;
use App\Notifications\DocumentSharingNotification;
use App\Objects\FilepondFile;
use App\Objects\ServiceSelected;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Session;

/**
 * App\Models\Document
 *
 * @property int $id id for Laravel
 * @property int|null $owner_rio_id
 * @property int|null $owner_neo_id
 * @property int|null $directory_id
 * @property string $document_name
 * @property string|null $mime_type
 * @property int $storage_type_id 1:HERO、2:GoogleDrive、3:Dropbox...
 * @property string $storage_path
 * @property int|null $file_bytes
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DocumentAccess[] $document_accesses
 * @property-read int|null $document_accesses_count
 * @property-read Document|null $directory
 * @property-read \Illuminate\Database\Eloquent\Collection|Document[] $documents
 * @property-read int|null $documents_count
 * @property-read Document|null $parentDocument
 * @method static \Illuminate\Database\Eloquent\Builder|Document fileList($service, $options)
 * @method static \Illuminate\Database\Eloquent\Builder|Document folderList($service, $options)
 * @method static \Illuminate\Database\Eloquent\Builder|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document newQuery()
 * @method static \Illuminate\Database\Query\Builder|Document onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDirectoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDocumentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereFileBytes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereOwnerNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereOwnerRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereStoragePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereStorageTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Document withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Document withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Document attachmentList($service, $options)
 * @method static \Illuminate\Database\Eloquent\Builder|Document documentList($service, $options)
 * @method static \Illuminate\Database\Eloquent\Builder|Document folderDocuments(int $id)
 * @mixin \Eloquent
 * @property int $document_type 1:Folder（フォルダ）,2:File（ファイル）3:Attachement(添付ファイル)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDocumentType($value)
 */
class Document extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'owner_rio_id',
        'owner_neo_id',
        'directory_id',
        'document_type',
        'document_name',
        'mime_type',
        'storage_type_id',
        'storage_path',
        'file_bytes'
    ];

    /**
     * Define relationship with rio connecton users model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function document_accesses()
    {
        return $this->hasMany(DocumentAccess::class);
    }

    /**
     * Define relationship with itself class
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(self::class, 'directory_id');
    }

    /**
     * Define relationship with itself class
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentDocument()
    {
        return $this->belongsTo(self::class, 'directory_id');
    }

    /**
     * Send email notification upon document sharing.
     *
     * @param \App\Models\Rio|\App\Models\Neo $sender
     * @param array $receivers
     * @return void
     */
    public static function sendEmailToSharedDocumentReceiver($sender, $receivers)
    {
        foreach ($receivers as $receiver) {
            // Check RIO receiver if allowed to receive email
            if ($receiver instanceof \App\Models\Rio) {
                if (NotificationRejectSetting::isRejectedEmail(
                    $receiver,
                    MailTemplates::ADD_DOCUMENT_SHARING
                )) {
                    // Skip and proceed to next email receiver
                    continue;
                }

                // Get email information
                $user = User::whereRioId($receiver->id)->firstOrFail();

                // Send email to RIO receiver
                Notification::sendNow($user, new DocumentSharingNotification($sender, $receiver));
            }

            // Check NEO owner receiver if allowed to receive email
            if ($receiver instanceof \App\Models\Neo) {
                // Get NEO owner NeoBelong information
                /** @var \App\Models\NeoBelong */
                $neoBelongs = $receiver->owner;

                // Guard clause for non-existing NEO owner
                if (empty($neoBelongs)) {
                    // Skip and proceed to next email receiver
                    continue;
                }

                // Get NEO owner RIO information
                /** @var \App\Models\Rio */
                $neoOwnerReceiver = $neoBelongs->rio;

                if (NotificationRejectSetting::isRejectedEmail(
                    $neoOwnerReceiver,
                    MailTemplates::ADD_DOCUMENT_SHARING
                )) {
                    // Skip and proceed to next email receiver
                    continue;
                }

                // Get email information
                $user = User::whereRioId($neoOwnerReceiver->id)->firstOrFail();

                // Send email to NEO receiver
                Notification::sendNow($user, new DocumentSharingNotification($sender, $receiver));
            }
        }
    }

    /**
     * Get all parent documents within a single document
     *
     * @return object
     */
    public function getParentDocuments()
    {
        $documentCollection = collect([]);

        if ($this->parentDocument) {
            $documentCollection->push($this->parentDocument);
            $documentCollection = $documentCollection->merge($this->parentDocument->getParentDocuments());
        }

        return $documentCollection;
    }

    /**
     * Check if current entity allowed to access document
     *
     * @return bool
     */
    public function isOwner()
    {
        // Get rio id of currently logged-in user
        /** @var User */
        $user = auth()->user();

        // Service selection session state values
        $service = json_decode(Session::get('ServiceSelected'));

        // Check if NEO owner or admininstrator is the current user logged-in
        if ($service->type === ServiceSelectionTypes::RIO) {
            if ($this->owner_rio_id === $user->rio_id) {
                return true;
            }
        }

        // Check if NEO owner or admininstrator is the current user logged-in
        if ($service->type === ServiceSelectionTypes::NEO) {
            if ($this->owner_neo_id === $service->data->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has access to specified document entity
     * Define relationship with document model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function directory()
    {
        return $this->hasOne(self::class, 'directory_id')
            ->with('directory');
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($document) {
            // Soft delete associated documents
            self::whereDirectoryId($document->id)->get()
                ->each(function ($directory) {
                    $directory->delete();
                });

            // Soft delete associated document accesses data
            $document->document_accesses()->each(function ($access) {
                $access->delete();
            });
        });
    }

    /**
     * Check if service is the document owner
     *
     * @param   mixed $service
     * @return bool
     */
    public function isDocumentOwner($service)
    {
        // Check if document owner is RIO by service selected
        if ($service->type == ServiceSelectionTypes::RIO) {
            return ($this->owner_rio_id === $service->data->id);
        }

        return ($this->owner_neo_id === $service->data->id);
    }

    /**
     * Check if service is a NEO owner/admin
     *
     * @param   mixed $service
     * @return bool
     */
    public function isAuthorizedNeoUser($service)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        if ($service->type === ServiceSelectionTypes::NEO) {
            return NeoBelong::whereRioId($user->rio_id)
                ->whereNeoId($this->owner_neo_id)
                ->whereIn('role', [
                    RoleType::OWNER,
                    RoleType::ADMINISTRATOR,
                ])
                ->exists();
        }

        return false;
    }

    /**
     * Check if service is a NEO owner/admin
     *
     * @param   mixed $service
     * @return bool
     */
    public function isNeoBelong($service)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        if ($service->type === ServiceSelectionTypes::NEO) {
            return NeoBelong::whereRioId($user->rio_id)
                ->whereNeoId($this->owner_neo_id)
                ->whereIn('role', [
                    RoleType::OWNER,
                    RoleType::ADMINISTRATOR,
                    RoleType::MEMBER,
                ])
                ->exists();
        }

        return false;
    }

    /**
     * Check if user allowed to create new document
     *
     * @return bool
     */
    public function isAllowedInDocumentManagement()
    {
        // Get rio id of currently logged-in user
        /** @var User */
        $user = auth()->user();

        // Service selection session state values
        $service = json_decode(Session::get('ServiceSelected'));

        // Guard clause for non-existing rio
        if (empty($user->rio) || empty($service)) {
            return false;
        }

        // Check if NEO owner or admininstrator is the current user logged-in
        if ($service->type === ServiceSelectionTypes::NEO) {
            $neo = $service->data;
            if ($neo->is_member) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if current entity allowed to access document
     *
     * @return bool
     */
    public function isAllowedDocumentAccess()
    {
        // Get rio id of currently logged-in user
        /** @var User */
        $user = auth()->user();

        // Service selection session state values
        $service = json_decode(Session::get('ServiceSelected'));

        // Guard clause for non-existing rio
        if (empty($user->rio) || empty($service)) {
            return false;
        }

        // Get all parent documents of given doccument id
        $parentDocuments = $this->getParentDocuments()->pluck('id')->toArray();
        array_push($parentDocuments, $this->id);

        /** @var DocumentAccess */
        $isAccessible = DocumentAccess::whereIn('document_id', $parentDocuments);

        // Check if NEO owner or admininstrator is the current user logged-in
        if ($service->type === ServiceSelectionTypes::RIO) {
            if ($this->owner_rio_id === $user->rio_id) {
                return true;
            }

            $neoGroups = NeoGroupUser::whereRioId($user->rio_id)->pluck('neo_group_id');
            $checkNeoGroup = $this->document_accesses()->whereIn('neo_group_id', $neoGroups)->exists();
            if ($checkNeoGroup) {
                return true;
            }

            $isAccessible = $isAccessible->whereRioId($user->rio_id);
        }

        // Check if NEO owner or admininstrator is the current user logged-in
        if ($service->type === ServiceSelectionTypes::NEO) {
            if ($this->owner_neo_id === $service->data->id) {
                return true;
            }

            $isAccessible = $isAccessible->whereNeoId($service->data->id);
        }

        return $isAccessible->exists();
    }

    /**
     * Check if user has access to specified document entity
     * Check if service has access to specified document entity
     *
     * @param   mixed $service
     * @return bool
     */
    public function isUserAccessible($service)
    {
        // Check if document is RIO accessible by service selected
        if ($service->type == ServiceSelectionTypes::RIO) {
            return $this->document_accesses()
                ->whereRioId($service->data->id)
                ->exists();
        }

        return $this->document_accesses()
            ->whereNeoId($service->data->id)
            ->exists();
    }

    /**
     * Check if document file is viewable.
     *
     * @param   \App\Models\Document $document
     * @param   mixed $service
     *
     * @return bool
     */
    public function isFileViewable($document, $service)
    {
        //Return value
        $isViewable = true;
        $isSharedFromFolder = true;

        //Determine if current viewer is RIO or NEO
        $isUserRio = ($service->type === ServiceSelectionTypes::RIO) ? true : false;

        //Determine if the viewer is also the file owner
        $isOwner = $this->whereId($document->id)
            ->when($isUserRio, function ($q) use ($service) { // true
                return $q->whereOwnerRioId($service->data->id);
            }, function ($q) use ($service) { // false
                return $q->whereOwnerNeoId($service->data->id);
            })
            ->exists();

        // If user is not the file owner, check if current file for view is shared to a viewer
        if (!$isOwner) {
            $isViewable = $this->document_accesses()
                ->whereDocumentId($document->id)
                ->when($isUserRio, function ($q) use ($service) { // true
                    return $q->whereRioId($service->data->id);
                }, function ($q) use ($service) { // false
                    return $q->whereNeoId($service->data->id);
                })
                ->exists();
            $isSharedFromFolder = $this->verifySharedSubDirectory($isUserRio, $service, $document->directory_id);
        }

        return ($isViewable || $isSharedFromFolder);
    }

    /**
     * Check if user allowed to create new document
     *
     * @return bool
     */
    public function isAllowCreateToDocument()
    {
        // Get rio id of currently logged-in user
        /** @var User */
        $user = auth()->user();

        // Service selection session state values
        $service = json_decode(Session::get('ServiceSelected'));

        // Guard clause for non-existing rio
        if (empty($user->rio) || empty($service)) {
            return false;
        }

        // Check if RIO is the current user logged-in
        if ($service->type === ServiceSelectionTypes::RIO) {
            if (isset($this->directory_id)) {
                /** @var \App\Models\Document */
                $parentDocument = self::find($this->directory_id);
                if ($parentDocument->owner_rio_id !== $user->rio_id) {
                    return false;
                }
            }
        }

        // Check if NEO owner or admininstrator is the current user logged-in
        if ($service->type === ServiceSelectionTypes::NEO) {
            $neo = $service->data;
            if ($neo->is_member) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if directory is accessible to a user
     *
     * @param \App\Models\Document $document
     * @param mixed $service
     *
     * @return bool
     */
    public function isDirectoryOwner($document, $service)
    {
        // uploader is uploading to root directory
        if (empty($document->directory_id) || !isset($document->directory_id)) {
            return true;
        }

        //Determine if current uploader is RIO or NEO
        $isUserRio = ($service->type === ServiceSelectionTypes::RIO) ? true : false;

        //Determine if the uploader owns the directory
        $isOwner = $this->whereId($document->directory_id)
            ->when($isUserRio, function ($q) use ($service) { // true
                return $q->whereOwnerRioId($service->data->id);
            }, function ($q) use ($service) { // false
                return $q->whereOwnerNeoId($service->data->id);
            })
            ->exists();

        return $isOwner;
    }

    /**
     * Check if owner record exist in document_accesses if not, create new
     *
     *
     * @return void
     */
    public function checkOwnerDocumentAccess()
    {
        /** @var User */
        $user = auth()->user();

        /** @var DocumentAccess */
        $ownerDocumentAccess = DocumentAccess::whereDocumentId($this->id);

        // Service selection session state values
        $service = json_decode(Session::get('ServiceSelected'));

        $documentAccess = new DocumentAccess();
        $documentAccess->document_id = $this->id;

        // Check if RIO is the current service selection
        if ($service->type === ServiceSelectionTypes::RIO) {
            $ownerDocumentAccess = $ownerDocumentAccess->whereRioId($user->rio_id);
            $documentAccess->rio_id = $user->rio_id;
        }

        // Check if NEO is the current service selection
        if ($service->type === ServiceSelectionTypes::NEO) {
            $ownerDocumentAccess = $ownerDocumentAccess->whereNeoId($service->data->id);
            $documentAccess->neo_id = $service->data->id;
        }

        if (!$ownerDocumentAccess->exists()) {
            $documentAccess->save();
        }
    }

    /**
     * Scope a query to get files from either NEO or RIO user.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Document> $query
     * @param mixed  $service
     * @param array  $options
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFileList($query, $service, $options)
    {
        $searchKey = isset($options['search']) && !is_null($options['search']);
        $isQueryForShared = $options['for_shared'] ?? false;
        $inFolder = isset($options['directory_id']) && !is_null($options['directory_id']);
        $currentId = $options['owner_rio_id']
            ?? $options['owner_neo_id']
            ?? $service->data->id;
        $data = array_merge($options, [
            'document_type' => DocumentTypes::FILE,
        ]);

        return $query->documentList($service, $data);
    }

    /**
     * Scope a query to get folders from either NEO or RIO user.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Document> $query
     * @param mixed  $service
     * @param array  $options
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFolderList($query, $service, $options)
    {
        $data = array_merge($options, [
            'document_type' => DocumentTypes::FOLDER,
        ]);

        return $query->documentList($service, $data);
    }

    /**
     * Scope a query to get attachment from either NEO or RIO user.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Document> $query
     * @param mixed  $service
     * @param array  $options
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAttachmentList($query, $service, $options)
    {
        $data = array_merge($options, [
            'document_type' => DocumentTypes::ATTACHMENT,
        ]);

        return $query->documentList($service, $data);
    }

    /**
     * Scope a query to get folders from either NEO or RIO user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed  $service
     * @param array  $options
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDocumentList($query, $service, $options)
    {
        // Initialize variables
        $neoGroupsJoined = [];
        $documentType = $options['document_type'] ?? DocumentTypes::FILE;
        $searchKey = isset($options['search']) && !is_null($options['search']);
        $isQueryForShared = $options['for_shared'] ?? false;
        $inFolder = isset($options['directory_id']) && !is_null($options['directory_id']);
        $currentId = $options['owner_rio_id']
            ?? $options['owner_neo_id']
            ?? $service->data->id;
        $isNeoEntity = ($service->type === ServiceSelectionTypes::NEO);
        if (!empty($options['owner_rio_id'])) {
            $isNeoEntity = false;
        }
        if (!empty($options['owner_neo_id'])) {
            $isNeoEntity = true;
        }
        //Get all shared files from NEO groups
        if ($isQueryForShared && !$isNeoEntity) {
            $neoGroupsJoined = NeoGroupUser::whereRioId($service->data->id)
                ->whereNull('neo_group_users.deleted_at')
                ->pluck('neo_group_users.neo_group_id')
                ->all();
        }

        $targetEntityColumn = $isNeoEntity
            ? 'neo_id'
            : 'rio_id';

        // Get documents being shared to other users
        $folderSharedQuery = Document::query()
            ->select(
                'documents.id as documentId',
                'documents.owner_rio_id as ownerRioId',
                'documents.owner_neo_id as ownerNeoId',
                'documents.directory_id as fileDirectoryId',
                'documents.document_type as documentType',
                'documents.document_name as name',
                'documents.storage_path as filePath',
                'documents.mime_type as fileType',
            )
            ->join('document_accesses', 'documents.id', '=', 'document_accesses.document_id')
            ->where('document_accesses.' . $targetEntityColumn, $currentId)
            ->where('documents.document_type', $documentType)
            ->whereNull('document_accesses.deleted_at')
            ->when($searchKey, function ($subQuery) use ($options) {
                return $subQuery->having('name', 'LIKE', "%{$options['search']}%");
            });

        // Get shared attachments and files/folders from joined Neo Groups of RIO
        $neoGroupSharedQuery = Document::query()
            ->select(
                'documents.id as documentId',
                'documents.owner_rio_id as ownerRioId',
                'documents.owner_neo_id as ownerNeoId',
                'documents.directory_id as fileDirectoryId',
                'documents.document_type as documentType',
                'documents.document_name as name',
                'documents.storage_path as filePath',
                'documents.mime_type as fileType',
            )
            ->join('document_accesses', 'documents.id', '=', 'document_accesses.document_id')
            ->where('documents.document_type', $documentType)
            ->whereNull('document_accesses.deleted_at')
            ->when(!$isNeoEntity, function ($subQuery) use ($neoGroupsJoined) {
                return $subQuery->whereIn('document_accesses.neo_group_id', $neoGroupsJoined);
            })
            ->when($searchKey, function ($subQuery) use ($options) {
                return $subQuery->having('name', 'LIKE', "%{$options['search']}%");
            });

        // Get documents
        return $query
            ->select(
                'documents.id as documentId',
                'documents.owner_rio_id as ownerRioId',
                'documents.owner_neo_id as ownerNeoId',
                'documents.directory_id as fileDirectoryId',
                'documents.document_type as documentType',
                'documents.document_name as name',
                'documents.storage_path as filePath',
                'documents.mime_type as fileType',
            )
            ->where('documents.owner_' . $targetEntityColumn, $currentId)
            ->where('documents.document_type', $documentType)
            ->when($searchKey, function ($subQuery) use ($options) {
                return $subQuery->having('name', 'LIKE', "%{$options['search']}%");
            })

            // Conditional logic for fetching shared or personal folders
            ->when($isQueryForShared, function ($subQuery) use ($folderSharedQuery, $neoGroupSharedQuery, $isNeoEntity) {
                // Fetch shared folders
                return $subQuery->join('document_accesses', 'documents.id', '=', 'document_accesses.document_id')
                    ->union($folderSharedQuery)
                    ->when(!$isNeoEntity, function ($subQuery) use ($neoGroupSharedQuery) {
                        return $subQuery->union($neoGroupSharedQuery);
                    });
            }, function ($subQuery) use ($targetEntityColumn, $currentId) {
                // Fetch personal folders
                return $subQuery->where('documents.owner_' . $targetEntityColumn, $currentId);
            })

            // Conditional logic for fetching items in root or sub directory
            ->when($inFolder, function ($subQuery) use ($options) { // true
                // Fetch items in sub directory
                return $subQuery->where('documents.directory_id', $options['directory_id']);
            }, function ($subQuery) { // false
                // Fetch items in root directory
                return $subQuery->whereNull('documents.directory_id');
            });
    }


    /**
     * Scope a query to get documents inside share_type folder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int  $id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFolderDocuments($query, int $id)
    {
        return $query->where('directory_id', $id);
    }

    /**
     * Check if user has access to specified document entity
     *
     * @param int $id
     * @param mixed  $service
     * @return bool
     */
    public function isUserAllowed($id, $service)
    {
        $isAllowed = false;
        /** @var Document */
        $document = Document::whereId($id)
            ->first();

        //check if user belongs to neo group chat
        if ($service->type === ServiceSelectionTypes::RIO) {
            $isAllowed = NeoGroupUser::where('neo_group_id', function ($query) use ($document) {
                $query->select('neo_group_id')
                    ->from('document_accesses')
                    ->where('document_id', $document->id)
                    ->whereNotNull('neo_group_id');
            })->where('rio_id', $service->data->id)->exists();

            if ($isAllowed) {
                return true;
            }
        }

        // Check if file/folder has share access record
        switch ($service->type) {
            case (ServiceSelectionTypes::NEO):
                $isAllowed = DB::table('document_accesses')
                    ->where('neo_id', $service->data->id)
                    ->where('document_id', $document->id)
                    ->whereNull('deleted_at')
                    ->exists();
                break;
            case (ServiceSelectionTypes::RIO):
                $isAllowed = DB::table('document_accesses')
                    ->where('rio_id', $service->data->id)
                    ->where('document_id', $document->id)
                    ->whereNull('deleted_at')
                    ->exists();
                break;
            default:
                break;
        }

        // Check if upper directories of file/folder has share access record
        if (!empty($document->directory_id) && !$isAllowed) {
            switch ($service->type) {
                case (ServiceSelectionTypes::NEO):
                    $isAllowed = DB::table('document_accesses')
                        ->where('neo_id', $service->data->id)
                        ->where('document_id', $document->directory_id)
                        ->whereNull('deleted_at')
                        ->exists();
                    break;
                case (ServiceSelectionTypes::RIO):
                    $isAllowed = DB::table('document_accesses')
                        ->where('rio_id', $service->data->id)
                        ->where('document_id', $document->directory_id)
                        ->whereNull('deleted_at')
                        ->exists();
                    break;
                default:
                    break;
            }

            return $isAllowed ? $isAllowed : $this->isUserAllowed($document->directory_id, $service);
        }

        return $isAllowed;
    }

    /**
     * Check if entity is a directory
     *
     * @return bool
     */
    public function isFolder()
    {
        return $this->document_type === DocumentTypes::FOLDER;
    }

    /**
     * Create documents for chat service attachments
     *
     * @param \App\Models\Chat $chat
     * @param \App\Models\ChatParticipant $owner
     * @param array $codes
     * @return array
     */
    public static function createAttachments(Chat $chat, ChatParticipant $owner, $codes)
    {
        // Initialize necessary values
        $fileAttachments = [];
        $documentAccesses = [];

        $service = ServiceSelected::getSelected();

        // Get storage info in service settings table
        $serviceSetting = ServiceSetting::serviceSettingInfo($service)->firstOrFail();
        $storageInfo = $serviceSetting->storage_info;

        // Prepare document accesses
        switch ($chat->chat_type) {
            case ChatTypes::NEO_GROUP:
                $neoGroup = NeoGroup::whereChatId($chat->id)->first();

                // Set NEO group id for accesses
                if (!empty($neoGroup)) {
                    $documentAccesses = [
                        [
                            'neo_id' => null,
                            'rio_id' => null,
                            'neo_group_id' => $neoGroup->id,
                        ]
                    ];
                }
                break;
            default:
                // Set accesses based upon chat participants
                $participants = $chat->participants()->get();

                $documentAccesses = $participants
                    ->transform(function ($entity) {
                        // Initialize default values
                        $data = [
                            'neo_id' => null,
                            'rio_id' => null,
                            'neo_group_id' => null,
                        ];

                        // Set id depending on entity id
                        if (!empty($entity->neo_id)) {
                            $data['neo_id'] = $entity->neo_id;
                        } else {
                            $data['rio_id'] = $entity->rio_id;
                        }

                        return $data;
                    })
                    ->all();
                break;
        }

        foreach ($codes ?? [] as $code) {
            try {
                // Initialize filepond file
                $filepond = new FilepondFile($code, true);

                // Get temporary file name
                $uploadFilename = $filepond->getFileName();

                // Handle non-existing temp upload file
                if (empty($uploadFilename)) {
                    continue;
                }

                // Instantiate document model
                $documentFile = new Document();

                // Initialize document defaults
                $documentFile->fill([
                    'directory_id' => null,
                    'document_type' => DocumentTypes::ATTACHMENT,
                    'storage_type_id' => StorageTypes::HERO,
                    'document_name' => $uploadFilename,
                ]);

                // Set owner and target directory based on user type
                $targetDirectory = null;
                if ($owner->isNeo()) {
                    $documentFile->owner_neo_id = $owner->neo_id;
                    $targetDirectory = config('bphero.neo_document_storage_path') . $owner->neo_id;
                } else {
                    $documentFile->owner_rio_id = $owner->rio_id;
                    $targetDirectory = config('bphero.rio_document_storage_path') . $owner->rio_id;
                }

                if ($documentFile->save()) {
                    // Generate filename
                    $targetFilename = $documentFile->id . '_' . $uploadFilename;

                    // Transfer temporary file to permanent directory
                    $fileinfo = $filepond->transferFile($targetDirectory, $targetFilename, false);

                    // Update storage path
                    $documentFile->update([
                        'mime_type' => $fileinfo['mime_type'],
                        'file_bytes' => $fileinfo['file_size'],
                        'storage_path' => config('bphero.private_directory') . '/'
                            . $targetDirectory . '/'
                            . $targetFilename,
                    ]);

                    // Prepare new storage info
                    $available = $storageInfo['available'] - $fileinfo['file_size'];
                    $storageInfo['used'] = $storageInfo['used'] + $fileinfo['file_size'];
                    $storageInfo['available'] = $available < 0 ? 0 : $available;

                    // Set document accesses
                    if (!empty($documentAccesses)) {
                        $documentFile
                            ->document_accesses()
                            ->createMany($documentAccesses);
                    }

                    $fileAttachments[] = $documentFile->id;
                }
            } catch (\Exception $exception) {
                report($exception);
                continue;
            }
        }

        // Save updated storage info
        $serviceSetting->data = json_encode($storageInfo) ?: null;
        $serviceSetting->save();

        return $fileAttachments;
    }

    /**
     * Create documents for workflow service attachments
     *
     * @param array $codes
     * @return array
     */
    public static function createWorkflowAttachments($codes)
    {
        // Initialize variables
        /** @var \App\Models\User */
        $user = auth()->user();
        // Get selected service
        $service = ServiceSelected::getSelected();

        // Initialize necessary values
        $fileAttachments = [];
        $documentAccesses = [];

        // Get storage info in service settings table
        $serviceSetting = ServiceSetting::serviceSettingInfo($service)->firstOrFail();
        $storageInfo = $serviceSetting->storage_info;

        // Set NEO id for accesses
        if ($service->type === ServiceSelectionTypes::NEO) {
            $documentAccesses = [
                [
                    'neo_id' => $service->data->id,
                    'rio_id' => null,
                    'neo_group_id' => null
                ]
            ];
        }

        foreach ($codes ?? [] as $code) {
            try {
                // Initialize filepond file
                $filepond = new FilepondFile($code, true);

                // Get temporary file name
                $uploadFilename = $filepond->getFileName();

                // Handle non-existing temp upload file
                if (empty($uploadFilename)) {
                    continue;
                }

                // Instantiate document model
                $documentFile = new Document();

                // Initialize document defaults
                $documentFile->fill([
                    'directory_id' => null,
                    'document_type' => DocumentTypes::ATTACHMENT,
                    'storage_type_id' => StorageTypes::HERO,
                    'document_name' => $uploadFilename,
                ]);

                // Set owner and target directory based on user type
                $targetDirectory = null;
                if ($service->type === ServiceSelectionTypes::NEO) {
                    $documentFile->owner_neo_id = $service->data->id;
                    $targetDirectory = config('bphero.neo_document_storage_path') . $service->data->id;
                } else {
                    $documentFile->owner_rio_id = $user->rio_id;
                    $targetDirectory = config('bphero.rio_document_storage_path') . $user->rio_id;
                }

                if ($documentFile->save()) {
                    // Generate filename
                    $targetFilename = $documentFile->id . '_' . $uploadFilename;

                    // Transfer temporary file to permanent directory
                    $fileinfo = $filepond->transferFile($targetDirectory, $targetFilename, false);

                    // Update storage path
                    $documentFile->update([
                        'mime_type' => $fileinfo['mime_type'],
                        'file_bytes' => $fileinfo['file_size'],
                        'storage_path' => config('bphero.private_directory') . '/'
                            . $targetDirectory . '/'
                            . $targetFilename,
                    ]);

                    // Prepare new storage info
                    $available = $storageInfo['available'] - $fileinfo['file_size'];
                    $storageInfo['used'] = $storageInfo['used'] + $fileinfo['file_size'];
                    $storageInfo['available'] = $available < 0 ? 0 : $available;

                    // Set document accesses
                    if (!empty($documentAccesses)) {
                        $documentFile
                            ->document_accesses()
                            ->createMany($documentAccesses);
                    }

                    $fileAttachments[] = $documentFile->id;
                }
            } catch (\Exception $exception) {
                report($exception);
                continue;
            }
        }

        // Save updated storage info
        $serviceSetting->data = json_encode($storageInfo) ?: null;
        $serviceSetting->save();

        return $fileAttachments;
    }

    /**
     * Checks if the subfolder's parent folder is shared to the accessor
     *
     * @param bool $isUserRio
     * @param object $service
     * @param int|null $directoryId
     *
     * @return bool
     */
    private function verifySharedSubDirectory($isUserRio, $service, $directoryId = null)
    {
        $isShared = Document::join('document_accesses', 'documents.id', '=', 'document_accesses.document_id')
            ->where('document_accesses.document_id', $directoryId)
            ->when($isUserRio, function ($q) use ($service) { // true
                return $q->where('document_accesses.rio_id', $service->data->id);
            }, function ($q) use ($service) { // false
                return $q->where('document_accesses.neo_id', $service->data->id);
            })
            ->exists();

        if (!$isShared) {
            if (!is_null($directoryId)) {
                /** @var Document */
                $folderDetails = Document::where('documents.id', $directoryId)->first();

                return $this->verifySharedSubDirectory($isUserRio, $service, $folderDetails->directory_id);
            }

            return false;
        }

        return true;
    }

    /**
     * Checks if directory is empty
     *
     * @param int|null $directoryId
     *
     * @return bool
     */
    public static function isEmptyDirectory($directoryId = null)
    {
        $hasFile = Document::whereDirectoryId($directoryId)
            ->whereDocumentType(DocumentTypes::FILE)
            ->exists();

        if ($hasFile) {
            return false;
        }

        $directories = Document::whereDirectoryId($directoryId)
            ->whereDocumentType(DocumentTypes::FOLDER)
            ->get();

        foreach ($directories as $directory) {
            if (!Document::isEmptyDirectory($directory->id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Fetch total storage space used
     *
     * @param \App\Models\Rio|\App\Models\Neo $entity
     *
     * @return string|int
     */
    public static function totalStorageUsed($entity)
    {
        // Identify entity type
        $entityType = CommonHelper::getEntityType($entity);

        return self::when($entityType === EntityType::RIO, function ($query) use ($entity) {
            return $query->where('documents.owner_rio_id', $entity->id);
        }, function ($query) use ($entity) {
            return $query->where('documents.owner_neo_id', $entity->id);
        })
            ->sum('file_bytes');
    }
}
