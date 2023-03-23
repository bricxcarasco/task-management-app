<?php

namespace App\Models;

use App\Enums\PaidPlan\StatusType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Subscription
 *
 * @property int $id id for Laravel
 * @property int $subscriber_id
 * @property int $plan_service_id
 * @property string $stripe_subscription_id
 * @property string $start_date
 * @property string $end_date
 * @property int $quantity
 * @property int $status incomplete = 0, incomplete_expired = 1, trialing = 2, active = 3, past_due = 4, canceled or unpaid  = 5
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property string|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\PlanService|null $plan_services
 * @property-read \App\Models\Subscriber $subscriber
 * @property-read string $unit
 * @property-read int $value
 * @property-read string $total_quantity
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription active()
 * @method static \Database\Factories\SubscriptionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription unexpired()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription wherePlanServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStripeSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscriptions';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscriber_id',
        'plan_service_id',
        'stripe_subscription_id',
        'start_date',
        'end_date',
        'quantity',
        'status',
    ];

    /**
     * Define relationship with subscriber model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class, 'subscriber_id');
    }

    /**
     * Define relationship with plan services model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function plan_services()
    {
        return $this->hasOne(PlanService::class, 'plan_service_id');
    }

    /**
     * Scope query for active subscriptions
     *
     * @param mixed $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('subscriptions.status', StatusType::ACTIVE);
    }

    /**
     * Scope query for unexpired subscriptions
     *
     * @param mixed $query
     * @return mixed
     */
    public function scopeUnexpired($query)
    {
        return $query->where('subscriptions.end_date', '>=', date('Y-m-d'));
    }

    /**
     * Scope query for list of subscriptions
     *
     * @param mixed $query
     * @param int $subscriberId
     * @return mixed
     */
    public function scopeSubscriberList($query, $subscriberId)
    {
        return $query->where('subscriptions.subscriber_id', $subscriberId);
    }
}
