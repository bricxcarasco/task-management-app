<?php

namespace App\Models;

use App\Enums\EntityType;
use App\Enums\ServiceSelectionTypes;
use App\Helpers\CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ServiceSetting
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property int $service_id
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read array $storage_info
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Rio|null $rio
 * @property-read \App\Models\Service $service
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting document()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|ServiceSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ServiceSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ServiceSetting withoutTrashed()
 */
class ServiceSetting extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'service_settings';

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
        'rio_id',
        'neo_id',
        'service_id',
        'data',
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
     * Define relationship with service model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Scope query for getting service setting info for current entity
     *
     * @param mixed $query
     * @param mixed $service
     * @return mixed
     */
    public function scopeServiceSettingInfo($query, $service)
    {
        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $query->whereRioId($service->data->id);
            default:
                return $query->whereNeoId($service->data->id);
        }
    }

    /**
     * Scope query for document management service
     *
     * @param mixed $query
     * @return mixed
     */
    public function scopeDocument($query)
    {
        $service = Service::whereRouteName('document')->firstOrFail();

        return $query->where('service_settings.service_id', $service->id);
    }

    /**
     * Get the storage info of entity
     *
     * @return array
     */
    public function getStorageInfoAttribute()
    {
        return json_decode($this->data ?? '', true);
    }

    /**
     * Create table record
     *
     * @param array $serviceInfo
     * @param \App\Models\Rio|\App\Models\Neo $entity
     * @param int|string $serviceId
     *
     * @return object
     */
    public static function createServiceSetting($serviceInfo, $entity, $serviceId)
    {
        // Identify entity type
        $entityType = CommonHelper::getEntityType($entity);
        $record = [
            'service_id' => $serviceId,
            'data' => json_encode($serviceInfo),
        ];

        switch ($entityType) {
            case EntityType::RIO:
                $record['rio_id'] = $entity->id;
                break;
            case EntityType::NEO:
                $record['neo_id'] = $entity->id;
                break;
            default:
                break;
        }

        return self::create($record);
    }
}
