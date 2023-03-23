<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BusinessCategory
 *
 * @property int $id id for Laravel
 * @property string $business_category_code
 * @property string $business_category_name
 * @property string $business_category_example
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property string|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @method static \Database\Factories\BusinessCategoryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessCategory whereBusinessCategoryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessCategory whereBusinessCategoryExample($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessCategory whereBusinessCategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BusinessCategory extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
}
