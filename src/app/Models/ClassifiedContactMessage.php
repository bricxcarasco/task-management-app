<?php

namespace App\Models;

use App\Enums\MailTemplates;
use App\Enums\ServiceSelectionTypes;
use App\Notifications\NetshopChatNotification;
use App\Traits\ModelUpdatedTrait;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;

/**
 * App\Models\ClassifiedContactMessage
 *
 * @property int $id id for Laravel
 * @property int $classified_contact_id
 * @property int $sender 0: 売り手側メッセージ、1: 買い手側メッセージ
 * @property string $message
 * @property string|null $attaches 最大5ファイルまでjson形式で指定可能… （文書管理サービスは使わない）
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\ClassifiedContact $classified_contact
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContactMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContactMessage newQuery()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedContactMessage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContactMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContactMessage whereAttaches($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContactMessage whereClassifiedContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContactMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContactMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContactMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContactMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContactMessage whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContactMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ClassifiedContactMessage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedContactMessage withoutTrashed()
 * @mixin \Eloquent
 */
class ClassifiedContactMessage extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classified_contact_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'classified_contact_id',
        'sender',
        'message',
        'attaches',
    ];

    /**
     * Define relationship for Classied contacts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classified_contact()
    {
        return $this->belongsTo(ClassifiedContact::class, 'classified_contact_id');
    }

    /**
     * Scope query for fetching messages of a specified chat
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $contactId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMessageList($query, $contactId)
    {
        $defaultProfileImage = config('app.url') . '/' . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . '/' . 'storage/' . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . '/' . 'storage/' . config('bphero.neo_profile_image');

        /**
         * Subquery to fetch seller and buyer info
         *
         * Output columns:
         * `id`
         * `buyer_id`
         * `buyer_name`
         * `buyer_photo`
         * `buyer_entity_type`
         * `seller_id`
         * `seller_name`
         * `seller_photo`
         * `seller_entity_type`
         */
        $contactsQuery = DB::table('classified_contacts as contacts')
            ->select([
                'contacts.id',
                'contacts.title',
            ])
            // Get buyer id
            ->selectRaw('
                (CASE
                    WHEN contacts.rio_id IS NOT NULL
                        THEN contacts.rio_id
                    WHEN contacts.neo_id IS NOT NULL
                        THEN contacts.neo_id
                    ELSE NULL
                END) AS buyer_id
            ')
            // Get buyer name
            ->selectRaw('
                (CASE
                    WHEN contacts.rio_id IS NOT NULL
                        THEN TRIM(CONCAT(buyer_rio.family_name, " ", buyer_rio.first_name))
                    WHEN contacts.neo_id IS NOT NULL
                        THEN buyer_neo.organization_name
                    ELSE NULL
                END) AS buyer_name
            ')
            // Get buyer photo
            ->selectRaw("
                (CASE
                    WHEN contacts.rio_id IS NOT NULL
                        THEN
                            CASE
                                WHEN buyer_rio_profile.profile_photo IS NOT NULL
                                    THEN CONCAT('" . $rioProfileImagePath . "', buyer_rio_profile.rio_id, '/', buyer_rio_profile.profile_photo)
                                ELSE '" . $defaultProfileImage . "'
                            END
                    WHEN contacts.neo_id IS NOT NULL
                        THEN
                            CASE
                                WHEN buyer_neo_profile.profile_photo IS NOT NULL
                                    THEN CONCAT('" . $neoProfileImagePath . "', buyer_neo_profile.neo_id, '/', buyer_neo_profile.profile_photo)
                                ELSE '" . $defaultProfileImage . "'
                            END
                    ELSE '" . $defaultProfileImage . "'
                END) AS buyer_photo
            ")
            // Get buyer entity type
            ->selectRaw('
                (CASE
                    WHEN contacts.rio_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::RIO . '"
                    WHEN contacts.neo_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::NEO . '"
                    ELSE NULL
                END) AS buyer_entity_type
            ')
            // Get seller id
            ->selectRaw('
                (CASE
                    WHEN contacts.selling_rio_id IS NOT NULL
                        THEN contacts.selling_rio_id
                    WHEN contacts.selling_neo_id IS NOT NULL
                        THEN contacts.selling_neo_id
                    ELSE NULL
                END) AS seller_id
            ')
            // Get seller name
            ->selectRaw('
                (CASE
                    WHEN contacts.selling_rio_id IS NOT NULL
                        THEN TRIM(CONCAT(selling_rio.family_name, " ", selling_rio.first_name))
                    WHEN contacts.selling_neo_id IS NOT NULL
                        THEN selling_neo.organization_name
                    ELSE NULL
                END) AS seller_name
            ')
            // Get seller photo
            ->selectRaw("
                (CASE
                    WHEN contacts.selling_rio_id IS NOT NULL
                        THEN
                            CASE
                                WHEN selling_rio_profile.profile_photo IS NOT NULL
                                    THEN CONCAT('" . $rioProfileImagePath . "', selling_rio_profile.rio_id, '/', selling_rio_profile.profile_photo)
                                ELSE '" . $defaultProfileImage . "'
                            END
                    WHEN contacts.selling_neo_id IS NOT NULL
                        THEN
                            CASE
                                WHEN selling_neo_profile.profile_photo IS NOT NULL
                                    THEN CONCAT('" . $neoProfileImagePath . "', selling_neo_profile.neo_id, '/', selling_neo_profile.profile_photo)
                                ELSE '" . $defaultProfileImage . "'
                            END
                    ELSE '" . $defaultProfileImage . "'
                END) AS seller_photo
            ")
            // Get seller entity type
            ->selectRaw('
                (CASE
                    WHEN contacts.selling_rio_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::RIO . '"
                    WHEN contacts.selling_neo_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::NEO . '"
                    ELSE NULL
                END) AS seller_entity_type
            ')
            ->leftJoin('rios AS buyer_rio', 'buyer_rio.id', '=', 'contacts.rio_id')
            ->leftJoin('rio_profiles AS buyer_rio_profile', 'buyer_rio_profile.rio_id', '=', 'contacts.rio_id')
            ->leftJoin('neos AS buyer_neo', 'buyer_neo.id', '=', 'contacts.neo_id')
            ->leftJoin('neo_profiles AS buyer_neo_profile', 'buyer_neo_profile.neo_id', '=', 'contacts.neo_id')
            ->leftJoin('rios AS selling_rio', 'selling_rio.id', '=', 'contacts.selling_rio_id')
            ->leftJoin('rio_profiles AS selling_rio_profile', 'selling_rio_profile.rio_id', '=', 'contacts.selling_rio_id')
            ->leftJoin('neos AS selling_neo', 'selling_neo.id', '=', 'contacts.selling_neo_id')
            ->leftJoin('neo_profiles AS selling_neo_profile', 'selling_neo_profile.neo_id', '=', 'contacts.selling_neo_id');

        /**
         * Query for fetching inquiry messages
         *
         * Output columns:
         * `classified_contact_messages.*`
         * `seller_id`
         * `seller_name`
         * `seller_photo`
         * `seller_type`
         * `buyer_id`
         * `buyer_name`
         * `buyer_photo`
         * `buyer_type`
         */
        return $query
            ->select([
                'classified_contact_messages.*',
                'contacts.seller_id AS seller_id',
                'contacts.seller_name AS seller_name',
                'contacts.seller_photo AS seller_photo',
                'contacts.seller_entity_type AS seller_type',
                'contacts.buyer_id AS buyer_id',
                'contacts.buyer_name AS buyer_name',
                'contacts.buyer_photo AS buyer_photo',
                'contacts.buyer_entity_type AS buyer_type',
            ])
            ->leftJoin(
                'classified_contacts',
                'classified_contacts.id',
                '=',
                'classified_contact_messages.classified_contact_id'
            )
            ->leftJoinSub($contactsQuery, 'contacts', function ($join) {
                $join->on('contacts.id', '=', 'classified_contacts.id');
            })
            ->whereNull('classified_contact_messages.deleted_at')
            ->where('classified_contact_messages.classified_contact_id', $contactId)
            ->orderBy('classified_contact_messages.created_at', 'ASC');
    }

    /**
    * Send email notification to RIO/NEO selected as connection recipient
    *
    * @param \App\Models\Neo|\App\Models\Rio $sender
    * @param \App\Models\Neo|\App\Models\Rio $receiver
    * @param \App\Models\ClassifiedContactMessage $message
    * @return bool|void
    */
    public function sendNetshopChatEmail($sender, $receiver, $message)
    {
        // Check RIO recipient if allowed to receive email
        if ($receiver instanceof \App\Models\Rio) {
            if (NotificationRejectSetting::isRejectedEmail($receiver, MailTemplates::NETSHOP_CHAT_MESSAGE)) {
                return false;
            }

            // Get email information
            $user = User::whereRioId($receiver->id)->firstOrFail();

            // Send email to RIO receiver
            Notification::sendNow($user, new NetshopChatNotification($sender, $receiver, $message));
        }

        // Check NEO owner receiver if allowed to receive email
        if ($receiver instanceof \App\Models\Neo) {
            // Get NEO owner NeoBelong information
            /** @var \App\Models\NeoBelong */
            $neoBelongs = $receiver->owner;

            // Guard clause for non-existing NEO owner
            if (empty($neoBelongs)) {
                return false;
            }

            // Get NEO owner RIO information
            /** @var \App\Models\Rio */
            $neoOwnerRecipient = $neoBelongs->rio;

            if (NotificationRejectSetting::isRejectedEmail(
                $neoOwnerRecipient,
                MailTemplates::NETSHOP_CHAT_MESSAGE
            )) {
                return false;
            }

            // Get email information
            $user = User::whereRioId($neoOwnerRecipient->id)->firstOrFail();

            // Send email to NEO receiver
            Notification::sendNow($user, new  NetshopChatNotification($sender, $receiver, $message));
        }
    }
}
