<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\NotificationRejectSetting
 *
 * @property int $id id for Laravel
 * @property int $rio_id
 * @property string|null $mail_template_id Save multiple mail template ID (Ex: 4,5,7,)
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationRejectSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationRejectSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|NotificationRejectSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationRejectSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationRejectSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationRejectSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationRejectSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationRejectSetting whereMailTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationRejectSetting whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationRejectSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NotificationRejectSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NotificationRejectSetting withoutTrashed()
 * @mixin \Eloquent
 */
class NotificationRejectSetting extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_reject_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rio_id',
        'mail_template_id',
    ];

    /**
     * Check if rejected or NOT allowed to send receive email
     *
     * @param \App\Models\Rio $rio
     * @param int $templateId
     * @return bool
     */
    public static function isRejectedEmail($rio, $templateId)
    {
        // Get rejected notification settings
        $settings = self::whereRioId($rio->id)->first();

        // If no rejected notification settings, do not reject
        if (empty($settings)) {
            return false;
        }

        // If rejected notification setting template IDs is null, do not reject
        if (empty($settings->mail_template_id)) {
            return false;
        }

        // Convert rejected templates to array list
        $rejectedList = explode(',', $settings->mail_template_id);

        return in_array($templateId, $rejectedList);
    }
}
