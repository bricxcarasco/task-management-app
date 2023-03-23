<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ChatParticipant
 *
 * @property int $id id for Laravel
 * @property int $chat_id
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property int|null $last_read_chat_message_id
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Chat $chat
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Rio|null $rio
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant newQuery()
 * @method static \Illuminate\Database\Query\Builder|ChatParticipant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereLastReadChatMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ChatParticipant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ChatParticipant withoutTrashed()
 * @mixin \Eloquent
 */
class ChatParticipant extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_participants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chat_id',
        'rio_id',
        'neo_id',
        'last_read_chat_message_id',
    ];

    /**
     * Define relationship with rios table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class, 'rio_id');
    }

    /**
     * Define relationship with neos table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class, 'neo_id');
    }

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
     * Update last read message id
     *
     * @return void
     */
    public function readMessages()
    {
        $lastMessageId = ChatMessage::whereChatId($this->chat_id)
            ->latest('id')
            ->value('id');

        if ($lastMessageId) {
            $this->last_read_chat_message_id = $lastMessageId;
            $this->save();
        }
    }

    /**
     * Check if chat participant is NEO
     *
     * @return bool
     */
    public function isNeo()
    {
        return !empty($this->neo_id);
    }
}
