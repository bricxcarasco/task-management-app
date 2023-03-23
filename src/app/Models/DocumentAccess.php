<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Neo\RoleType;
use App\Enums\ServiceSelectionTypes;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\DocumentAccess
 *
 * @property int $id id for Laravel
 * @property int $document_id
 * @property int|null $neo_id
 * @property int|null $rio_id
 * @property int|null $neo_group_id
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Document $document
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAccess newQuery()
 * @method static \Illuminate\Database\Query\Builder|DocumentAccess onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAccess whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAccess whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAccess whereNeoGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAccess whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAccess whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAccess whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|DocumentAccess withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DocumentAccess withoutTrashed()
 * @mixin \Eloquent
 */
class DocumentAccess extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'document_accesses';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'document_id',
        'neo_id',
        'rio_id',
        'neo_group_id',
    ];

    /**
     * Define relationship with rio model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Check if service is the document owner
     *
     * @param   mixed $service
     * @return bool
     */
    public function isDocumentOwner($service)
    {
        return Document::where('id', $this->document_id)
            ->when($service->type === ServiceSelectionTypes::RIO, function ($q1) use ($service) {
                return $q1->where('owner_rio_id', $service->data->id);
            }, function ($q1) use ($service) {
                return $q1->where('owner_neo_id', $service->data->id);
            })
            ->exists();
    }

    /**
     * Check if service is a NEO owner/admin
     *
     * @param   mixed $service
     * @return bool
     */
    public function isAuthorizedNeoUser($service)
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        /** @var Document */
        $document = Document::where('id', $this->document_id)
            ->first();

        if ($service->type === ServiceSelectionTypes::NEO && !empty($document->owner_neo_id)) {
            return NeoBelong::whereRioId($user->rio_id)
                ->whereNeoId($document->owner_neo_id)
                ->whereIn('role', [
                    RoleType::OWNER,
                    RoleType::ADMINISTRATOR,
                ])
                ->exists();
        }

        return false;
    }

    /**
     * Scope a query to get the list of services given share permission
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int  $id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePermittedList($query, $id)
    {
        $defaultProfileDirectory = config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.neo_profile_image');

        // Get permitted RIO (prepare for union)
        $rio = DB::table('document_accesses')
                ->join('rios', 'document_accesses.rio_id', '=', 'rios.id')
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->select(
                    'document_accesses.document_id as document_id',
                    'document_accesses.id as access_id',
                    'rios.id as id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("'RIO' as service"),
                    DB::raw("CASE 
                            WHEN rio_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileDirectory . "'
                            ELSE CONCAT('" . $rioProfileImagePath . "', rio_profiles.rio_id, '/', rio_profiles.profile_photo)
                        END AS profile_photo")
                )
                ->where('document_accesses.document_id', $id)
                ->whereNull('document_accesses.deleted_at');

        // Get permitted NEO (prepare for union)
        $neo = DB::table('document_accesses')
                ->join('neos', 'document_accesses.neo_id', '=', 'neos.id')
                ->join('neo_profiles', 'neos.id', '=', 'neo_profiles.neo_id')
                ->select(
                    'document_accesses.document_id as document_id',
                    'document_accesses.id as access_id',
                    'neos.id as id',
                    'neos.organization_name as name',
                    DB::raw("'NEO' as service"),
                    DB::raw("CASE 
                            WHEN neo_profiles.profile_photo IS NULL
                            THEN '" . $defaultProfileDirectory . "'
                            ELSE CONCAT('" . $neoProfileImagePath . "', neo_profiles.neo_id, '/', neo_profiles.profile_photo)
                        END AS profile_photo")
                )
                ->where('document_accesses.document_id', $id)
                ->whereNull('document_accesses.deleted_at');

        // Get permitted RIO, NEO, and NEO Groups (through union)
        $query->join('neo_groups', 'document_accesses.neo_group_id', '=', 'neo_groups.id')
            ->select(
                'document_accesses.document_id as document_id',
                'document_accesses.id as access_id',
                'neo_groups.id as id',
                'neo_groups.group_name as name',
                DB::raw("'NEO_Group' as service"),
                DB::raw('"' . $defaultProfileDirectory. '" as profile_photo')
            )
            ->where('document_accesses.document_id', $id)
            ->whereNull('document_accesses.deleted_at')
            ->union($rio)
            ->union($neo);

        return $query;
    }
}
