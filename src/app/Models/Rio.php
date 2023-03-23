<?php

namespace App\Models;

use App\Enums\AttributeCodes;
use App\Enums\ConnectionInclusion;
use App\Enums\ConnectionStatuses;
use App\Enums\Neo\AcceptBelongType;
use App\Enums\Neo\AcceptConnectionType;
use App\Enums\ConnectionStatusType;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enums\ServiceSelectionTypes;
use App\Enums\NeoBelongStatuses;
use App\Enums\Neo\RoleType;

/**
 * App\Models\Rio
 *
 * @property int $id id for Laravel
 * @property string $family_name
 * @property string $first_name
 * @property string $family_kana
 * @property string $first_kana
 * @property string $birth_date
 * @property string $gender
 * @property string $tel fill in user's living country if user not living in Japan
 * @property string $referral_code
 * @property int|null $referral_rio_id
 * @property string|null $referral_message_template
 * @property string|null $google_token
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RioExpert[] $awards
 * @property-read int|null $awards_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Chat[] $chats
 * @property-read int|null $chats_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RioExpert[] $educational_backgrounds
 * @property-read int|null $educational_backgrounds_count
 * @property-read string|null $full_name
 * @property-read string|null $full_name_kana
 * @property-read string $profile_photo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupConnection[] $group_connections
 * @property-read int|null $group_connections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RioExpert[] $industries
 * @property-read int|null $industries_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Neo[] $neos
 * @property-read int|null $neos_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RioExpert[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RioExpert[] $professions
 * @property-read int|null $professions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RioExpert[] $qualifications
 * @property-read int|null $qualifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RioConnectionUser[] $rio_connection_users
 * @property-read int|null $rio_connection_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RioConnection[] $rio_connections
 * @property-read int|null $rio_connections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RioConnection[] $rio_connections_affiliated
 * @property-read int|null $rio_connections_affiliated_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RioExpert[] $rio_expert
 * @property-read int|null $rio_expert_count
 * @property-read \App\Models\RioPrivacy|null $rio_privacy
 * @property-read \App\Models\RioProfile|null $rio_profile
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RioExpert[] $skills
 * @property-read int|null $skills_count
 * @property-read \App\Models\Subscriber|null $subscriber
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Rio commonConditions($options)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio connectedRio($rioId)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio connectionGroupInviteMembers($groupId, $rioId)
 * @method static \Database\Factories\RioFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rio newQuery()
 * @method static \Illuminate\Database\Query\Builder|Rio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Rio query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rio search($options, $service)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereFamilyKana($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereFamilyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereFirstKana($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereGoogleToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereReferralMessageTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereReferralRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Rio withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Rio withoutTrashed()
 * @mixin \Eloquent
 */
