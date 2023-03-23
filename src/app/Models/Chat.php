<?php

namespace App\Models;

use App\Enums\Chat\ChatStatuses;
use App\Enums\Chat\ChatTypes;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Chat\ConnectedChatType;
use App\Enums\ServiceSelectionTypes;
use App\Objects\TalkSubject;
use DB;
use Session;

/**
 * App\Models\Chat
 *
 * @property int $id id for Laravel
 * @property int|null $owner_rio_id ↓どちらかのみセット
 * @property int|null $owner_neo_id ↑どちらかのみセット
 * @property int $created_rio_id
 * @property int $chat_type 1:つながりチャット、2:つながりグループチャット、3:NEOチームチャット、4:NEOメッセージ配信
 * @property string $room_name
 * @property string|null $room_icon
 * @property string|null $room_caption
 * @property string $status active, archive
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Rio $created_rio
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatMessage[] $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\Neo|null $owner_neo
 * @property-read \App\Models\Rio|null $owner_rio
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatParticipant[] $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder|Chat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat newQuery()
 * @method static \Illuminate\Database\Query\Builder|Chat onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereChatType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereOwnerNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereOwnerRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereRoomCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereRoomIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereRoomName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Chat withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Chat withoutTrashed()
 * @mixin \Eloquent
 */
