<?php

namespace App\Models;

use App\Enums\EntityType;
use App\Objects\ServiceSelected;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Plan
 *
 * @property int $id id for Laravel
 * @property string $name
 * @property int $entity_type 0: RIO, 1: NEO
 * @property string $stripe_price_id
 * @property int $price
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PlanService[] $plan_services
 * @property-read int|null $plan_services_count
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newQuery()
 * @method static \Illuminate\Database\Query\Builder|Plan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan serviceSelected($service = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereStripePriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Plan withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Plan withoutTrashed()
 * @mixin \Eloquent
 */
class Plan extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plans';

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
        'name',
        'entity_type',
        'stripe_price_id',
        'price',
    ];

    /**
     * Define relationship with plan service model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plan_services()
    {
        return $this->hasMany(PlanService::class, 'plan_id');
    }

    /**
     * Get the accessible routes of the plan
     *
     * @return array
     */
    public function getAccessibleRoutesAttribute()
    {
        return $this->plan_services()
            ->inclusion()
            ->serviceDetail()
            ->get()
            ->pluck('route_name')
            ->filter()
            ->map(function ($item, $key) {
                return $item . '.*';
            })
            ->toArray();
    }

    /**
     * Scope query for active service features
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $service
     * @return mixed
     */
    public function scopeServiceSelected($query, $service = null)
    {
        // Get service selected if unspecified
        if (empty($service)) {
            $service = ServiceSelected::getSelected();
        }

        // Handle entity type enum
        $entityType = EntityType::fromKey($service->type);

        return $query->where('plans.entity_type', $entityType->value);
    }

    /**
     * Scope query for free plans
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function scopeFree($query)
    {
        return $query->where('plans.price', 0);
    }
}
