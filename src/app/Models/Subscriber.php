<?php

namespace App\Models;

use App\Enums\Classified\PaymentMethods;
use App\Enums\PaidPlan\StatusType;
use App\Enums\ServiceSelectionTypes;
use App\Objects\ServiceSelected;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Subscriber
 *
 * @property int $id
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property string $stripe_customer_id
 * @property int $plan_id
 * @property string $stripe_subscription_id
 * @property string $stripe_client_secret
 * @property int $status incomplete = 0, incomplete_expired = 1, trialing = 2, active = 3, past_due = 4, canceled or unpaid  = 5
 * @property string $start_date
 * @property string $end_date
 * @property int $payment_method 1 - CC/Stripe, 2 - Bank Transfer
 * @property int $total_price
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Plan $plan
 * @property-read \App\Models\Rio|null $rio
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber active()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber incompletePayment()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber newQuery()
 * @method static \Illuminate\Database\Query\Builder|Subscriber onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber serviceSelected($service = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber stripePayment()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber unexpired()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereStripeClientSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereStripeCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereStripeSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Subscriber withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Subscriber withoutTrashed()
 * @mixin \Eloquent
 */
class Subscriber extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscribers';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'rio_id',
        'neo_id',
        'stripe_customer_id',
        'plan_id',
        'stripe_subscription_id',
        'stripe_client_secret',
        'status',
        'start_date',
        'end_date',
        'payment_method',
        'total_price',
        'data'
    ];

    /**
     * Define relationship for RIO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class);
    }

    /**
     * Define relationship for NEO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class);
    }

    /**
     * Define relationship for plan model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Scope query for active subscriptions
     *
     * @param mixed $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('subscribers.status', StatusType::ACTIVE);
    }

    /**
     * Scope query for unexpired subscriptions
     *
     * @param mixed $query
     * @return mixed
     */
    public function scopeUnexpired($query)
    {
        return $query->where('subscribers.end_date', '>=', date('Y-m-d'));
    }

    /**
     * Scope query for pending subscriptions for payment
     *
     * @param mixed $query
     * @return mixed
     */
    public function scopeIncompletePayment($query)
    {
        return $query->where('subscribers.status', StatusType::INCOMPLETE)
            ->whereNotNull('subscribers.stripe_client_secret');
    }

    /**
     * Scope query for stripe payment subscribers
     *
     * @param mixed $query
     * @return mixed
     */
    public function scopeStripePayment($query)
    {
        return $query->where('subscribers.payment_method', PaymentMethods::CARD);
    }

    /**
     * Scope query for service selected
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param object|null $service
     * @return mixed
     */
    public function scopeServiceSelected($query, $service = null)
    {
        // Get service selected if unspecified
        if (empty($service)) {
            $service = ServiceSelected::getSelected();
        }

        // Appropriate condition for session type
        if ($service->type == ServiceSelectionTypes::NEO) {
            $query = $query->where('neo_id', $service->data->id);
        } else {
            $query = $query->where('rio_id', $service->data->id);
        }

        return $query;
    }
}
