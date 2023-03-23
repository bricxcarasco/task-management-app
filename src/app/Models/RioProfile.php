<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\RioProfile
 *
 * @property int $id id for Laravel
 * @property int $rio_id
 * @property string|null $profilePhoto
 * @property string|null $self_introduce
 * @property int $business_use 0-off, 1-on
 * @property string|null $present_address_nationality
 * @property string|null $present_address_prefecture
 * @property string|null $present_address_city
 * @property string|null $present_address
 * @property string|null $present_address_building
 * @property string|null $home_address_nationality
 * @property string|null $home_address_prefecture
 * @property string|null $home_address_city
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read string $profile_image
 * @property-read \App\Models\Rio $rio
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile newQuery()
 * @method static \Illuminate\Database\Query\Builder|RioProfile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile whereBusinessUse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile whereHomeAddressCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile whereHomeAddressNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile whereHomeAddressPrefecture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile wherePresentAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile wherePresentAddressBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile wherePresentAddressCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile wherePresentAddressNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile wherePresentAddressPrefecture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile whereSelfIntroduce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RioProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|RioProfile withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RioProfile withoutTrashed()
 * @mixin \Eloquent
 */
class RioProfile extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rio_profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rio_id',
        'profile_photo',
        'self_introduce',
        'business_use',
        'present_address_nationality',
        'present_address_prefecture',
        'present_address_city',
        'present_address',
        'present_address_building',
        'home_address_nationality',
        'home_address_prefecture',
        'home_address_city',
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
     * Define relationship with rio model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class);
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
            $rioProfileImagePath = config('bphero.rio_profile_image');
            $profilePhoto = "storage/${rioProfileImagePath}" . $this->rio_id . "/" . $this->profile_photo;
        }

        return asset($profilePhoto);
    }
}
