<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubscriptionHistory
 *
 * @property int $id id for Laravel
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property int $total_price
 * @property string $data
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property string|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Rio|null $rio
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionHistory whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionHistory whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionHistory whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionHistory whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubscriptionHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscription_histories';

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
        'total_price',
        'data'
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
}
