<?php

namespace App\Models;

use App\Enums\MailTemplates;
use App\Enums\ServiceSelectionTypes;
use App\Notifications\NetshopPurchaseNotification;
use App\Objects\ServiceSelected;
use App\Traits\ModelUpdatedTrait;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;
use Laravel\Cashier\Billable;

/**
 * App\Models\ClassifiedPayment
 *
 * @property int $id id for Laravel
 * @property int $classified_sale_id
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property string $access_key ハッシュ値またはランダム文字列を保持
 * @property string|null $price 必要になるまで（日本で運用する限り）は小数点以下を使用しない
 * @property string $payment_method
 * @property string|null $stripe_payment_intent_id
 * @property int $status 0: 決済待ち、1: 決済完了(自動反映)、2: 決済完了(手動反映)
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\ClassifiedSale $classified_sale
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Rio|null $rio
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Cashier\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment accessiblePayments($service = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment newQuery()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedPayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment whereAccessKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment whereClassifiedSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment whereStripePaymentIntentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ClassifiedPayment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedPayment withoutTrashed()
 * @mixin \Eloquent
 */
class ClassifiedPayment extends Model
{
    use Billable;
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classified_payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'classified_sale_id',
        'rio_id',
        'neo_id',
        'access_key',
        'price',
        'payment_method',
        'stripe_payment_intent_id',
        'status',
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
     * Scope a query to get all accessible payments of the selected service.
     *
     * @param \Illuminate\Database\Eloquent\Builder<ClassifiedSetting> $query
     * @param object|null $service
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeAccessiblePayments($query, $service = null)
    {
        // Get current subject selected session
        if (empty($service)) {
            $service = ServiceSelected::getSelected();
        }

        /**
         * Subquery to fetch seller and product information
         *
         * Output columns:
         * `id`
         * `selling_rio_id`
         * `selling_neo_id`
         * `title`
         * `price`
         * `seller_id`
         * `seller_name`
         * `seller_photo`
         * `seller_entity_type`
         */
        $sellerQuery = DB::table('classified_sales as seller')
            ->select([
                'seller.id',
                'seller.selling_rio_id',
                'seller.selling_neo_id',
                'seller.title',
                'seller.price',
            ])
            // Get seller id
            ->selectRaw('
                (CASE
                    WHEN seller.selling_rio_id IS NOT NULL
                        THEN seller.selling_rio_id
                    WHEN seller.selling_neo_id IS NOT NULL
                        THEN seller.selling_neo_id
                    ELSE NULL
                END) AS seller_id
            ')
            // Get seller name
            ->selectRaw('
                (CASE
                    WHEN seller.selling_rio_id IS NOT NULL
                        THEN TRIM(CONCAT(selling_rio.family_name, " ", selling_rio.first_name))
                    WHEN seller.selling_neo_id IS NOT NULL
                        THEN selling_neo.organization_name
                    ELSE NULL
                END) AS seller_name
            ')
            // Get seller photo
            ->selectRaw('
                (CASE
                    WHEN seller.selling_rio_id IS NOT NULL
                        THEN selling_rio_profile.profile_photo
                    WHEN seller.selling_neo_id IS NOT NULL
                        THEN selling_neo_profile.profile_photo
                    ELSE NULL
                END) AS seller_photo
            ')
            // Get seller entity type
            ->selectRaw('
                (CASE
                    WHEN seller.selling_rio_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::RIO . '"
                    WHEN seller.selling_neo_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::NEO . '"
                    ELSE NULL
                END) AS seller_entity_type
            ')
            ->leftJoin('rios AS selling_rio', 'selling_rio.id', '=', 'seller.selling_rio_id')
            ->leftJoin('rio_profiles AS selling_rio_profile', 'selling_rio_profile.rio_id', '=', 'seller.selling_rio_id')
            ->leftJoin('neos AS selling_neo', 'selling_neo.id', '=', 'seller.selling_neo_id')
            ->leftJoin('neo_profiles AS selling_neo_profile', 'selling_neo_profile.neo_id', '=', 'seller.selling_neo_id');

        /**
         * Subquery to fetch buyer information
         *
         * Output columns:
         * `id`
         * `buyer_id`
         * `buyer_name`
         * `buyer_photo`
         * `buyer_entity_type`
         */
        $buyerQuery = DB::table('classified_payments as buyer')
            ->select([
                'buyer.id',
            ])
            // Get buyer id
            ->selectRaw('
                (CASE
                    WHEN buyer.rio_id IS NOT NULL
                        THEN buyer.rio_id
                    WHEN buyer.neo_id IS NOT NULL
                        THEN buyer.neo_id
                    ELSE NULL
                END) AS buyer_id
            ')
            // Get buyer name
            ->selectRaw('
                (CASE
                    WHEN buyer.rio_id IS NOT NULL
                        THEN TRIM(CONCAT(buyer_rio.family_name, " ", buyer_rio.first_name))
                    WHEN buyer.neo_id IS NOT NULL
                        THEN buyer_neo.organization_name
                    ELSE NULL
                END) AS buyer_name
            ')
            // Get buyer photo
            ->selectRaw('
                (CASE
                    WHEN buyer.rio_id IS NOT NULL
                        THEN buyer_rio_profile.profile_photo
                    WHEN buyer.neo_id IS NOT NULL
                        THEN buyer_neo_profile.profile_photo
                    ELSE NULL
                END) AS buyer_photo
            ')
            // Get buyer entity type
            ->selectRaw('
                (CASE
                    WHEN buyer.rio_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::RIO . '"
                    WHEN buyer.neo_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::NEO . '"
                    ELSE NULL
                END) AS buyer_entity_type
            ')
            ->leftJoin('rios AS buyer_rio', 'buyer_rio.id', '=', 'buyer.rio_id')
            ->leftJoin('rio_profiles AS buyer_rio_profile', 'buyer_rio_profile.rio_id', '=', 'buyer.rio_id')
            ->leftJoin('neos AS buyer_neo', 'buyer_neo.id', '=', 'buyer.neo_id')
            ->leftJoin('neo_profiles AS buyer_neo_profile', 'buyer_neo_profile.neo_id', '=', 'buyer.neo_id');

        /**
         * Subquery for fetching accessible payments
         *
         * Output columns:
         * `classified_payments.*`
         * `product_title`
         * `product_price`
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
                'classified_payments.*',
                'seller.selling_rio_id AS selling_rio_id',
                'seller.selling_neo_id AS selling_neo_id',
                'seller.title AS product_title',
                'seller.price AS product_price',
                'seller.seller_id AS seller_id',
                'seller.seller_name AS seller_name',
                'seller.seller_photo AS seller_photo',
                'seller.seller_entity_type AS seller_type',
                'buyer.buyer_id AS buyer_id',
                'buyer.buyer_name AS buyer_name',
                'buyer.buyer_photo AS buyer_photo',
                'buyer.buyer_entity_type AS buyer_type',
            ])
            ->leftJoinSub($sellerQuery, 'seller', function ($join) {
                $join->on('seller.id', '=', 'classified_payments.classified_sale_id');
            })
            ->leftJoinSub($buyerQuery, 'buyer', function ($join) {
                $join->on('buyer.id', '=', 'classified_payments.id');
            })
            ->whereNull('classified_payments.deleted_at');
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

        /** @var ClassifiedSale */
        $sale = $this->classified_sale;

        // Check if service selected is the seller
        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $sale->selling_rio_id === $service->data->id;
            case ServiceSelectionTypes::NEO:
                return $sale->selling_neo_id === $service->data->id;
            default:
                return false;
        }
    }

    /**
    * Send email notification to product buyer RIO/NEO
    *
    * @param \App\Models\Neo|\App\Models\Rio $seller
    * @param \App\Models\Neo|\App\Models\Rio $buyer
    * @param mixed $product
    * @return bool|void
    */
    public function sendNetshopPurchaseEmail($seller, $buyer, $product)
    {
        // Check RIO recipient if allowed to receive email
        if ($buyer instanceof \App\Models\Rio) {
            if (NotificationRejectSetting::isRejectedEmail($buyer, MailTemplates::NETSHOP_PURCHASE)) {
                return false;
            }

            // Get email information
            $user = User::whereRioId($buyer->id)->firstOrFail();

            // Send email to RIO receiver
            Notification::sendNow($user, new NetshopPurchaseNotification($seller, $buyer, $product));
        }

        // Check NEO owner receiver if allowed to receive email
        if ($buyer instanceof \App\Models\Neo) {
            // Get NEO owner NeoBelong information
            /** @var \App\Models\NeoBelong */
            $neoBelongs = $buyer->owner;

            // Guard clause for non-existing NEO owner
            if (empty($neoBelongs)) {
                return false;
            }

            // Get NEO owner RIO information
            /** @var \App\Models\Rio */
            $neoOwnerRecipient = $neoBelongs->rio;

            if (NotificationRejectSetting::isRejectedEmail(
                $neoOwnerRecipient,
                MailTemplates::NETSHOP_PURCHASE
            )) {
                return false;
            }

            // Get email information
            $user = User::whereRioId($neoOwnerRecipient->id)->firstOrFail();

            // Send email to NEO receiver
            Notification::sendNow($user, new  NetshopPurchaseNotification($seller, $buyer, $product));
        }
    }
}