class Rio extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'family_name',
        'first_name',
        'family_kana',
        'first_kana',
        'birth_date',
        'gender',
        'tel',
        'referral_code',
        'referral_rio_id',
        'referral_message_template',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_name',
        'full_name_kana',
    ];

    /**
     * Define relationship with subscriptions model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscriber::class);
    }

    /**
     * Define relationship with users model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Define relationship with subscriber model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscriber()
    {
        return $this->hasOne(Subscriber::class);
    }

    /**
     * Define relationship with rio profiles model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rio_profile()
    {
        return $this->hasOne(RioProfile::class);
    }

    /**
     * Define relationship with rio privacies model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rio_privacy()
    {
        return $this->hasOne(RioPrivacy::class);
    }

    /**
     * Define relationship with rio privacies model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rio_connections()
    {
        return $this->hasMany(RioConnection::class, 'created_rio_id');
    }

    /**
     * Define relationship with rio privacies model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rio_connections_affiliated()
    {
        return $this->hasMany(RioConnection::class, 'created_rio_id')
            ->whereStatus(ConnectionStatusType::AFFILIATED);
    }

    /**
     * Define relationship with rio connection users model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rio_connection_users()
    {
        return $this->hasMany(RioConnectionUser::class);
    }

    /**
     * Define relationship with rio experts model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rio_expert()
    {
        return $this->hasMany(RioExpert::class);
    }

    /**
     * Define relationship with neo model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function neos()
    {
        return $this->belongsToMany(Neo::class, 'neo_belongs')
            ->as('neo_belongs')
            ->withPivot([
                'status',
                'is_display',
                'role',
            ])
            ->wherePivotNull('deleted_at');
    }

    /**
     * Define relationship with group_connections model with group connection users
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function group_connections()
    {
        return $this->hasMany(GroupConnection::class)
            ->with('users');
    }

    /**
     * Define relationship with rio experts model with profession attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function professions()
    {
        return $this->hasMany(RioExpert::class)
            ->whereAttributeCode(AttributeCodes::PROFESSION);
    }

    /**
     * Define relationship with rio experts model with industry attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function industries()
    {
        return $this->hasMany(RioExpert::class)
            ->whereAttributeCode(AttributeCodes::INDUSTRY);
    }

    /**
     * Define relationship with rio experts model with educational_background attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function educational_backgrounds()
    {
        return $this->hasMany(RioExpert::class)
            ->whereAttributeCode(AttributeCodes::EDUCATIONAL_BACKGROUND);
    }

    /**
     * Define relationship with rio experts model with awards attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function awards()
    {
        return $this->hasMany(RioExpert::class)
            ->whereAttributeCode(AttributeCodes::AWARDS);
    }

    /**
     * Define relationship with rio experts model with qualification attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qualifications()
    {
        return $this->hasMany(RioExpert::class)
            ->whereAttributeCode(AttributeCodes::QUALIFICATIONS);
    }

    /**
     * Define relationship with rio experts model with skills attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skills()
    {
        return $this->hasMany(RioExpert::class)
            ->whereAttributeCode(AttributeCodes::SKILLS);
    }

    /**
     * Define relationship with rio experts model with product_service_information attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(RioExpert::class)
            ->whereAttributeCode(AttributeCodes::PRODUCT_SERVICE_INFORMATION);
    }

    /**
     * Define relationship with service settings model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service_settings()
    {
        return $this->hasMany(ServiceSetting::class);
    }

    /**
     * Get full name attribute
     *
     * @return string|null
     */
    public function getFullNameAttribute()
    {
        return "{$this->family_name} {$this->first_name}";
    }

    /**
     * Get full name kana attribute
     *
     * @return string|null
     */
    public function getFullNameKanaAttribute()
    {
        return "{$this->family_kana} {$this->first_kana}";
    }

    /**
     * Define relationship with chats
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chats()
    {
        return $this->hasMany(Chat::class, 'owner_rio_id');
    }

    /**
     * Scope a query of rios connected to specified rio
     *
     * @param \Illuminate\Database\Eloquent\Builder<Rio> $query
     * @param int $rioId Target RIO user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeConnectedRio($query, $rioId)
    {
        /**
         * Subquery for fetching affiliated connection ids of target rio
         *
         * Outputs columns:
         * `rio_connection_id`
         */
        $connectionIdsQuery = DB::table('rio_connection_users AS connection_ids')
            ->select(
                'connection_ids.rio_connection_id',
            )
            ->leftJoin('rio_connections', 'rio_connections.id', '=', 'connection_ids.rio_connection_id')
            ->where('connection_ids.rio_id', $rioId)
            ->where('rio_connections.status', ConnectionStatusType::AFFILIATED)
            ->whereNull('connection_ids.deleted_at')
            ->groupBy('connection_ids.rio_connection_id');

        /**
         * Subquery for connected rio ids based on connection ids pseudo-table
         *
         * Outputs columns:
         * `rio_id`
         */
        $connectedRiosQuery = DB::table('rio_connection_users AS connected_rios')
            ->select(
                'connected_rios.rio_id',
            )
            ->rightJoinSub($connectionIdsQuery, 'connection_ids', function ($join) {
                $join->on('connected_rios.rio_connection_id', '=', 'connection_ids.rio_connection_id');
            })
            ->where('connected_rios.rio_id', '<>', $rioId)
            ->whereNull('connected_rios.deleted_at')
            ->groupBy('connected_rios.rio_id');

        /**
         * Main query fetching connected rio users from specified rio
         */
        return $query
            ->rightJoinSub($connectedRiosQuery, 'connected_rios', function ($join) {
                $join->on('rios.id', '=', 'connected_rios.rio_id');
            });
    }

    /**
     * Scope a query of rios connected to specified rio
     *
     * @param \Illuminate\Database\Eloquent\Builder<Rio> $query
     * @param int $groupId Target Group Connection
     * @param int $rioId Target RIO user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeConnectionGroupInviteMembers($query, $groupId, $rioId)
    {
        /**
         * Subquery for connected rio ids based on connection ids pseudo-table
         *
         * Outputs columns:
         * `rio_id`
         */
        $groupConnectionStatusQuery = DB::table('group_connection_users AS group_connection_statuses')
            ->select(
                'group_connection_statuses.id',
                'group_connection_statuses.rio_id',
                'group_connection_statuses.status',
            )
            ->where('group_connection_statuses.group_connection_id', $groupId)
            ->where('group_connection_statuses.rio_id', '<>', $rioId)
            ->whereNull('group_connection_statuses.deleted_at')
            ->groupBy('group_connection_statuses.id', 'group_connection_statuses.rio_id');

        /**
         * Main query fetching connected rio users from specified rio
         */
        return $query
            ->connectedRio($rioId)
            ->select('rios.*')
            ->addSelect('group_connection_statuses.id as invite_id')
            ->addSelect('group_connection_statuses.status as invite_status')
            ->leftJoinSub($groupConnectionStatusQuery, 'group_connection_statuses', function ($join) {
                $join->on('rios.id', '=', 'group_connection_statuses.rio_id');
            })
            ->with('rio_profile');
    }

    /**
     * Scope a query based on common search conditions
     *
     * @param \Illuminate\Database\Eloquent\Builder<Rio> $query
     * @param array  $options   Array of response options
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeCommonConditions($query, $options)
    {
        // Search by full name
        if (isset($options['name']) && !is_null($options['name'])) {
            $searchName = '%' . mb_strtolower($options['name']) . '%';
            $query->where(DB::raw("LOWER(CONCAT(`rios`.`family_name`,' ',`rios`.`first_name`))"), 'LIKE', $searchName);
        }

        return $query;
    }

    /**
     * Save registration input in users, rios, rio_profiles, rio_privacies.
     *
     * @param array $registrationInput
     *
     * @return object
     */
    public static function createRioUser($registrationInput)
    {
        $rio = self::create($registrationInput['rio']);

        $rio->update([
            'referral_message_template' => Rio::getDefaultMessage(
                $rio,
                route('registration.email', $rio->referral_code)
            ),
        ]);

        $registrationInput['user']['rio_id'] = $rio->id;
        $registrationInput['rio_profile']['rio_id'] = $rio->id;
        $registrationInput['user']['password'] = Hash::make($registrationInput['user']['password']);
        $registrationInput['rio_privacy'] = [
            'rio_id' => $rio->id,
            'accept_connections' => AcceptConnectionType::ACCEPT_APPLICATION,
            'accept_connections_by_neo' => AcceptBelongType::ACCEPT_CONNECTION,
        ];

        $user = User::create($registrationInput['user']);
        RioProfile::create($registrationInput['rio_profile']);
        RioPrivacy::create($registrationInput['rio_privacy']);

        return $user;
    }

    /**
     * Get profile photo attribute
     *
     * @return string
     */
    public function getProfilePhotoAttribute()
    {
        if (is_null($this->rio_profile)) {
            return asset(config('bphero.profile_image_directory') . config('bphero.profile_image_filename'));
        }

        $profilePhoto = is_null($this->rio_profile->profile_photo) ? config('bphero.profile_image_filename') : $this->rio_profile->profile_photo;

        return asset(config('bphero.profile_image_directory') . $profilePhoto);
    }

    /**
     * Scope search query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array  $options
     * @param mixed $service
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $options, $service)
    {
        /** @var User */
        $user = auth()->user();

        $yearsOfExperience = isset($options['years_of_experience']) && !is_null($options['years_of_experience']);
        $businessCategory = isset($options['business_category']) && !is_null($options['business_category']);
        $freeWord = isset($options['free_word']) && !is_null($options['free_word']);
        $excludeConnected = isset($options['exclude_connected']) && !is_null($options['exclude_connected']) && $options['exclude_connected'] == ConnectionInclusion::EXCLUDED;

        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');

        if (($yearsOfExperience || $businessCategory) || (isset($options['search_expert']) && !is_null($options['search_expert']))) {
            $query->join('rio_experts', 'rios.id', '=', 'rio_experts.rio_id');
        }

        $query->join('rio_privacies', 'rios.id', '=', 'rio_privacies.rio_id')
            ->select(
                'rios.id as id',
                DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                DB::raw("'rio' as service"),
                DB::raw("(SELECT CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo) FROM rio_profiles WHERE rio_profiles.rio_id = rios.id) as profile_image")
            )
            ->when($service->type === ServiceSelectionTypes::RIO, function ($q1) use ($service) {
                return $q1->where('rios.id', '<>', $service->data->id);
            })
            ->where('rio_privacies.accept_connections', '<>', AcceptConnectionType::PRIVATE_CONNECTION);

        if ($yearsOfExperience) {
            $query->where('rio_experts.additional', $options['years_of_experience']);
        }

        if ($businessCategory) {
            $query->where('rio_experts.business_category_id', $options['business_category']);
        }

        if ($freeWord) {
            $keyword = $options['free_word'];

            if (isset($options['search_expert']) && !is_null($options['search_expert'])) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('rio_experts.content', 'LIKE', "%{$keyword}%")
                        ->orWhere('rio_experts.additional', 'LIKE', "%{$keyword}%")
                        ->orWhere('rio_experts.information', 'LIKE', "%{$keyword}%");
                });
            } else {
                $query->where(function ($q) use ($keyword) {
                    $q->where('rios.family_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('rios.first_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('rios.family_kana', 'LIKE', "%{$keyword}%")
                        ->orWhere('rios.first_kana', 'LIKE', "%{$keyword}%");
                });
            }
        }

        if ($excludeConnected) {
            $connectedRios = [];

            if ($service->type === ServiceSelectionTypes::RIO) {
                $rio1 = DB::table('rio_connections')
                    ->join('rio_connection_users', 'rio_connections.id', '=', 'rio_connection_users.rio_connection_id')
                    ->select('rio_connections.created_rio_id as id')
                    ->where('rio_connections.status', ConnectionStatuses::CONNECTED)
                    ->where('rio_connection_users.rio_id', $service->data->id);

                $connectedRios = DB::table('rio_connections')
                    ->join('rio_connection_users', 'rio_connections.id', '=', 'rio_connection_users.rio_connection_id')
                    ->select('rio_connection_users.rio_id as id')
                    ->where('rio_connections.status', ConnectionStatuses::CONNECTED)
                    ->where('rio_connections.created_rio_id', $service->data->id)
                    ->union($rio1)
                    ->pluck('id')
                    ->toArray();
            }

            if ($service->type === ServiceSelectionTypes::NEO) {
                $neoBelongs = DB::table('neo_belongs')
                    ->select('neo_belongs.rio_id as id')
                    ->where('neo_belongs.status', NeoBelongStatuses::AFFILIATED)
                    ->where('neo_belongs.role', '<>', RoleType::MEMBER)
                    ->where('neo_belongs.neo_id', $service->data->id);

                $connectedRios = DB::table('neo_connections')
                    ->select('neo_connections.connection_rio_id as id')
                    ->where('neo_connections.status', ConnectionStatuses::CONNECTED)
                    ->where('neo_connections.neo_id', $service->data->id)
                    ->union($neoBelongs)
                    ->pluck('id')
                    ->toArray();
            }

            $query->whereNotIn('rios.id', array_filter($connectedRios));
        }

        return $query;
    }

    /**
     * Rio referral default message.
     *
     * @param mixed $rio
     * @param mixed $link
     *
     * @return string
     */
    public static function getDefaultMessage($rio, $link)
    {
        return <<<TEMPLATE
        I'tHEROはとても素敵なサービスです、以下のURLより是非登録してください。
        
        -------------
        $rio->family_name $rio->first_name さんからの招待URL
        $link
        -------------
        TEMPLATE;
    }
}
