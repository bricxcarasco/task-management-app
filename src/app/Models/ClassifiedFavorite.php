<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ClassifiedFavorite
 *
 * @property int $id id for Laravel
 * @property int $classified_sale_id
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property int $created_rio_id
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\ClassifiedSale $classified_sale
 * @property-read \App\Models\Rio $created_rio
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Rio|null $rio
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedFavorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedFavorite newQuery()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedFavorite onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedFavorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedFavorite whereClassifiedSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedFavorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedFavorite whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedFavorite whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedFavorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedFavorite whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedFavorite whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedFavorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ClassifiedFavorite withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedFavorite withoutTrashed()
 * @mixin \Eloquent
 */
class ClassifiedFavorite extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classified_favorites';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'classified_sale_id',
        'rio_id',
        'neo_id',
        'created_rio_id',
    ];

    /**
     * Define relationship for Classied sales
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classified_sale()
    {
        return $this->belongsTo(ClassifiedSale::class, 'classified_sale_id');
    }

    /**
     * Define relationship for RIO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class, 'rio_id');
    }

    /**
     * Define relationship for NEO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class, 'neo_id');
    }

    /**
     * Define relationship for RIO creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_rio()
    {
        return $this->belongsTo(Rio::class, 'created_rio_id');
    }
}
