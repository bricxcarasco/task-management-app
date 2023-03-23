<?php

namespace App\Models;

use App\Enums\Classified\PaymentMethods;
use App\Enums\ServiceSelectionTypes;
use App\Objects\ServiceSelected;
use App\Services\StripeService;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ClassifiedSetting
 *
 * @property int $id id for Laravel
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property int $created_rio_id
 * @property string $settings_by_card （Stripe Connectの詳細確認中のためWIP）
 * @property string $settings_by_transfer JSON形式で3件まで振込先口座を登録可能
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Rio $created_rio
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Rio|null $rio
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting whereSettingsByCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting whereSetttingsByTransfer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ClassifiedSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedSetting withoutTrashed()
 * @mixin \Eloquent
 */
class ClassifiedSetting extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classified_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rio_id',
        'neo_id',
        'created_rio_id',
        'settings_by_card',
        'settings_by_transfer',
    ];

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

    /**
     * Scope a query to get setting information.
     *
     * @param \Illuminate\Database\Eloquent\Builder<ClassifiedSetting> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeSetting($query)
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $query->where('classified_settings.rio_id', $service->data->id);
            case ServiceSelectionTypes::NEO:
                return $query->where('classified_settings.neo_id', $service->data->id);
            default:
                return $query;
        }
    }

    /**
     * Scope a query to get card payment setting information.
     *
     * @param \Illuminate\Database\Eloquent\Builder<ClassifiedSetting> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeCardPaymentSetting($query)
    {
        return $query
            ->setting()
            ->whereNotNull('classified_settings.settings_by_card');
    }

    /**
     * Scope a query to get account transfer setting information.
     *
     * @param \Illuminate\Database\Eloquent\Builder<ClassifiedSetting> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeAccountTransferSetting($query)
    {
        return $query
            ->setting()
            ->whereNotNull('classified_settings.settings_by_transfer');
    }

    /**
     * Get service available card Stripe settings.
     *
     * @param \App\Models\ClassifiedSetting|null $setting
     * @return array
     */
    public static function getCardSettings($setting = null)
    {
        return StripeService::getStripeInfo($setting);
    }

    /**
     * Get service available bank accounts.
     *
     * @param \App\Models\ClassifiedSetting|null $setting
     * @return array
     */
    public static function getBankAccounts($setting = null)
    {
        if (empty($setting)) {
            // Get setting based on selected service
            $setting = self::accountTransferSetting()->first();
        }

        // Guard clause if no bank accounts
        if (empty($setting)) {
            return [];
        }

        // Guard clause if empty bank accounts
        if (empty($setting->settings_by_transfer)) {
            return [];
        }

        // Get bank accounts
        $accounts = json_decode($setting->settings_by_transfer, true);

        foreach ($accounts as $key => $account) {
            $accounts[$key] = json_decode($account, true);
        }

        return $accounts;
    }

    /**
     * Get list of service available settings.
     *
     * @return array
     */
    public static function getSelectableSettings()
    {
        // Get settings based on selected service
        $settings = self::setting()->first();

        // Initialize list
        $list = [];

        if (empty($settings)) {
            return $list;
        }

        if (!empty($settings->settings_by_card)) {
            $cardSettings = json_decode($settings->settings_by_card, true);

            if ($cardSettings['is_completed']) {
                $key = PaymentMethods::CARD;
                $list[$key] = PaymentMethods::getDescription($key);
            }
        }

        if (!empty($settings->settings_by_transfer)) {
            $key = PaymentMethods::TRANSFER;
            $list[$key] = PaymentMethods::getDescription($key);
        }

        return $list;
    }

    /**
     * Set a Stripe Card setting setup to Pending status.
     *
     * @param bool $pendingStatus
     * @return void
     */
    public static function pendingStripeSetup($pendingStatus)
    {
        // Get card payment setting information
        /** @var ClassifiedSetting */
        $cardSetting = ClassifiedSetting::cardPaymentSetting()->first();

        // Get stripe card setting information
        $stripeInfo = StripeService::getStripeInfo($cardSetting);
        $stripeInfo['is_pending'] = $pendingStatus;

        /** @var string */
        $settingsByCard = json_encode($stripeInfo, JSON_FORCE_OBJECT);

        // Update card payment setting
        $cardSetting->update([
            'settings_by_card' => $settingsByCard
        ]);
    }
}
