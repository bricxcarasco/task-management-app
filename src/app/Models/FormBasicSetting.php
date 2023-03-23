<?php

namespace App\Models;

use App\Enums\PrefectureTypes;
use App\Enums\ServiceSelectionTypes;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Objects\ServiceSelected;

/**
 * App\Models\FormBasicSetting
 *
 * @property int $id id for Laravel
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property int $created_rio_id
 * @property string $name
 * @property string|null $department_name
 * @property string|null $address
 * @property string|null $tel
 * @property string|null $fax
 * @property string|null $business_number
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Rio $created_rio
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Rio|null $rio
 * @property-read array $payment_term_list
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|FormBasicSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereBusinessNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereDepartmentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|FormBasicSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FormBasicSetting withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting currentEntityServiceSetting()
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting serviceSetting($service)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting wherePaymentTermsOne($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting wherePaymentTermsThree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting wherePaymentTermsTwo($value)
 * @mixin \Eloquent
 * @property string|null $payment_terms_one
 * @property string|null $payment_terms_two
 * @property string|null $payment_terms_three
 * @property-read array $payment_term_list
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting currentEntityServiceSetting()
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting serviceSetting($service)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting wherePaymentTermsOne($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting wherePaymentTermsThree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormBasicSetting wherePaymentTermsTwo($value)
 */
class FormBasicSetting extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_basic_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rio_id',
        'neo_id',
        'created_rio_id',
        'name',
        'department_name',
        'address',
        'tel',
        'fax',
        'business_number',
        'payment_terms_one',
        'payment_terms_two',
        'payment_terms_three',
        'image',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'payment_term_list'
    ];

    /**
     * Define relationship with RIO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class, 'rio_id');
    }

    /**
     * Define relationship with NEO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class, 'neo_id');
    }

    /**
     * Define relationship with RIO creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_rio()
    {
        return $this->belongsTo(Rio::class, 'created_rio_id');
    }

    /**
     * Scope a query to get basic setting of the current enity service selected.
     *
     * @param \Illuminate\Database\Eloquent\Builder<FormBasicSetting> $query
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public static function scopeCurrentEntityServiceSetting($query)
    {
        $service = ServiceSelected::getSelected();

        if ($service->type === ServiceSelectionTypes::RIO) {
            return $query->where('rio_id', $service->data->id);
        }

        if ($service->type === ServiceSelectionTypes::NEO) {
            return $query->where('neo_id', $service->data->id);
        }

        return null;
    }

    /**
     * Scope a query to get basic setting of the current service selected.
     *
     * @param \Illuminate\Database\Eloquent\Builder<FormBasicSetting> $query
     * @param object $service
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public static function scopeServiceSetting($query, $service)
    {
        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $query->where('form_basic_settings.rio_id', $service->data->id);
            case ServiceSelectionTypes::NEO:
                return $query->where('form_basic_settings.neo_id', $service->data->id);
            default:
                return null;
        }
    }

    /**
     * Get basic setting from the RIO/NEO information.
     *
     * @param object $service
     * @return object|null
     */
    public static function getSettingFromRioNeo($service)
    {
        $defaultProfileImage = config('app.url') . '/' . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . '/' . 'storage/' . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . '/' . 'storage/' . config('bphero.neo_profile_image');

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                /** @var \App\Models\Rio */
                $rio = Rio::whereId($service->data->id)->first();

                /** @var \App\Models\RioProfile */
                $profile = $rio->rio_profile;

                $image = !empty($profile->profile_photo)
                    ? $rioProfileImagePath . $rio->id . '/' . $profile->profile_photo
                    : $defaultProfileImage;

                $address = !empty($profile->present_address_nationality)
                    ? $profile->present_address_nationality
                    : PrefectureTypes::getDescription($profile->present_address_prefecture) . $profile->present_address_city . $profile->present_address . $profile->present_address_building;

                return (object) [
                    'name' => $rio->family_name . ' ' . $rio->first_name,
                    'department_name' => '',
                    'address' => $address,
                    'tel' => $rio->tel,
                    'fax' => '',
                    'business_number' => '',
                    'image' => $image,
                ];
            case ServiceSelectionTypes::NEO:
                /** @var \App\Models\Neo */
                $neo = Neo::whereId($service->data->id)->first();

                /** @var \App\Models\NeoProfile */
                $profile = $neo->neo_profile;

                $image = !empty($profile->profile_photo)
                    ? $neoProfileImagePath . $neo->id . '/' . $profile->profile_photo
                    : $defaultProfileImage;

                $address = !empty($profile->nationality)
                    ? $profile->nationality
                    : PrefectureTypes::getDescription($profile->prefecture) . $profile->city . $profile->address . $profile->building;

                return (object) [
                    'name' => $neo->organization_name,
                    'department_name' => '',
                    'address' => $address,
                    'tel' => $neo->tel,
                    'fax' => '',
                    'business_number' => '',
                    'image' => $image,
                ];
            default:
                return null;
        }
    }

    /**
     * Get service profile photo.
     *
     * @param object $service
     * @return string|null
     */
    public static function getServiceProfilePhoto($service)
    {
        $rioProfileImagePath = config('app.url') . '/' . 'storage/' . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . '/' . 'storage/' . config('bphero.neo_profile_image');

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                /** @var \App\Models\Rio */
                $rio = Rio::whereId($service->data->id)->first();

                /** @var \App\Models\RioProfile */
                $profile = $rio->rio_profile;

                return !empty($profile->profile_photo)
                    ? $rioProfileImagePath . $rio->id . '/' . $profile->profile_photo
                    : null;
            case ServiceSelectionTypes::NEO:
                /** @var \App\Models\Neo */
                $neo = Neo::whereId($service->data->id)->first();

                /** @var \App\Models\NeoProfile */
                $profile = $neo->neo_profile;

                return !empty($profile->profile_photo)
                    ? $neoProfileImagePath . $neo->id . '/' . $profile->profile_photo
                    : null;
            default:
                return null;
        }
    }

    /**
     * Get the payment terms of form basic settings
     *
     * @return array
     */
    public function getPaymentTermListAttribute()
    {
        $paymentTerms = [];

        if (!is_null($this->payment_terms_one)) {
            array_push($paymentTerms, $this->payment_terms_one);
        }

        if (!is_null($this->payment_terms_two)) {
            array_push($paymentTerms, $this->payment_terms_two);
        }

        if (!is_null($this->payment_terms_three)) {
            array_push($paymentTerms, $this->payment_terms_three);
        }

        return $paymentTerms;
    }
}
