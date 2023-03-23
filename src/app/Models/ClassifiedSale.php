<?php

namespace App\Models;

use App\Enums\Classified\SaleProductAccessibility;
use App\Enums\Classified\SaleProductVisibility;
use App\Enums\ServiceSelectionTypes;
use App\Helpers\CommonHelper;
use App\Objects\ClassifiedImages;
use App\Objects\ServiceSelected;
use App\Traits\ModelUpdatedTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;

/**
 * App\Models\ClassifiedSale
 *
 * @property int $id id for Laravel
 * @property int|null $selling_rio_id ↓どちらかのみセット (it's either)
 * @property int|null $selling_neo_id ↑どちらかのみセット (it's either)
 * @property int $created_rio_id
 * @property string $sale_category
 * @property string $title
 * @property string $detail
 * @property string|null $images 最大5ファイルまでjson形式で指定可能… （文書管理サービスは使わない）
 * @property string|null $price NULL：「個別見積」　必要になるまでは小数点以下を使用しない
 * @property int $is_accept 0: 受付中止(Closed)、1: 受付中 (Open)
 * @property int $is_public 0: 非公開(Private)、1:公開(Private)
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Rio $created_rio
 * @property-read \App\Models\Neo|null $selling_neo
 * @property-read \App\Models\Rio|null $selling_rio
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClassifiedContact[] $contacts
 * @property-read int|null $contacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClassifiedFavorite[] $favorites
 * @property-read int|null $favorites_count
 * @property-read bool $has_inquiry
 * @property-read array $image_urls
 * @property-read bool $is_connected
 * @property-read bool $is_favorite
 * @property-read bool $is_owned
 * @property-read string $main_photo
 * @property-read string|null $price_str
 * @property-read string $product_accessibility
 * @property-read string $product_visibility
 * @property-read string $registered_date
 * @property-read string $selling_photo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClassifiedSaleCategory[] $sale_categories
 * @property-read int|null $sale_categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale newQuery()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedSale onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereIsAccept($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereSaleCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereSellingNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereSellingRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ClassifiedSale withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ClassifiedSale withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale conditions($options = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale favoriteList($service)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale productList()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassifiedSale sellerList()
 * @mixin \Eloquent
 */