class Chat extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_rio_id',
        'owner_neo_id',
        'created_rio_id',
        'chat_type',
        'room_name',
        'room_icon',
        'room_caption',
        'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'chat_image',
    ];

    /**
     * Define relationship with Rio Owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner_rio()
    {
        return $this->belongsTo(Rio::class, 'owner_rio_id');
    }

    /**
     * Define relationship with Neo Owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner_neo()
    {
        return $this->belongsTo(Neo::class, 'owner_neo_id');
    }

    /**
     * Define relationship with Group connection
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(GroupConnection::class, 'chat_id', 'id');
    }

    /**
     * Define relationship with Created Rio
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_rio()
    {
        return $this->belongsTo(Rio::class, 'created_rio_id');
    }

    /**
     * Define relationship with chat_messages table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Define scop with latest message sent table
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return mixed
     */
    public function scopeLastMessage($query)
    {
        return $query->with('messages')->latest();
    }

    /**
     * Define relationship with chat_participants table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    /**
     * Define relationship with chat_participants table where rio_id is not null
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rioParticipants()
    {
        return $this->hasMany(ChatParticipant::class)
            ->where('rio_id', '!=', null);
    }

    /**
     * Define relationship with chat_participants table where neo_id is not null
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function neoParticipants()
    {
        return $this->hasMany(ChatParticipant::class)
            ->where('neo_id', '!=', null);
    }

    /**
     * Check if user has access to specified chat entity
     *
     * @return bool
     */
    public function isChatParticipant()
    {
        // Get selected talk subject
        $talkSubject = TalkSubject::getSelected();

        // Check if authenticated user is a participant
        switch ($talkSubject->type) {
            case ServiceSelectionTypes::RIO:
                return $this->rioParticipants()
                    ->whereRioId($talkSubject->data->id)
                    ->exists();

            case ServiceSelectionTypes::NEO:
                return $this->neoParticipants()
                    ->whereNeoId($talkSubject->data->id)
                    ->exists();
        }

        return false;
    }

    /**
     * Create document accesses if attachment is sent in chat service
     *
     * @param int $document_id
     *
     * @return void
     */
    public function createDocumentAccesses($document_id)
    {
        // Initialize necessary values
        $documentAccesses = [];

        $document = Document::find($document_id);

        if ($document) {
            // Prepare document accesses
            switch ($this->chat_type) {
                case ChatTypes::NEO_GROUP:
                    $neoGroup = NeoGroup::whereChatId($this->id)->first();

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
                    $participants = $this->participants()->get();

                    $documentAccesses = $participants
                        ->transform(function ($entity) use ($document) {
                            // Initialize default values
                            $data = [
                                'neo_id' => null,
                                'rio_id' => null,
                                'neo_group_id' => null,
                            ];

                            $checkDocumentAccess = DocumentAccess::where('document_id', $document->id);

                            // Set id depending on entity id
                            if (!empty($entity->neo_id)) {
                                $data['neo_id'] = $entity->neo_id;
                                $checkDocumentAccess = $checkDocumentAccess->where('neo_id', $entity->neo_id);
                            } else {
                                $data['rio_id'] = $entity->rio_id;
                                $checkDocumentAccess = $checkDocumentAccess->where('rio_id', $entity->rio_id);
                            }

                            if ($checkDocumentAccess->exists()) {
                                return [];
                            }

                            return $data;
                        })
                        ->all();
                    break;
            }

            $filteredDocumentAccesses = array_filter($documentAccesses);

            if (!empty($filteredDocumentAccesses)) {
                $document->document_accesses()->createMany($filteredDocumentAccesses);
            }
        }
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($chat) {
            // Soft-delete associated chat messages
            $chat->messages()
                ->each(function ($message) {
                    $message->delete();
                });

            // Soft-delete associated chat participants
            $chat->participants()
                ->each(function ($participant) {
                    $participant->delete();
                });
        });
    }

    /**
     * Get the chat image attribute
     *
     * @return string
     */
    public function getChatImageAttribute()
    {
        $profilePhoto = is_null($this->room_icon) ? config('bphero.profile_image_filename') : $this->room_icon;

        return asset(config('bphero.profile_image_directory') . $profilePhoto);
    }

    /**
     * Creation of chat and chat participants for connected chat typee
     *
     * @param object $connection
     * @param int $connectedChatType
     *
     * @return void
     */
    public static function createConnectedChat($connection, $connectedChatType)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $chat = new self();
        $chat->chat_type = ChatTypes::CONNECTED;
        $chat->status = ChatStatuses::ACTIVE;

        $participants = [];

        switch ($connectedChatType) {
            case ConnectedChatType::RIO_TO_NEO:
                $chat->owner_rio_id = $connection->connection_rio_id;
                $chat->created_rio_id = $connection->connection_rio_id;
                $chat->save();

                $participants = [
                    [
                        "chat_id" => $chat->id,
                        "rio_id" => $connection->connection_rio_id,
                        "neo_id" => null,
                    ],
                    [
                        "chat_id" => $chat->id,
                        "rio_id" => null,
                        "neo_id" => $connection->neo_id
                    ]
                ];
                break;

            case ConnectedChatType::NEO_TO_NEO:
                $chat->owner_neo_id = $connection->connection_neo_id;
                $chat->created_rio_id = $user->rio_id;
                $chat->save();

                $participants = [
                    [
                        "chat_id" => $chat->id,
                        "neo_id" => $connection->neo_id
                    ],
                    [
                        "chat_id" => $chat->id,
                        "neo_id" => $connection->connection_neo_id
                    ]
                ];
                break;

            default:
                $chat->owner_rio_id = $connection->created_rio_id;
                $chat->created_rio_id = $connection->created_rio_id;
                $chat->save();

                $rioConnectionUser = $connection->rio_connection_users()->exceptRio($connection->created_rio_id);

                $participants = [
                    [
                        "chat_id" => $chat->id,
                        "rio_id" => $chat->owner_rio_id
                    ],
                    [
                        "chat_id" => $chat->id,
                        "rio_id" => $rioConnectionUser->rio_id
                    ]
                ];
                break;
        }

        ChatParticipant::insert($participants);
    }

    /**
     * Create connected group chat room
     *
     * @param \App\Models\Rio $rio
     * @param string $roomName
     * @return \App\Models\Chat
     */
    public static function createConnectedGroupChat(Rio $rio, $roomName)
    {
        // Fetch profile image of owner
        $roomIcon = $rio
            ->rio_profile
            ->profile_image ?? null;

        // Create connected group chat room
        return self::create([
            'owner_rio_id' => $rio->id,
            'owner_neo_id' => null,
            'created_rio_id' => $rio->id,
            'chat_type' => ChatTypes::CONNECTED_GROUP,
            'room_name' => $roomName,
            'room_icon' => $roomIcon,
            'room_caption' => null,
            'status' => ChatStatuses::ACTIVE,
        ]);
    }

    /**
     * Add chat room participant
     *
     * @param \App\Models\Rio|\App\Models\Neo $entity
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addParticipant($entity)
    {
        $participantData = [];

        // Set participant as rio
        if ($entity instanceof Rio) {
            $participantData['rio_id'] = $entity->id;
        }

        // Set participant as neo
        if ($entity instanceof Neo) {
            $participantData['neo_id'] = $entity->id;
        }

        // Check for existing participant
        $existingParticipant = $this->participants()
            ->where($participantData)
            ->first();

        // Return existing
        if (!empty($existingParticipant)) {
            return $existingParticipant;
        }

        // Add participant to chat room
        return $this->participants()->create($participantData);
    }

    /**
     * Remove chat room participant
     *
     * @param \App\Models\Rio|\App\Models\Neo $entity
     * @return mixed
     */
    public function removeParticipant($entity)
    {
        $query = $this->participants();

        // Query participant as rio
        if ($entity instanceof Rio) {
            $query->where('rio_id', $entity->id);
        }

        // Query participant as neo
        if ($entity instanceof Neo) {
            $query->where('neo_id', $entity->id);
        }

        // Get participant
        $participant = $query->first();

        // Disregard if non-existing participant
        if (empty($participant)) {
            return false;
        }

        return $participant->delete();
    }

    /**
     * Scope a query of rios connected to specified rio
     *
     * @param \Illuminate\Database\Eloquent\Builder<Chat> $query
     * @param object $session Talk subject session
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeRoomList($query, $session)
    {
        $entity = $session->data;

        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.neo_profile_image');

        /**
         * Subquery for chat participants records of current session
         *
         * Output columns:
         * `id`
         * `chat_id`
         */
        $participatingQuery = DB::table('chat_participants as subject_participants')
            ->select(
                'subject_participants.id',
                'subject_participants.chat_id',
            )
            // Get unread messages count for chat room
            ->selectRaw('
                (CASE
                    WHEN subject_participants.last_read_chat_message_id IS NOT NULL
                        THEN (
                            SELECT COUNT(unread_messages.id) as count
                                FROM chat_messages AS unread_messages
                                WHERE subject_participants.last_read_chat_message_id < unread_messages.id
                                    AND subject_participants.chat_id = unread_messages.chat_id
                                    AND unread_messages.deleted_at IS NULL
                                GROUP BY unread_messages.chat_id
                        )
                    ELSE (
                        SELECT COUNT(unread_messages.id) as count
                            FROM chat_messages AS unread_messages
                            WHERE subject_participants.chat_id = unread_messages.chat_id
                                AND unread_messages.deleted_at IS NULL
                            GROUP BY unread_messages.chat_id
                    )
                END) AS unread_messages_count
            ')
            ->whereNull('subject_participants.deleted_at');

        // Appropriate condition for session type
        if ($session->type == ServiceSelectionTypes::NEO) {
            $participatingQuery->where('subject_participants.neo_id', $entity->id);
        } else {
            $participatingQuery->where('subject_participants.rio_id', $entity->id);
        }

        /**
         * Subquery for connected participant to session
         *
         * Fetches chat participants where chat type is connected
         * and where the entity is not the current session
         *
         * Output columns:
         * `id` - Max value since we only expect one record but handles invalid data also
         * `chat_id`
         */
        $connectedParticipantsQuery = DB::table('chat_participants as connected_participants')
            ->select([
                DB::raw('max(connected_participants.id) as id'),
                'connected_participants.chat_id',
            ])
            ->leftJoin('chats', 'chats.id', '=', 'connected_participants.chat_id')
            ->where('chats.chat_type', ChatTypes::CONNECTED)
            ->whereNull('connected_participants.deleted_at')
            ->groupBy('connected_participants.chat_id');

        // Appropriate condition for session type
        if ($session->type === ServiceSelectionTypes::NEO) {
            $connectedParticipantsQuery->where(function ($query) use (&$entity) {
                $query->where('connected_participants.neo_id', '<>', $entity->id)
                    ->orWhereNull('connected_participants.neo_id');
            });
        } else {
            $connectedParticipantsQuery->where(function ($query) use (&$entity) {
                $query->where('connected_participants.rio_id', '<>', $entity->id)
                    ->orWhereNull('connected_participants.rio_id');
            });
        }

        /**
         * Subquery to fetch connected entity details such as profile image and entity name
         *
         * Output columns:
         * `chat_id`
         * `name`
         * `icon`
         */
        $connectedParticipantsDetailsQuery = DB::table('chat_participants as connected_participant_details')
            ->select([
                'connected_participants.chat_id',
            ])
            // Get chat room name for connected type
            ->selectRaw('
                (CASE
                    WHEN connected_participant_details.rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    WHEN connected_participant_details.neo_id IS NOT NULL
                        THEN neos.organization_name
                    ELSE NULL
                END) AS name
            ')
            // Get chat room icon for connected type
            ->selectRaw("
                (CASE
                    WHEN connected_participant_details.rio_id IS NOT NULL
                        THEN (
                            SELECT
                                CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                                FROM rio_profiles
                                WHERE rio_profiles.rio_id = connected_participant_details.rio_id
                                LIMIT 1
                        )
                    WHEN connected_participant_details.neo_id IS NOT NULL
                        THEN (
                            SELECT
                                CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                                FROM neo_profiles
                                WHERE neo_profiles.neo_id = connected_participant_details.neo_id
                                LIMIT 1
                        )
                    ELSE NULL
                END) AS icon
            ")
            ->rightJoinSub($connectedParticipantsQuery, 'connected_participants', function ($join) {
                $join->on('connected_participants.id', '=', 'connected_participant_details.id');
            })
            ->leftJoin('rios', 'rios.id', '=', 'connected_participant_details.rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'connected_participant_details.neo_id')
            ->groupBy('connected_participants.chat_id');

        /**
         * Subquery to fetch user details of the last message
         *
         * Output columns:
         * `id`
         * `name`
         */
        $lastMessageParticipantsDetailsQuery = DB::table('chat_participants as last_message_participant_details')
            ->select([
                'last_message_participant_details.id',
            ])
            // Get chat room name for connected type
            ->selectRaw('
                (CASE
                    WHEN last_message_participant_details.rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    WHEN last_message_participant_details.neo_id IS NOT NULL
                        THEN neos.organization_name
                    ELSE NULL
                END) AS name
            ')
            ->leftJoin('rios', 'rios.id', '=', 'last_message_participant_details.rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'last_message_participant_details.neo_id');

        /**
         * Subquery for fetching last message of chat room
         *
         * Output columns:
         * `chat_id`, `last_message_date`
         */
        $lastMessagesQuery = DB::table('chat_messages AS last_messages')
            ->select(
                'last_messages.chat_id',
                'last_messages.message',
                'last_messages.attaches',
                'last_messages.created_at AS last_message_date',
                'last_message_participant_details.name AS name',
            )
            ->leftJoin('chat_messages AS reference_last_messages', function ($join) {
                $join->on('last_messages.chat_id', '=', 'reference_last_messages.chat_id');
                $join->on('last_messages.id', '<', 'reference_last_messages.id');
            })
            ->rightJoinSub($lastMessageParticipantsDetailsQuery, 'last_message_participant_details', function ($join) {
                $join->on('last_message_participant_details.id', '=', 'last_messages.chat_participant_id');
            })
            ->whereNull('reference_last_messages.id');

        /**
         * Main query for fetching chat room list
         */
        return $query
            ->select([
                'chats.*',
                'last_messages.message',
                'last_messages.attaches AS last_message_attachment',
                'last_messages.name AS last_message_name',
                'last_messages.last_message_date',
                'subject_participants.unread_messages_count',
            ])
            // When no messages yet, use created at date of chat
            ->selectRaw('
                (CASE
                    WHEN last_messages.last_message_date IS NULL
                        THEN chats.created_at
                    ELSE last_messages.last_message_date
                END) AS last_action_date
            ')
            // When connected type, use connected participant icon
            ->selectRaw('
                (CASE
                    WHEN chats.chat_type = ?
                        THEN connected_participant_details.icon
                    ELSE chats.room_icon
                END) AS display_icon
            ', [ChatTypes::CONNECTED])
            // When connected type, use connected participant name
            ->selectRaw('
                (CASE
                    WHEN chats.chat_type = ?
                        THEN connected_participant_details.name
                    ELSE chats.room_name
                END) AS display_name
            ', [ChatTypes::CONNECTED])
            ->rightJoinSub($participatingQuery, 'subject_participants', function ($join) {
                $join->on('chats.id', '=', 'subject_participants.chat_id');
            })
            ->leftJoinSub($lastMessagesQuery, 'last_messages', function ($join) {
                $join->on('chats.id', '=', 'last_messages.chat_id');
            })
            ->leftJoinSub($connectedParticipantsDetailsQuery, 'connected_participant_details', function ($join) {
                $join->on('chats.id', '=', 'connected_participant_details.chat_id');
            })
            ->whereNull('chats.deleted_at')
            ->orderBy('last_action_date', 'desc');
    }

    /**
     * Scope query for chat with active status
     *
     * @param mixed $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('chats.status', ChatStatuses::ACTIVE);
    }

    /**
    * Scope query for chat with archive status
    *
    * @param mixed $query
    * @return mixed
    */
    public function scopeArchive($query)
    {
        return $query->where('chats.status', ChatStatuses::ARCHIVE);
    }

    /**
     * Scope a query based on common search conditions
     *
     * @param \Illuminate\Database\Eloquent\Builder<Chat> $query
     * @param array  $options   Array of response options
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeCommonConditions($query, $options)
    {
        // Search by full name
        if (isset($options['name']) && !is_null($options['name'])) {
            $searchName = '%' . mb_strtolower($options['name']) . '%';
            $query->having(DB::raw("LOWER(display_name)"), 'LIKE', $searchName);
        }

        return $query;
    }

    /**
     * Get chat participants users pair per auth user and other rio
     *
     * @param mixed $rio
     * @param mixed $service
     * @param mixed $type
     * @return mixed
     */
    public static function getChatParticipantsPair($rio, $service, $type = null)
    {
        /** @var User */
        $user = auth()->user();
        $serviceSelection = json_decode(Session::get('ServiceSelected'));
        /** @var Neo */
        $currentNeo = Neo::find($serviceSelection->data->id);
        $connections = null;
        $currentId = null;
        $otherUser = null;

        switch ($type) {
            case ConnectedChatType::NEO_TO_RIO:
                if ($service === ServiceSelectionTypes::RIO) {
                    $connections = $rio->chats;
                    $currentId = $serviceSelection->data->id;
                    $otherUser = $rio->id;
                    $service = ServiceSelectionTypes::NEO;
                }
                break;
            case ConnectedChatType::RIO_TO_NEO:
                if ($service === ServiceSelectionTypes::NEO) {
                    $connections = $user->rio->chats;
                    $otherUser = $serviceSelection->data->id;
                    $currentId = $rio->id;
                }
                break;
            case ConnectedChatType::NEO_TO_NEO:
                if ($service === ServiceSelectionTypes::NEO) {
                    $connections = $currentNeo->chats;
                    $otherUser = $serviceSelection->data->id;
                    $currentId = $rio->id;
                }
                break;
            default:
                if ($service === ServiceSelectionTypes::RIO) {
                    $connections = $user->rio->chats;
                    $currentId = $rio->id;
                    $otherUser = $user->id;
                } else {
                    $connections = $rio->chats;
                    $currentId = $serviceSelection->data->id;
                }
        }
        $connectionUsers = self::checkConnections($connections, $currentId, $service);

        if (empty($connectionUsers)) {
            $connectionUsers = self::checkConnections($rio->chats, $otherUser, $service);
        }

        return $connectionUsers;
    }

    /**
     * Check chat participants connections by pair per auth user and other rio
     *
     * @param int $id
     * @param mixed $connections
     * @param mixed $service
     * @return mixed
     */
    public static function checkConnections($connections, $id, $service)
    {
        $chatUsers = [];

        if (!$connections) {
            return $chatUsers;
        }

        if ($service === ServiceSelectionTypes::RIO) {
            foreach ($connections as $connection) {
                $checkChatConnectedRios = $connection->participants->where('rio_id', $id)->first();

                if ($checkChatConnectedRios) {
                    $chatUsers[$connection->id] = $connection->participants;
                }
            }
        } else {
            foreach ($connections as $connection) {
                $checkChatConnectedRios = $connection->participants
                    ->where('neo_id', $id)->first();

                if ($checkChatConnectedRios) {
                    $chatUsers[$connection->id] = $connection->participants;
                }
            }
        }

        return $chatUsers;
    }
}
