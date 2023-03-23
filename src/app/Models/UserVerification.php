<?php

namespace App\Models;

use App\Notifications\SignupEmailVerificationNotification;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Str;

/**
 * App\Models\UserVerification
 *
 * @property int $id id for Laravel
 * @property string $email
 * @property string $token ランダムなハッシュ値を取得しメールに付与。一致する場合に会員登録画面を開く
 * @property string $expiration_datetime 会員登録画面が開かれる時点でこのテーブルのうち、登録期限日時を超えたレコードは物理削除を行う
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|UserVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserVerification query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserVerification whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserVerification whereExpirationDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserVerification whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserVerification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserVerification extends Model
{
    use HasFactory;
    use Notifiable;
    use ModelUpdatedTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'email',
        'token',
        'expiration_datetime',
        'referral_rio_id',
        'affi_moshimo_code',
        'affi_a8_code'
    ];

    /**
     * Generates verification record from email passed
     *
     * @param string $email
     * @return \App\Models\UserVerification
     */
    public static function createEmailVerify($email)
    {
        return static::create([
            'email' => $email,
            'token' => Str::random(config('bphero.email_verification_token_length')),
            'expiration_datetime' => now()->addDay(),
        ]);
    }

    /**
     * send notification to user_verification instance.
     *
     * @param UserVerification $user
     * @return void
     */
    public function sendSignupEmailVerificationNotification($user)
    {
        $this->notifyNow(new SignupEmailVerificationNotification($user));
    }

    /**
     * After registration delete user_verification relaed email.
     *
     * @param string $email
     *
     * @return mixed
     */
    public static function deleteUserVerificationByEmail($email)
    {
        return self::where('email', $email)->delete();
    }

    /**
     * Scope a query to only include active or not expired verification records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('user_verifications.expiration_datetime', '>', now());
    }
}
