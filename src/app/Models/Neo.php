<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Neo\AcceptConnectionType;
use App\Enums\AttributeCodes;
use App\Enums\ConnectionInclusion;
use App\Enums\ConnectionStatuses;
use App\Enums\Neo\NeoBelongStatusType;
use App\Enums\ServiceSelectionTypes;
use DB;
use App\Enums\NeoBelongStatuses;
use App\Enums\Neo\RoleType;

/**
 * App\Models\Neo
 *
 * @property int $id id for Laravel
 * @property string $organization_name
 * @property string $organization_kana
 * @property string $organization_type
 * @property string $establishment_date
 * @property string|null $email
 * @property string|null $tel
 * @property string|null $site_url
 * @property string|null $google_token
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoBelong[] $administrators
 * @property-read int|null $administrators_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoBelong[] $applying_members
 * @property-read int|null $applying_members_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoExpert[] $awards
 * @property-read int|null $awards_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Chat[] $chats
 * @property-read int|null $chats_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoExpert[] $emails
 * @property-read int|null $emails_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoExpert[] $experts
 * @property-read int|null $experts_count
 * @property-read string|null $business_duration
 * @property-read mixed $business_hours
 * @property-read bool $is_admin
 * @property-read bool $is_authorized_user
 * @property-read bool $is_member
 * @property-read bool $is_owner
 * @property-read string $profile_photo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoExpert[] $histories
 * @property-read int|null $histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoExpert[] $industries
 * @property-read int|null $industries_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoBelong[] $inviting_members
 * @property-read int|null $inviting_members_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoBelong[] $members
 * @property-read int|null $members_count
 * @property-read \App\Models\NeoPrivacy|null $neo_privacy
 * @property-read \App\Models\NeoProfile|null $neo_profile
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoExpert[] $overseas
 * @property-read int|null $overseas_count
 * @property-read \App\Models\NeoBelong|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoExpert[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoExpert[] $qualifications
 * @property-read int|null $qualifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rio[] $rios
 * @property-read int|null $rios_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoExpert[] $skills
 * @property-read int|null $skills_count
 * @property-read \App\Models\Subscriber|null $subscriber
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NeoExpert[] $urls
 * @property-read int|null $urls_count
 * @method static \Illuminate\Database\Eloquent\Builder|Neo currentUser($type, $id)
 * @method static \Database\Factories\NeoFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Neo newQuery()
 * @method static \Illuminate\Database\Query\Builder|Neo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Neo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Neo search($options, $service)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereEstablishmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereGoogleToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereOrganizationKana($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereOrganizationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereOrganizationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereSiteUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Neo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Neo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Neo withoutTrashed()
 * @mixin \Eloquent
 */
