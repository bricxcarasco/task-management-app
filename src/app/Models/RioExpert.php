<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\RioExpert
 *
 * @property int $id id for Laravel
 * @property int $rio_id
 * @property string $attribute_code ※ここではIDでなくコードを使用する。
 * @property int $sort データの並び順を設定
 * @property int|null $business_category_id attribute_codeが"experience"の場合に使用
 * @property string $content
 * @property string|null $additional
 * @property string|null $information json形式で記録
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property string|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Rio $rio
 * @property-read \App\Models\BusinessCategory|null $businessCategory
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert query()
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert whereAdditional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert whereAttributeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert whereBusinessCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert whereInformation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioExpert whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RioExpert extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "rio_experts";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rio_id',
        'attribute_code',
        'sort',
        'business_category_id',
        'content',
        'additional',
        'information',
    ];

    /**
     * Define relationship with rio model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class);
    }

    /**
     * Define relationship with business category model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function businessCategory()
    {
        return $this->belongsTo(BusinessCategory::class);
    }

    /**
     * Get in-depth additional attribute
     *
     * @param string|null $value
     * @return string|null
     */
    public function getInformationAttribute($value)
    {
        // Guard clause for nulled information
        if (is_null($value)) {
            return $value;
        }

        // JSON Decode information
        $json = json_decode($value);

        // Return JSON decoded value
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        return $value;
    }
}
