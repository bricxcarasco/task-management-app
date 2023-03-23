<?php

namespace App\Models;

use App\Enums\PlanServiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PlanService
 *
 * @property int $id id for Laravel
 * @property int $plan_id
 * @property int $service_id
 * @property int $type 1: Plan Inclusion, 2: Plan Additional/Option
 * @property string|null $stripe_price_id
 * @property int $value
 * @property string|null $unit
 * @property int|null $price
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService newQuery()
 * @method static \Illuminate\Database\Query\Builder|PlanService onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService whereStripePriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanService whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|PlanService withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PlanService withoutTrashed()
 * @mixin \Eloquent
 */
class PlanService extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_services';

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
        'plan_id',
        'service_id',
        'type',
        'stripe_price_id',
        'value',
        'unit',
        'price',
        'description',
    ];

    /**
     * Define relationship with plan model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Define relationship with service model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Scope query for list of plan service
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $id
     * @return mixed
     */
    public function scopeList($query, $id)
    {
        return $query->whereIn('plan_services.id', $id);
    }

    /**
     * Scope query for inclusions
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeInclusion($query)
    {
        return $query->where('plan_services.type', PlanServiceType::PLAN);
    }

    /**
     * Scope query for options
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeOption($query)
    {
        return $query->where('plan_services.type', PlanServiceType::OPTION);
    }

    /**
     * Scope query for service detail
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeServiceDetail($query)
    {
        /** @var \Illuminate\Database\Query\Builder */
        $serviceQuery = Service::query()
            ->active();

        return $query
            ->select([
                'plan_services.*',
                'services.name',
                'services.route_name',
            ])
            ->joinSub($serviceQuery, 'services', 'plan_services.service_id', '=', 'services.id');
    }
}