class ClassifiedSale extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classified_sales';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'selling_rio_id',
        'selling_neo_id',
        'created_rio_id',
        'sale_category',
        'title',
        'detail',
        'images',
        'price',
        'is_accept',
        'is_public',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'selling_photo',
        'main_photo',
        'image_urls',
        'price_str',
        'is_favorite',
        'is_connected',
        'is_owned',
        'has_inquiry',
        'product_visibility',
        'product_accessibility',
        'registered_date',
    ];

    /**
     * Define relationship for RIO seller
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function selling_rio()
    {
        return $this->belongsTo(Rio::class, 'selling_rio_id');
    }

    /**
     * Define relationship for NEO seller
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function selling_neo()
    {
        return $this->belongsTo(Neo::class, 'selling_neo_id');
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
     * Define relationship with Classified favorites table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favorites()
    {
        return $this->hasMany(ClassifiedFavorite::class);
    }

    /**
     * Define relationship with Classified sale categories table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sale_categories()
    {
        return $this->hasMany(ClassifiedSaleCategory::class);
    }

    /**
     * Define relationship with Classified contacts table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany(ClassifiedContact::class);
    }

    /**
     * Define relationship with Classified payments table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(ClassifiedPayment::class);
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($sales) {
            // Softdelete associated sales favorite
            $sales->favorites()->each(function ($favorite) {
                $favorite->delete();
            });
            // Softdelete associated sales payment
            $sales->payments()->each(function ($payment) {
                $payment->delete();
            });
        });
    }

    /**
     * Get the selling_photo attribute
     *
     * @return string
     */
    public function getSellingPhotoAttribute()
    {
        return $this->sellingPhoto($this);
    }

    /**
     * Get the main_photo attribute
     *
     * @return string
     */
    public function getMainPhotoAttribute()
    {
        return ClassifiedImages::getMainPhotoUrl($this->images);
    }

    /**
     * Get the image_urls attribute
     *
     * @return array
     */
    public function getImageUrlsAttribute()
    {
        return ClassifiedImages::getImageUrls($this->images);
    }

    /**
     * Get the image_paths attribute
     *
     * @return array
     */
    public function getImagePathsAttribute()
    {
        return ClassifiedImages::getImagePaths($this->images);
    }

    /**
     * Get the price_str attribute
     *
     * @return int|string|null
     */
    public function getPriceStrAttribute()
    {
        if (empty($this->price)) {
            return null;
        }

        return CommonHelper::priceFormat($this->price);
    }

    /**
     * Get the is_favorite attribute
     *
     * @return bool
     */
    public function getIsFavoriteAttribute()
    {
        return $this->favorites->count() > 0;
    }

    /**
     * Get the is_connected attribute
     *
     * @return bool
     */
    public function getIsConnectedAttribute()
    {
        /** @phpstan-ignore-next-line */
        if (!isset($this->selling_id)) {
            return false;
        }

        /** @phpstan-ignore-next-line */
        if (!isset($this->selling_type)) {
            return false;
        }

        /** @phpstan-ignore-next-line */
        return $this->isConnected($this->selling_type, $this->selling_id);
    }

    /**
     * Get the is_owned attribute
     *
     * @return bool
     */
    public function getIsOwnedAttribute()
    {
        return $this->isOwned($this);
    }

    /**
     * Get the has_inquiry attribute
     *
     * @return bool
     */
    public function getHasInquiryAttribute()
    {
        return $this->hasInquiry($this);
    }

    /**
     * Get the product_visibility attribute
     *
     * @return string
     */
    public function getProductVisibilityAttribute()
    {
        return SaleProductVisibility::getDescription($this->is_public);
    }

    /**
     * Get the product_accessibility attribute
     *
     * @return string
     */
    public function getProductAccessibilityAttribute()
    {
        return SaleProductAccessibility::getDescription($this->is_accept);
    }

    /**
     * Get the registered_date attribute
     *
     * @return string
     */
    public function getRegisteredDateAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y-m-d');
    }

    /**
     * Scope a query to fetch products list
     *
     * @param \Illuminate\Database\Eloquent\Builder<ClassifiedSale> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeProductList($query)
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        return $query
            ->select([
                'classified_sales.*',
                'classified_sale_categories.sale_category_name',
            ])
            ->selectRaw('
                (CASE
                    WHEN classified_sales.selling_rio_id IS NOT NULL
                        THEN classified_sales.selling_rio_id
                    WHEN classified_sales.selling_neo_id IS NOT NULL
                        THEN classified_sales.selling_neo_id
                    ELSE NULL
                END) AS selling_id
            ')
            ->selectRaw('
                (CASE
                    WHEN classified_sales.selling_rio_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::RIO . '"
                    WHEN classified_sales.selling_neo_id IS NOT NULL
                        THEN "' . ServiceSelectionTypes::NEO . '"
                    ELSE NULL
                END) AS selling_type
            ')
            ->selectRaw('
                (CASE
                    WHEN classified_sales.selling_rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    WHEN classified_sales.selling_neo_id IS NOT NULL
                        THEN neos.organization_name
                    ELSE NULL
                END) AS selling_name
            ')
            ->leftJoin('rios', 'rios.id', '=', 'classified_sales.selling_rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'classified_sales.selling_neo_id')
            ->leftJoin('classified_sale_categories', 'classified_sale_categories.id', '=', 'classified_sales.sale_category')
            ->whereNull('rios.deleted_at')
            ->whereNull('neos.deleted_at')
            ->whereNull('classified_sale_categories.deleted_at')
            ->with('favorites', function ($q) use ($service) {
                switch ($service->type) {
                    case ServiceSelectionTypes::RIO:
                        return $q->where('rio_id', $service->data->id);
                    case ServiceSelectionTypes::NEO:
                        return $q->where('neo_id', $service->data->id);
                    default:
                        return $q;
                }
            })
            ->orderBy('classified_sales.updated_at', 'DESC');
    }

    /**
     * Scope a query to fetch products list based on conditions
     *
     * @param \Illuminate\Database\Eloquent\Builder<ClassifiedSale> $query
     * @param mixed $options
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeConditions($query, $options = null)
    {
        // Search by accessibility
        if (isset($options['is_accept']) && !is_null($options['is_accept'])) {
            // Filter only if is_accept is checked (true)
            if ($options['is_accept']) {
                $query->whereIsAccept($options['is_accept']);
            }
        }

        // Search by keyword
        if (isset($options['keyword']) && !is_null($options['keyword'])) {
            $keyword = $options['keyword'];
            $query->where(function ($queries) use ($keyword) {
                $queries->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('detail', 'LIKE', "%{$keyword}%");
            });
        }

        // Search by sales category
        if (isset($options['salesCategory']) && !is_null($options['salesCategory'])) {
            $query->whereSaleCategory($options['salesCategory']);
        }

        return $query;
    }

    /**
     * Scope a query to fetch only seller registered product lists.
     *
     * @param \Illuminate\Database\Eloquent\Builder<ClassifiedSale> $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeSellerList($query)
    {
        $query = $query
            ->productList()
            ->reorder('classified_sales.created_at', 'DESC');

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $query->where('classified_sales.selling_rio_id', $service->data->id);
            case ServiceSelectionTypes::NEO:
                return $query->where('classified_sales.selling_neo_id', $service->data->id);
            default:
                return $query;
        }
    }

    /**
     * Scope a query to fetch favorite list of selected service
     *
     * @param \Illuminate\Database\Eloquent\Builder<ClassifiedSale> $query
     * @param mixed $service Selected service
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeFavoriteList($query, $service)
    {
        return $query
            ->productList()
            ->whereHas('favorites', function ($q) use ($service) {
                switch ($service->type) {
                    case ServiceSelectionTypes::RIO:
                        return $q->where('rio_id', $service->data->id);
                    case ServiceSelectionTypes::NEO:
                        return $q->where('neo_id', $service->data->id);
                    default:
                        return $q;
                }
            });
    }

    /**
     * Check if RIO/NEO seller is connected to the service subject
     *
     * @param string $type Seller type
     * @param int $id RIO/NEO Seller ID
     * @return bool
     */
    public static function isConnected($type, $id)
    {
        $service = ServiceSelected::getSelected();
        $connectionList = RioConnection::connectedList($service, [])->get();
        $connectedRioIds = [];
        $connectedNeoIds = [];

        foreach ($connectionList as $connection) {
            if ($connection['service'] === ServiceSelectionTypes::RIO) {
                $connectedRioIds[] = $connection->id;
            }
            if ($connection['service'] === ServiceSelectionTypes::NEO) {
                $connectedNeoIds[] = $connection->id;
            }
        }

        switch ($type) {
            case ServiceSelectionTypes::RIO:
                return in_array($id, $connectedRioIds);
            case ServiceSelectionTypes::NEO:
                return in_array($id, $connectedNeoIds);
            default:
                return false;
        }
    }

    /**
     * Check if active service is the product owner.
     *
     * @param \App\Models\ClassifiedSale $product
     * @return bool
     */
    public static function isOwned($product)
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $service->data->id === $product->selling_rio_id;
            case ServiceSelectionTypes::NEO:
                return $service->data->id === $product->selling_neo_id;
            default:
                return false;
        }
    }

    /**
     * Check if active service has already inquired.
     *
     * @param \App\Models\ClassifiedSale $product
     * @return bool
     */
    public static function hasInquiry($product)
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Initialize query
        $contacts = $product->contacts();

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                $contacts->where('classified_contacts.rio_id', $service->data->id);
                break;
            case ServiceSelectionTypes::NEO:
                $contacts->where('classified_contacts.neo_id', $service->data->id);
                break;
            default:
                break;
        }

        return $contacts->count() > 0;
    }

    /**
     * Get seller profile photo.
     *
     * @param \App\Models\ClassifiedSale $product
     * @return string
     */
    public static function sellingPhoto($product)
    {
        // Image directories
        $defaultProfileImage = config('app.url') . '/' . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . '/' . 'storage/' . config('bphero.rio_profile_image');
        $neoProfileImagePath = config('app.url') . '/' . 'storage/' . config('bphero.neo_profile_image');

        // RIO seller
        if ($product->selling_rio_id !== null) {
            /** @phpstan-ignore-next-line */
            $rioSeller = $product->selling_rio->rio_profile;

            return !empty($rioSeller->profile_photo)
                ? $rioProfileImagePath . '/' . $rioSeller->rio_id . '/' . $rioSeller->profile_photo
                : $defaultProfileImage;
        }

        // NEO seller
        if ($product->selling_neo_id !== null) {
            /** @phpstan-ignore-next-line */
            $neoSeller = $product->selling_neo->neo_profile;

            return !empty($neoSeller->profile_photo)
                ? $neoProfileImagePath . '/' . $neoSeller->rio_id . '/' . $neoSeller->profile_photo
                : $defaultProfileImage;
        }

        return $defaultProfileImage;
    }
}
