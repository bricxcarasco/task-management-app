<?php

namespace App\Models;

use App\Enums\ServiceStatusType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Service
 *
 * @property int $id id for Laravel
 * @property string $name
 * @property string|null $route_name
 * @property int $status 0: inactive, 1: active
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PlanService[] $plan_services
 * @property-read int|null $plan_services_count
 * @method static \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|Service active()
 * @method static \Illuminate\Database\Eloquent\Builder|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Service newQuery()
 * @method static \Illuminate\Database\Query\Builder|Service onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereRouteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Service withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Service withoutTrashed()
 * @mixin \Eloquent
 */
class Service extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'services';

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
        'route_name',
        'status',
    ];

    /**
     * Define relationship with plan service model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plan_services()
    {
        return $this->hasMany(PlanService::class, 'service_id');
    }

    /**
     * Define relationship with service settings model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service_settings()
    {
        return $this->hasMany(ServiceSetting::class, 'service_id');
    }

    /**
     * Scope query for active service features
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function scopeActive($query)
    {
        return $query->where('services.status', ServiceStatusType::ACTIVE);
    }
}
