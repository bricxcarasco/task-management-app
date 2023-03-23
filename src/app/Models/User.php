<?php

namespace App\Models;

use App\Notifications\ChangePasswordNotification;
use App\Notifications\RegistrationVerifiedNotification;
use App\Notifications\ResetEmailVerificationNotification;
use App\Services\FcmService;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id id for Laravel
 * @property int $rio_id
 * @property string|null $username
 * @property string $email
 * @property string $password
 * @property string|null $remember_token ログイン成功時、0リセット(0 reset upon successful login)
 * @property int $login_failed 0=アクティブ(active)、1=ロック(locked)
 * @property int $lock
 * @property string|null $last_login
 * @property string|null $secret_question
 * @property string|null $secret_answer
 * @property int $two_factor_authentication 0=inactive, 1=email, 2=message
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property string|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Rio $rio
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLoginFailed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSecretAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSecretQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorAuthentication($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use ModelUpdatedTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'rio_id',
        'username',
        'email',
        'password',
        'secret_question',
        'secret_answer',
        'last_login',
        'feature_description',
        'affiliate',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'feature_description' => 'boolean',
    ];

    /**
     * User instance belongs to rio model.
     *
     * @return mixed
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class);
    }

    /**
     * Send mail notification trigger to registered user.
     *
     * @param object $user
     * @return void
     */
    public function sendRegistrationVerifiedNotification($user)
    {
        $this->notifyNow(new RegistrationVerifiedNotification($user));
    }

    /**
     * Send reset email notification.
     *
     * @param \App\Models\UserVerification $verification
     * @return void
     */
    public function sendResetEmailVerificationNotification($verification)
    {
        $this->notifyNow(new ResetEmailVerificationNotification($verification));
    }

    /**
     * Send change password notification.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function sendChangePasswordNotification($user)
    {
        $this->notifyNow(new ChangePasswordNotification($user));
    }

    /**
     * Specifies the user's FCM tokens
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return $this->fcm_tokens;
    }

    /**
     * Set FCM token
     *
     * @param string $token
     * @return void
     */
    public function setFcmToken($token)
    {
        // Initialize array
        $tokens = [];

        // Fetch tokens from database
        if (!empty($this->fcm_token)) {
            $tokens = json_decode($this->fcm_token, true);
        }

        // Push new token to existing array
        if (!in_array($token, $tokens)) {
            $tokens[] = $token;
        }

        // Encode and save to database
        $this->update([
            'fcm_token' => json_encode($tokens),
        ]);

        // Save token as a cookie
        FcmService::setCookie($this->id, $token);
    }

    /**
     * Remove FCM token
     *
     * @param string $token
     * @return void
     */
    public function removeFcmToken($token)
    {
        // Fetch tokens from database
        if (empty($this->fcm_token)) {
            return;
        }

        // Decode tokens to an array
        $tokens = json_decode($this->fcm_token, true);

        // Get array key for token to be removed
        $targetKey = array_search($token, $tokens);

        // Disregard when nothing to removed
        if ($targetKey === false) {
            return;
        }

        // Removed from array
        unset($tokens[$targetKey]);

        // Prepare token value to be saved
        $value = !empty($tokens)
            ? json_encode($tokens)
            : null;

        // Encode and save to database
        $this->update([
            'fcm_token' => $value,
        ]);
    }

    /**
     * Get fcm tokens attribute
     *
     * @return array
     */
    public function getFcmTokensAttribute()
    {
        // Fetch tokens from database
        if (empty($this->fcm_token)) {
            return [];
        }

        // Decode tokens to an array
        return json_decode($this->fcm_token, true);
    }
}