class Neo extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'neos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organization_name',
        'organization_kana',
        'organization_type',
        'establishment_date',
        'email',
        'tel',
        'site_url',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_authorized_user',
        'is_owner',
        'is_admin',
        'is_member',
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
     * Define relationship with neo profiles model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function neo_profile()
    {
        return $this->hasOne(NeoProfile::class);
    }

    /**
     * Define relationship of owner in neo belongs model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner()
    {
        return $this->hasOne(NeoBelong::class)
            ->affiliatedPerRole(RoleType::OWNER);
    }

    /**
     * Define relationship of administrators in neo belongs model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function administrators()
    {
        return $this->hasMany(NeoBelong::class)
            ->affiliatedPerRole(RoleType::ADMINISTRATOR);
    }

    /**
     * Define relationship of members in neo belongs model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members()
    {
        return $this->hasMany(NeoBelong::class)
            ->affiliatedPerRole(RoleType::MEMBER);
    }

    /**
     * Define relationship of members in neo belongs model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applying_members()
    {
        return $this->hasMany(NeoBelong::class)
            ->whereRole(RoleType::MEMBER)
            ->whereStatus(NeoBelongStatusType::APPLYING);
    }

    /**
     * Define relationship of members in neo belongs model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inviting_members()
    {
        return $this->hasMany(NeoBelong::class)
            ->whereRole(RoleType::MEMBER)
            ->whereStatus(NeoBelongStatusType::INVITING);
    }

    /**
     * Define relationship with neo experts model with product_service_information attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(NeoExpert::class)->whereAttributeCode(AttributeCodes::PRODUCT_SERVICE_INFORMATION);
    }

    /**
     * Define relationship with rio model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rios()
    {
        return $this->belongsToMany(Rio::class, 'neo_belongs')
            ->as('neo_belongs')
            ->withPivot([
                'status',
                'is_display',
                'role',
            ])
            ->wherePivotNull('deleted_at');
    }

    /**
     * Define relationship with neo experts model with industry attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function awards()
    {
        return $this->hasMany(NeoExpert::class)
            ->whereAttributeCode(AttributeCodes::AWARDS);
    }

    /**
     * Define relationship with neo experts model with industry attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function industries()
    {
        return $this->hasMany(NeoExpert::class)
            ->whereAttributeCode(AttributeCodes::INDUSTRY);
    }

    /**
     * Define relationship with neo experts model with qualification attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qualifications()
    {
        return $this->hasMany(NeoExpert::class)
            ->whereAttributeCode(AttributeCodes::QUALIFICATIONS);
    }

    /**
     * Define relationship with neo experts model with skills attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skills()
    {
        return $this->hasMany(NeoExpert::class)
            ->whereAttributeCode(AttributeCodes::SKILLS);
    }

    /**
     * Define relationship with neo experts model with url attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function urls()
    {
        return $this->hasMany(NeoExpert::class)
            ->whereAttributeCode(AttributeCodes::URL);
    }

    /**
     * Define relationship with neo experts model with history attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories()
    {
        return $this->hasMany(NeoExpert::class)
            ->whereAttributeCode(AttributeCodes::HISTORY);
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
     * Define relationship with chats
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chats()
    {
        return $this->hasMany(Chat::class, 'owner_neo_id');
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
     * Get business duration attribute
     *
     * @return string|null
     */
    public function getBusinessDurationAttribute()
    {
        // Guard clause for empty value
        if (empty($this->establishment_date)) {
            return null;
        }

        $establishmentDate = Carbon::parse($this->establishment_date);

        // Get year difference
        $years = now()->diffInYears($establishmentDate);

        // Get month difference
        $monthReference = $establishmentDate->copy()->addYears($years);
        $months = now()->diffInMonths($monthReference);

        // Get day reference
        $dayReference = $monthReference->copy()->addMonths($months);
        $days = now()->diffInDays($dayReference);

        // Initialize compiled array
        $result = [];

        if (!empty($years)) {
            $result[] = trans_choice('label.business_duration_year', $years, ['value' => $years]);
        }

        if (!empty($months)) {
            $result[] = trans_choice('label.business_duration_month', $months, ['value' => $months]);
        }

        if (!empty($days)) {
            $result[] = trans_choice('label.business_duration_day', $days, ['value' => $days]);
        }

        return implode(' ', $result);
    }

    /**
     * Check if user has access to specified neo entity
     *
     * @return bool
     */
    public function isUserAccessible()
    {
        // Get rio id of currently logged-in user
        /** @var User */
        $user = auth()->user();
        $rioId = $user->rio_id;

        // Check if authenticated user belongs to specified neo
        $isExistInNeo = $this->rios()
            ->whereRioId($rioId)
            ->exists();

        return $isExistInNeo;
    }

    /**
     * Define relationship with rio experts model with qualification attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails()
    {
        return $this->hasMany(NeoExpert::class)
            ->whereAttributeCode(AttributeCodes::EMAIL);
    }

    /**
     * Define relationship with neo_expert model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function experts()
    {
        return $this->hasMany(NeoExpert::class);
    }

    /**
     * Get business hours attribute
     *
     * @return mixed
     */
    public function getBusinessHoursAttribute()
    {
        $expert = $this->experts()->where('attribute_code', AttributeCodes::BUSINESS_HOURS)->first();

        if (empty($expert)) {
            return [
                'id' => null,
                'content' => null,
                'additional' => null
            ];
        }

        return array_intersect_key($expert->toArray(), array_flip([
            'id',
            'content',
            'additional'
        ]));
    }

    /**
     * Define relationship with neo privacies model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function neo_privacy()
    {
        return $this->hasOne(NeoPrivacy::class);
    }

    /**
     * Define relationship with neo experts model with overseas attribute
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function overseas()
    {
        return $this->hasMany(NeoExpert::class)
            ->whereAttributeCode(AttributeCodes::OVERSEAS);
    }

    /**
     * Get profile photo attribute
     *
     * @return string
     */
    public function getProfilePhotoAttribute()
    {
        if (is_null($this->neo_profile)) {
            return asset(config('bphero.profile_image_directory') . config('bphero.profile_image_filename'));
        }

        $profilePhoto = is_null($this->neo_profile->profile_photo) ? config('bphero.profile_image_filename') : $this->neo_profile->profile_photo;

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

        $neoProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.neo_profile_image');

        if (($yearsOfExperience || $businessCategory) || (isset($options['search_expert']) && !is_null($options['search_expert']))) {
            $query->join('neo_experts', 'neos.id', '=', 'neo_experts.neo_id');
        }

        $query->join('neo_privacies', 'neos.id', '=', 'neo_privacies.neo_id')
            ->select(
                'neos.id as id',
                'neos.organization_name as name',
                DB::raw("'neo' as service"),
                DB::raw("(SELECT CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo) FROM neo_profiles WHERE neo_profiles.neo_id = neos.id) as profile_image")
            )
            ->when($service->type === ServiceSelectionTypes::NEO, function ($q1) use ($service) {
                return $q1->where('neos.id', '<>', $service->data->id);
            })
            ->where('neo_privacies.accept_connections', '<>', AcceptConnectionType::PRIVATE_CONNECTION);

        if ($yearsOfExperience) {
            $query->where('neo_experts.additional', $options['years_of_experience']);
        }

        if ($businessCategory) {
            $query->where('neo_experts.business_category_id', $options['business_category']);
        }

        if ($freeWord) {
            $keyword = $options['free_word'];

            if (isset($options['search_expert']) && !is_null($options['search_expert'])) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('neo_experts.content', 'LIKE', "%{$keyword}%")
                        ->orWhere('neo_experts.additional', 'LIKE', "%{$keyword}%")
                        ->orWhere('neo_experts.information', 'LIKE', "%{$keyword}%");
                });
            } else {
                $query->where(function ($q) use ($keyword) {
                    $q->where('neos.organization_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('neos.organization_kana', 'LIKE', "%{$keyword}%");
                });
            }
        }

        if ($excludeConnected) {
            $connectedNeos = [];

            if ($service->type === ServiceSelectionTypes::RIO) {
                $neoBelongs = DB::table('neo_belongs')
                    ->select('neo_belongs.neo_id as id')
                    ->where('neo_belongs.status', NeoBelongStatuses::AFFILIATED)
                    ->where('neo_belongs.role', '<>', RoleType::MEMBER)
                    ->where('neo_belongs.rio_id', $service->data->id);

                $connectedNeos = DB::table('neo_connections')
                    ->select('neo_connections.neo_id as id')
                    ->where('neo_connections.status', ConnectionStatuses::CONNECTED)
                    ->where('neo_connections.connection_rio_id', $service->data->id)
                    ->union($neoBelongs)
                    ->pluck('id')
                    ->toArray();
            }

            if ($service->type === ServiceSelectionTypes::NEO) {
                $neoConnectionNeo1 = DB::table('neo_connections')
                    ->select('neo_connections.neo_id as id')
                    ->where('neo_connections.status', ConnectionStatuses::CONNECTED)
                    ->where('neo_connections.connection_neo_id', $service->data->id);

                $connectedNeos = DB::table('neo_connections')
                    ->select('neo_connections.connection_neo_id as id')
                    ->where('neo_connections.status', ConnectionStatuses::CONNECTED)
                    ->where('neo_connections.neo_id', $service->data->id)
                    ->union($neoConnectionNeo1)
                    ->pluck('id')
                    ->toArray();
            }

            $query->whereNotIn('neos.id', array_filter($connectedNeos));
        }

        return $query;
    }

    /**
     * Check if logged-in user is an authorized NEO user.
     *
     * Authorized users are either OWNER or ADMIN role
     *
     * @return bool
     */
    public function getIsAuthorizedUserAttribute()
    {
        /** @var User */
        $user = auth()->user();

        if (empty($user)) {
            return false;
        }

        return $this
            ->rios()
            ->whereRioId($user->rio_id)
            ->wherePivotIn(
                'role',
                [
                    RoleType::OWNER,
                    RoleType::ADMINISTRATOR,
                ]
            )
            ->exists();
    }

    /**
     * Check if logged-in user is the NEO owner
     *
     * @return bool
     */
    public function getIsOwnerAttribute()
    {
        /** @var User */
        $user = auth()->user();

        if (empty($user)) {
            return false;
        }

        return $this
            ->rios()
            ->whereRioId($user->rio_id)
            ->wherePivot('role', RoleType::OWNER)
            ->exists();
    }

    /**
     * Check if logged-in user is a NEO admin
     *
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        /** @var User */
        $user = auth()->user();

        if (empty($user)) {
            return false;
        }

        return $this
            ->rios()
            ->whereRioId($user->rio_id)
            ->wherePivot('role', RoleType::ADMINISTRATOR)
            ->exists();
    }

    /**
     * Check if logged-in user is a NEO member
     *
     * @return bool
     */
    public function getIsMemberAttribute()
    {
        /** @var User */
        $user = auth()->user();

        if (empty($user)) {
            return false;
        }

        return $this
            ->rios()
            ->whereRioId($user->rio_id)
            ->wherePivot('role', RoleType::MEMBER)
            ->exists();
    }

    /**
     * Scope query for session owner
     *
     * @param mixed $query
     * @param mixed $type
     * @param mixed $id
     * @return mixed
     */
    public function scopeCurrentUser($query, $type, $id)
    {
        $defaultProfileImage = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.neo_profile_image');

        if ($type === ServiceSelectionTypes::RIO) {
            $query = Rio::select([
                    'rios.id',
                    DB::raw("CASE
                                WHEN rio_profiles.profile_photo IS NOT NULL
                                    THEN CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                                ELSE '" . $defaultProfileImage . "'
                            END AS profile_image"),
                    DB::raw("'RIO' as type"),
                ])
                ->selectRaw('TRIM(CONCAT(rios.family_name, " ", rios.first_name)) AS name')
                ->leftJoin('rio_profiles', 'rio_profiles.rio_id', '=', 'rios.id')
                ->where('rios.id', '=', $id);
        } else {
            $query = Neo::select([
                    'neos.id',
                    DB::raw("CASE
                                WHEN neo_profiles.profile_photo IS NOT NULL
                                    THEN CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                                ELSE '" . $defaultProfileImage . "'
                            END AS profile_image"),
                    'organization_name as name',
                    DB::raw("'NEO' as type"),
                ])
                ->leftJoin('neo_profiles', 'neo_profiles.neo_id', '=', 'neos.id')
                ->where('neos.id', '=', $id);
        }

        return $query;
    }
}
