<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ClassifiedSaleCategory
 *
 * @property int $id id for Laravel
 * @property string $sale_category_name
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSaleCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSaleCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedSaleCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSaleCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSaleCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSaleCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSaleCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSaleCategory whereSaleCategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSaleCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ClassifiedSaleCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedSaleCategory withoutTrashed()
 * @mixin \Eloquent
 */
class ClassifiedSaleCategory extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classified_sale_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sale_category_name',
    ];
}
