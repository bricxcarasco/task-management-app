<?php

namespace App\Models;

use App\Enums\ServiceSelectionTypes;
use App\Traits\ModelUpdatedTrait;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;

/**
 * App\Models\ChatMessage
 *
 * @property int $id id for Laravel
 * @property int $chat_id
 * @property int $chat_participant_id
 * @property string $message
 * @property string|null $attaches 最大5ファイルまでjson形式で指定可能…セットするIDは 文章管理.ID
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Chat $chat
 * @property-read \App\Models\ChatParticipant $participant
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newQuery()
 * @method static \Illuminate\Database\Query\Builder|ChatMessage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereAttaches($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereChatParticipantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ChatMessage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ChatMessage withoutTrashed()
 * @mixin \Eloquent
 */
class ChatMessage extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chat_id',
        'chat_participant_id',
        'message',
        'attaches',
    ];

    /**
     * Define relationship with chats table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Define relationship with chat_participants table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function participant()
    {
        return $this->belongsTo(ChatParticipant::class, 'chat_participant_id');
    }

    /**
     * Get all unread messages count of all service subject talk rooms.
     *
     * @return int
     */
    public static function getAllUnreadMessagesCount()
    {
        // Get selected service session
        $service = json_decode(Session::get('ServiceSelected'));
        $entity = $service->data;

        /**
         * Subquery for chat where service is participating
         * including unread messages per chat room
         *
         * Output columns:
         * `id`
         * `chat_id`
         * `unread_messages_count`
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
        if ($service->type == ServiceSelectionTypes::NEO) {
            $participatingQuery->where('subject_participants.neo_id', $entity->id);
        } else {
            $participatingQuery->where('subject_participants.rio_id', $entity->id);
        }

        // Get total unread message count
        $chats = Chat::query()
            ->selectRaw('
                SUM(subject_participants.unread_messages_count) AS unread_message_count
            ')
            ->rightJoinSub($participatingQuery, 'subject_participants', function ($join) {
                $join->on('chats.id', '=', 'subject_participants.chat_id');
            })
            ->whereNull('chats.deleted_at')
            ->first();

        return $chats->unread_message_count ?? 0;
    }

    /**
     * Get the file attachments of the chat message
     *
     * @return array
     */
    public function getFileAttachmentsAttribute()
    {
        try {
            // Guard clause for empty attachment
            if (empty($this->attaches)) {
                return [];
            }

            // Decode json string
            $directoryIds = json_decode($this->attaches, true);

            // Get documents
            $documents = collect($directoryIds)
                ->map(function ($directoryId) {
                    $document = Document::query()
                        ->select(
                            'id',
                            'document_name',
                            'mime_type'
                        )
                        ->whereId($directoryId)
                        ->first();

                    return $document ?: null;
                })
                ->toArray();

            return $documents;
        } catch (\Exception $exception) {
            report($exception);
            return [];
        }
    }

    /**
     * Scope query for fetching messages of a specified chat
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $chatId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMessageList($query, $chatId)
    {
        $defaultProfileImage = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.neo_profile_image');

        $participantQuery = DB::table('chat_participants')
            ->select(
                'chat_participants.id',
            )
            // Get participant name
            ->selectRaw('
                (CASE
                    WHEN chat_participants.rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    WHEN chat_participants.neo_id IS NOT NULL
                        THEN neos.organization_name
                    ELSE NULL
                END) AS name
            ')
            // Get participant profile_photo
            ->selectRaw("
                (CASE
                    WHEN chat_participants.rio_id IS NOT NULL
                        THEN
                            CASE
                                WHEN rio_profiles.profile_photo IS NOT NULL
                                    THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                                ELSE '" . $defaultProfileImage . "'
                            END
                    WHEN chat_participants.neo_id IS NOT NULL
                        THEN
                            CASE
                                WHEN neo_profiles.profile_photo IS NOT NULL
                                    THEN CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                                ELSE '" . $defaultProfileImage . "'
                            END
                    ELSE '" . $defaultProfileImage . "'
                END) AS profile_photo
            ")
            // Get participant entity type
            ->selectRaw('
                (CASE
                    WHEN chat_participants.rio_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::RIO . '"
                    WHEN chat_participants.neo_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::NEO . '"
                    ELSE NULL
                END) AS participant_type
            ')
            // Get participant entity id
            ->selectRaw('
                (CASE
                    WHEN chat_participants.rio_id IS NOT NULL
                        THEN rios.id
                    WHEN chat_participants.neo_id IS NOT NULL
                        THEN neos.id
                    ELSE NULL
                END) AS entity_id
            ')
            ->leftJoin('rios', 'rios.id', '=', 'chat_participants.rio_id')
            ->leftJoin('rio_profiles', 'rio_profiles.rio_id', '=', 'chat_participants.rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'chat_participants.neo_id')
            ->leftJoin('neo_profiles', 'neo_profiles.neo_id', '=', 'chat_participants.neo_id')
            ->where('chat_participants.chat_id', $chatId);

        return $query
            ->select(
                'chat_messages.*',
                'participants.name',
                'participants.profile_photo',
                'participants.participant_type',
                'participants.entity_id'
            )
            ->leftJoinSub($participantQuery, 'participants', function ($join) {
                $join->on('chat_messages.chat_participant_id', '=', 'participants.id');
            })
            ->where('chat_messages.chat_id', $chatId);
    }
}
