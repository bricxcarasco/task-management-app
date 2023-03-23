<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\NeoProfile
 *
 * @property int $id id for Laravel
 * @property int $neo_id
 * @property string|null $nationality
 * @property string|null $prefecture
 * @property string|null $city
 * @property string|null $address
 * @property string|null $building
 * @property string|null $profilePhoto
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile newQuery()
 * @method static \Illuminate\Database\Query\Builder|NeoProfile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile whereBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile whereNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile wherePrefecture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NeoProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NeoProfile withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NeoProfile withoutTrashed()
 * @mixin \Eloquent
 */
class NeoProfile extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'neo_profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'neo_id',
        'nationality',
        'prefecture',
        'city',
        'address',
        'building',
        'self_introduce',
        'profile_video',
        'overseas_support'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_image',
    ];

    /**
     * Neo profile instance belongs to neo model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class);
    }

    /**
     * Get the profile image attribute
     *
     * @return string
     */
    public function getProfileImageAttribute()
    {
        $profilePhoto = config('bphero.profile_image_directory') . config('bphero.profile_image_filename');

        if (!is_null($this->profile_photo)) {
            $neoProfileImagePath = config('bphero.neo_profile_image');
            $profilePhoto = "storage/${neoProfileImagePath}" . $this->neo_id . "/" . $this->profile_photo;
        }

        return asset($profilePhoto);
    }
}
