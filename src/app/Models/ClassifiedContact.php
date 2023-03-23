<?php

namespace App\Models;

use App\Enums\Classified\MessageSender;
use App\Enums\ServiceSelectionTypes;
use App\Objects\ServiceSelected;
use App\Traits\ModelUpdatedTrait;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ClassifiedContact
 *
 * @property int $id id for Laravel
 * @property int $classified_sale_id
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property int|null $selling_rio_id ↓どちらかのみセット (it's either)
 * @property int|null $selling_neo_id ↑どちらかのみセット (it's either)
 * @property string $title
 * @property int $created_rio_id
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\ClassifiedSale $classified_sale
 * @property-read \App\Models\Rio $created_rio
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClassifiedContactMessage[] $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Rio|null $rio
 * @property-read \App\Models\Neo|null $selling_neo
 * @property-read \App\Models\Rio|null $selling_rio
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact commonConditions($options = [], $session = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact inquiryList()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact newQuery()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedContact onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact whereClassifiedSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact whereSellingNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact whereSellingRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedContact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ClassifiedContact withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedContact withoutTrashed()
 * @mixin \Eloquent
 */
class ClassifiedContact extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classified_contacts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'classified_sale_id',
        'rio_id',
        'neo_id',
        'selling_rio_id',
        'selling_neo_id',
        'title',
        'created_rio_id',
    ];

    /**
     * Define relationship for Classied sales
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classified_sale()
    {
        return $this->belongsTo(ClassifiedSale::class, 'classified_sale_id');
    }

    /**
     * Define relationship for RIO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class, 'rio_id');
    }

    /**
     * Define relationship for NEO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class, 'neo_id');
    }

    /**
     * Define relationship for RIO seller
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function selling_rio()
    {
        return $this->belongsTo(Rio::class, 'selling_rio_id');
    }

    /**
     * Define relationship for NEO seller
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function selling_neo()
    {
        return $this->belongsTo(Neo::class, 'selling_neo_id');
    }

    /**
     * Define relationship for RIO creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_rio()
    {
        return $this->belongsTo(Rio::class, 'created_rio_id');
    }

    /**
     * Define relationship with Classified payments table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(ClassifiedContactMessage::class);
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($contact) {
            // Softdelete associated contact messages
            $contact->messages()->each(function ($message) {
                $message->delete();
            });
        });
    }

    /**
     * Scope a query of inquiry list
     *
     * @param \Illuminate\Database\Eloquent\Builder<ClassifiedContact> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeInquiryList($query)
    {
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
            ->selectRaw('
                (CASE
                    WHEN contacts.rio_id IS NOT NULL
                        THEN buyer_rio_profile.profile_photo
                    WHEN contacts.neo_id IS NOT NULL
                        THEN buyer_neo_profile.profile_photo
                    ELSE NULL
                END) AS buyer_photo
            ')
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
            ->selectRaw('
                (CASE
                    WHEN contacts.selling_rio_id IS NOT NULL
                        THEN selling_rio_profile.profile_photo
                    WHEN contacts.selling_neo_id IS NOT NULL
                        THEN selling_neo_profile.profile_photo
                    ELSE NULL
                END) AS seller_photo
            ')
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
         * Subquery for fetching last message of inquiry
         *
         * Output columns:
         * `classified_contact_id`, `message`, 'attaches', 'last_message_date'
         */
        $lastMessagesQuery = DB::table('classified_contact_messages AS last_messages')
            ->select(
                'last_messages.classified_contact_id',
                'last_messages.message',
                'last_messages.attaches',
                'last_messages.created_at AS last_message_date',
            )
            ->leftJoin('classified_contact_messages AS reference_last_messages', function ($join) {
                $join->on('last_messages.classified_contact_id', '=', 'reference_last_messages.classified_contact_id');
                $join->on('last_messages.id', '<', 'reference_last_messages.id');
            })
            ->whereNull('reference_last_messages.id');

        /**
         * Subquery for fetching last message of inquiry
         *
         * Output columns:
         * `classified_contacts.*`, `product_title`, 'seller_name', 'seller_photo'
         * `seller_type`, `buyer_name`, `buyer_photo`, `buyer_type`, `message`
         * `last_message_attachment`
         */
        return $query
            ->select([
                'classified_contacts.*',
                'contacts.title AS product_title',
                'contacts.seller_name AS seller_name',
                'contacts.seller_photo AS seller_photo',
                'contacts.seller_entity_type AS seller_type',
                'contacts.buyer_name AS buyer_name',
                'contacts.buyer_photo AS buyer_photo',
                'contacts.buyer_entity_type AS buyer_type',
                'last_messages.message',
                'last_messages.last_message_date',
                'last_messages.attaches AS last_message_attachment',
            ])
            // When no messages yet, use created at date of chat
            ->selectRaw('
                (CASE
                    WHEN last_messages.last_message_date IS NULL
                        THEN classified_contacts.created_at
                    ELSE last_messages.last_message_date
                END) AS last_action_date
            ')
            ->leftJoinSub($contactsQuery, 'contacts', function ($join) {
                $join->on('contacts.id', '=', 'classified_contacts.id');
            })
            ->leftJoinSub($lastMessagesQuery, 'last_messages', function ($join) {
                $join->on('classified_contacts.id', '=', 'last_messages.classified_contact_id');
            })
            ->whereNull('classified_contacts.deleted_at')
            ->orderBy('last_action_date', 'desc');
    }

    /**
     * Scope a query based on common search conditions
     *
     * @param \Illuminate\Database\Eloquent\Builder<ClassifiedContact> $query
     * @param array  $options   Array of response options
     * @param object $session   Service session
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeCommonConditions($query, $options = [], $session = null)
    {
        // Set default session
        if (empty($session)) {
            $session = ServiceSelected::getSelected();
        }

        // Initialize variables
        $entity = $session->data;

        if (isset($options['transmit_type']) && !is_null($options['transmit_type'])) {
            switch ($options['transmit_type']) {
                // Get inquiries from seller / sent inquiries
                case MessageSender::SELLER:
                    $query
                        ->addSelect([
                            'contacts.seller_id AS display_entity_id',
                            'contacts.seller_name AS display_name',
                            'contacts.seller_photo AS display_photo',
                            'contacts.seller_entity_type AS display_entity_type',
                        ])
                        ->where('contacts.buyer_entity_type', $session->type)
                        ->where('contacts.buyer_id', $entity->id);
                    break;

                // Get inquiries from buyer / received inquiries
                default:
                    $query
                        ->addSelect([
                            'contacts.buyer_id AS display_entity_id',
                            'contacts.buyer_name AS display_name',
                            'contacts.buyer_photo AS display_photo',
                            'contacts.buyer_entity_type AS display_entity_type',
                        ])
                        ->where('contacts.seller_entity_type', $session->type)
                        ->where('contacts.seller_id', $entity->id);
                    break;
            }
        }
        return $query;
    }

    /**
     * Get contact receiver information
     *
     * @return array
     */
    public function getReceiver()
    {
        // Initialize defaults
        $type = ServiceSelectionTypes::RIO;
        $receiver = null;

        // If buyer, set seller information as contact receiver
        if ($this->isBuyer()) {
            if (!empty($this->selling_rio_id)) {
                $type = ServiceSelectionTypes::RIO;
                $receiver = $this->selling_rio;
            }

            if (!empty($this->selling_neo_id)) {
                $type = ServiceSelectionTypes::NEO;
                $receiver = $this->selling_neo;
            }
        }

        // If seller, set buyer information as contact receiver
        if ($this->isSeller()) {
            if (!empty($this->rio_id)) {
                $type = ServiceSelectionTypes::RIO;
                $receiver = $this->rio;
            }

            if (!empty($this->neo_id)) {
                $type = ServiceSelectionTypes::NEO;
                $receiver = $this->neo;
            }
        }

        return [
            'type' => $type,
            'data' => $receiver,
        ];
    }

    /**
     * Check if user has access to the contact inquiry conversation.
     *
     * @return bool
     */
    public function isAllowedAccess()
    {
        if ($this->isBuyer()) {
            return true;
        }

        return $this->isSeller();
    }

    /**
     * Check if user is the product buyer
     *
     * @return bool
     */
    public function isBuyer()
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Check if service selected is the buyer
        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $this->rio_id === $service->data->id;
            case ServiceSelectionTypes::NEO:
                return $this->neo_id === $service->data->id;
            default:
                return false;
        }
    }

    /**
     * Check if user is the product seller
     *
     * @return bool
     */
    public function isSeller()
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Check if service selected is the seller
        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $this->selling_rio_id === $service->data->id;
            case ServiceSelectionTypes::NEO:
                return $this->selling_neo_id === $service->data->id;
            default:
                return false;
        }
    }

    /**
     * Send notification to message receiver when a new message is sent.
     *
     * @param \App\Models\ClassifiedContact $contact
     * @param int $sender
     * @return void
     */
    public static function sendNotification($contact, $sender)
    {
        if ($sender === MessageSender::SELLER) {
            // SELLER - RIO sender
            if (!empty($contact->selling_rio_id)) {
                /** @var Rio */
                $rioSender = Rio::whereId($contact->selling_rio_id)->first();
                $senderName = $rioSender->full_name . 'さん';
            }

            // SELLER - NEO sender
            if (!empty($contact->selling_neo_id)) {
                /** @var Neo */
                $neoSender = Neo::whereId($contact->selling_neo_id)->first();
                $senderName = $neoSender->organization_name;
            }

            // BUYER - RIO receiver
            if (!empty($contact->rio_id)) {
                $rioReceiverId = $contact->rio_id;
            }

            // BUYER - NEO receiver
            if (!empty($contact->neo_id)) {
                /** @var Neo */
                $neoReceiver = Neo::whereId($contact->neo_id)->first();
                /** @var int @phpstan-ignore-next-line */
                $rioReceiverId = $neoReceiver->owner ? $neoReceiver->owner->rio_id : null;
            }
        } else {
            // BUYER - RIO sender
            if (!empty($contact->rio_id)) {
                /** @var Rio */
                $rioSender = Rio::whereId($contact->rio_id)->first();
                $senderName = $rioSender->full_name . 'さん';
            }

            // BUYER - NEO sender
            if (!empty($contact->neo_id)) {
                /** @var Neo */
                $neoSender = Neo::whereId($contact->neo_id)->first();
                $senderName = $neoSender->organization_name;
            }

            // SELLER - RIO receiver
            if (!empty($contact->selling_rio_id)) {
                $rioReceiverId = $contact->selling_rio_id;
            }

            // SELLER - NEO receiver
            if (!empty($contact->selling_neo_id)) {
                /** @var Neo */
                $neoReceiver = Neo::whereId($contact->selling_neo_id)->first();
                /** @var int @phpstan-ignore-next-line */
                $rioReceiverId = $neoReceiver->owner ? $neoReceiver->owner->rio_id : null;
            }
        }

        // Send notification to message receiver
        if (!empty($rioReceiverId)) {
            Notification::createNotification([
                'rio_id' => $rioReceiverId,
                'receive_neo_id' => $neoReceiver->id ?? null,
                'notification_content' => __('Notification Content - Netshop Message', [
                    'sender_name' => $senderName ?? '-'
                ]),
                'destination_url' => route('classifieds.messages.conversation', $contact->id)
            ]);
        }
    }
}
