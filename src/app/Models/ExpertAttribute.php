<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExpertAttribute
 *
 * @property int $id id for Laravel
 * @property string $attribute_code
 * @property string $attribute_name
 * @property int $is_searchable 0: 検索対象としない、1:検索対象とする
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property string|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @method static \Database\Factories\ExpertAttributeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertAttribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertAttribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertAttribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertAttribute whereAttributeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertAttribute whereAttributeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertAttribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertAttribute whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertAttribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertAttribute whereIsSearchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertAttribute whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExpertAttribute extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;

    /**
     * Fetch expert attribute by code
     *
     * @param mixed $code
     * @return mixed
     */
    public static function findByCode($code)
    {
        return self::where('attribute_code', $code)->first();
    }
}
