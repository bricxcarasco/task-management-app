<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MailTemplate
 *
 * @property int $id id for Laravel
 * @property int $template_id
 * @property string $name
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property string|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @method static \Illuminate\Database\Eloquent\Builder|MailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailTemplate whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailTemplate whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MailTemplate extends Model
{
    use HasFactory;

    /**
     * Find mail template by template_id.
     *
     * @param int $id
     *
     * @return mixed
     */
    public static function findByTemplateId($id)
    {
        return self::where('template_id', $id)->first();
    }
}
