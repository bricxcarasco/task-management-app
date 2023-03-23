<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\NeoExpert
 *
 * @property int $id id for Laravel
 * @property int $neo_id
 * @property string $attribute_code ※ここではIDでなくコードを使用する。
 * @property int $sort データの並び順を設定
 * @property int|null $business_category_id attribute_codeが"experience"の場合に使用
 * @property string $content
 * @property string|null $additional
 * @property string|null $information json形式で記録
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert newQuery()
 * @method static \Illuminate\Database\Query\Builder|NeoExpert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert query()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert whereAdditional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert whereAttributeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert whereBusinessCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert whereInformation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoExpert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NeoExpert withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NeoExpert withoutTrashed()
 * @mixin \Eloquent
 */
class NeoExpert extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'neo_experts';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'neo_id',
        'attribute_code',
        'sort',
        'business_category_id',
        'content',
        'additional',
        'information',
    ];

    /**
     * Define relationship with neo model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class);
    }

    /**
     * Get the reference url attribute
     *
     * @return string
     */
    public function getReferenceUrlAttribute()
    {
        $reference_url = "";
        if ($this->information) {
            $information = json_decode($this->information);
            if (isset($information->reference_url) && !empty($information->reference_url)) {
                $reference_url = $information->reference_url;
            }
        }

        return $reference_url;
    }

    /**
     * Get the image link attribute
     *
     * @return string
     */
    public function getImageLinkAttribute()
    {
        $image_link = "";
        if ($this->information) {
            $information = json_decode($this->information);
            if (isset($information->image_link) && !empty($information->image_link)) {
                $image_link = $information->image_link;
            }
        }

        return $image_link;
    }
}
