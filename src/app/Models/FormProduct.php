<?php

namespace App\Models;

use App\Enums\ServiceSelectionTypes;
use App\Objects\ServiceSelected;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\FormProduct
 *
 * @property int $id id for Laravel
 * @property int $form_id
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property int $created_rio_id
 * @property string $name
 * @property int|null $quantity
 * @property string|null $unit
 * @property string|null $unit_price
 * @property int|null $tax_distinction 1:10%, 2:軽減8%, 3:対象外
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Rio $created_rio
 * @property-read \App\Models\Form $form
 * @property-read \App\Models\Neo|null $neo
 * @property-read \App\Models\Rio|null $rio
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct newQuery()
 * @method static \Illuminate\Database\Query\Builder|FormProduct onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereTaxDistinction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|FormProduct withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FormProduct withoutTrashed()
 * @mixin \Eloquent
 */
class FormProduct extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_id',
        'rio_id',
        'neo_id',
        'created_rio_id',
        'name',
        'quantity',
        'unit',
        'unit_price',
        'tax_distinction',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'amount',
    ];

    /**
     * Define relationship with FORM
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    /**
     * Define relationship with deleted FORM
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deleted_form()
    {
        return $this->belongsTo(Form::class, 'form_id')->withTrashed();
    }

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
     * Get amount attribute
     *
     * @return int|float
     */
    public function getAmountAttribute()
    {
        return (int)$this->quantity * (float)$this->unit_price ?? 0;
    }

    /**
     * Scope a query to get all downloadable CSV per type.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Form> $query
     * @param array $ids
     * @param int $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeDownloadableCsv($query, $ids, $type)
    {
        // Get selected service
        $service = ServiceSelected::getSelected();

        // Filter based on current service
        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                $query->where('forms.rio_id', $service->data->id);
                break;
            case ServiceSelectionTypes::NEO:
                $query->where('forms.neo_id', $service->data->id);
                break;
            default:
                break;
        }

        return $query
            ->select([
                'form_products.*',
            ])
            ->selectRaw('
                (CASE
                    WHEN forms.supplier_name IS NOT NULL
                        THEN forms.supplier_name
                    WHEN forms.supplier_rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    WHEN forms.supplier_neo_id IS NOT NULL
                        THEN neos.organization_name
                    ELSE NULL
                END) AS supplier_name
            ')
            ->leftJoin('forms', 'forms.id', '=', 'form_products.form_id')
            ->leftJoin('rios', 'rios.id', '=', 'forms.supplier_rio_id')
            ->leftJoin('rio_profiles', 'rio_profiles.rio_id', '=', 'forms.supplier_rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'forms.supplier_neo_id')
            ->leftJoin('neo_profiles', 'neo_profiles.neo_id', '=', 'forms.supplier_neo_id')
            ->whereIn('forms.id', $ids)
            ->where('forms.type', $type)
            ->with('form.products');
    }
}
