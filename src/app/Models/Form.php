<?php

namespace App\Models;

use App\Enums\Form\FormSortTypes;
use App\Enums\Form\Types;
use App\Enums\MailTemplates;
use App\Enums\ServiceSelectionTypes;
use App\Notifications\FormRecipientNotification;
use App\Objects\ServiceSelected;
use App\Traits\ModelUpdatedTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Form
 *
 * @property int $id id for Laravel
 * @property int|null $rio_id ↓どちらかのみセット
 * @property int|null $neo_id ↑どちらかのみセット
 * @property int $created_rio_id
 * @property int|null $supplier_rio_id ↓どちらかのみセット
 * @property int|null $supplier_neo_id ↑どちらかのみセット
 * @property string|null $supplier_name
 * @property int|null $deleter_rio_id
 * @property string $form_no
 * @property string $title
 * @property int $type 1:quotation, 2:purchase history, 3:delivery slip, 4:invoice, 5:receipt
 * @property string $price
 * @property string|null $zipcode
 * @property string|null $address
 * @property string $subject
 * @property int $receipt_amount
 * @property string $delivery_address
 * @property string $payment_terms
 * @property string|null $delivery_date
 * @property string|null $payment_date
 * @property string|null $issue_date
 * @property string|null $expiration_date
 * @property string|null $receipt_date
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at 登録日時(created datetime)
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時(updated datetime)
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)
 * @property-read \App\Models\Rio $created_rio
 * @property-read \App\Models\Rio|null $deleter_rio
 * @property-read \App\Models\Neo|null $neo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FormProduct[] $products
 * @property-read int|null $products_count
 * @property-read \App\Models\Rio|null $rio
 * @property-read \App\Models\Neo|null $supplier_neo
 * @property-read \App\Models\Rio|null $supplier_rio
 * @method static \Illuminate\Database\Eloquent\Builder|Form commonConditions($options = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Form formDetails($type)
 * @method static \Illuminate\Database\Eloquent\Builder|Form formList($type)
 * @method static \Illuminate\Database\Eloquent\Builder|Form newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Form newQuery()
 * @method static \Illuminate\Database\Query\Builder|Form onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Form query()
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereCreatedRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereDeleterRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereDeliveryAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereFormNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form wherePaymentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereReceiptDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSupplierNeoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSupplierRioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereZipcode($value)
 * @method static \Illuminate\Database\Query\Builder|Form withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Form withoutTrashed()
 * @mixin \Eloquent
 */
class Form extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'forms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rio_id',
        'neo_id',
        'created_rio_id',
        'supplier_rio_id',
        'supplier_neo_id',
        'supplier_name',
        'deleter_rio_id',
        'form_no',
        'title',
        'type',
        'price',
        'zipcode',
        'address',
        'delivery_address',
        'payment_terms',
        'delivery_date',
        'delivery_deadline',
        'payment_date',
        'issue_date',
        'expiration_date',
        'receipt_date',
        'remarks',
        'issuer_name',
        'issuer_department_name',
        'issuer_address',
        'issuer_tel',
        'issuer_fax',
        'issuer_business_number',
        'issuer_image',
        'issuer_payee_information',
        'issuer_payment_notes'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'supplier',
        'is_supplier_connected',
        'receipt_amount'
    ];

    /**
     * Append relationship to the model's array form.
     *
     * @var array
     */
    protected $with = [
        'products'
    ];

    /**
     * Define relationship with Form products
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(FormProduct::class);
    }

    /**
     * Define relationship with deleted Form products
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deleted_products()
    {
        return $this->hasMany(FormProduct::class)->withTrashed();
    }

    /**
     * Define relationship with Form histories
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function form_histories()
    {
        return $this->hasMany(FormHistory::class);
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
     * Define relationship with RIO supplier
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier_rio()
    {
        return $this->belongsTo(Rio::class, 'supplier_rio_id');
    }

    /**
     * Define relationship with NEO supplier
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier_neo()
    {
        return $this->belongsTo(Neo::class, 'supplier_neo_id');
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
     * Define relationship with RIO deleter
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deleter_rio()
    {
        return $this->belongsTo(Rio::class, 'deleter_rio_id');
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($form) {
            // Softdelete associated form products
            $form->products()->each(function ($product) {
                $product->delete();
            });
        });
    }


    /**
     * Send email notification to RIO/NEO selected as connection recipient
     *
     * @param string $issuer
     * @param \App\Models\Neo|\App\Models\Rio $recipientConnection
     * @param \App\Models\Form $form
     * @return bool|void
     */
    public function sendEmailToConnectionRecipient($issuer, $recipientConnection, $form)
    {
        // Check RIO recipient if allowed to receive email
        if ($recipientConnection instanceof \App\Models\Rio) {
            if (NotificationRejectSetting::isRejectedEmail($recipientConnection, MailTemplates::FORM_RECIPIENT_CONNECTION)) {
                return false;
            }

            // Get email information
            $user = User::whereRioId($recipientConnection->id)->firstOrFail();

            // Send email to RIO receiver
            Notification::sendNow($user, new FormRecipientNotification($issuer, $recipientConnection, $form));
        }

        // Check NEO owner receiver if allowed to receive email
        if ($recipientConnection instanceof \App\Models\Neo) {
            // Get NEO owner NeoBelong information
            /** @var \App\Models\NeoBelong */
            $neoBelongs = $recipientConnection->owner;

            // Guard clause for non-existing NEO owner
            if (empty($neoBelongs)) {
                return false;
            }

            // Get NEO owner RIO information
            /** @var \App\Models\Rio */
            $neoOwnerRecipient = $neoBelongs->rio;

            if (NotificationRejectSetting::isRejectedEmail(
                $neoOwnerRecipient,
                MailTemplates::FORM_RECIPIENT_CONNECTION
            )) {
                return false;
            }

            // Get email information
            $user = User::whereRioId($neoOwnerRecipient->id)->firstOrFail();

            // Send email to NEO receiver
            Notification::sendNow($user, new FormRecipientNotification($issuer, $recipientConnection, $form));
        }
    }

    /**
     * Get supplier attribute
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function getSupplierAttribute()
    {
        $supplier = collect();
        $entity = '';
        $name = '';

        if (is_null($this->supplier_rio_id) && is_null($this->supplier_neo_id)) {
            return $supplier;
        }

        if (!is_null($this->supplier_rio_id)) {
            /** @var \App\Models\Rio */
            $supplier = $this->supplier_rio;
            $entity = ServiceSelectionTypes::RIO;
            $name = "{$supplier->family_name} {$supplier->first_name}";
        }

        if (!is_null($this->supplier_neo_id)) {
            /** @var \App\Models\Neo */
            $supplier = $this->supplier_neo;
            $entity = ServiceSelectionTypes::NEO;
            $name = $supplier->organization_name;
        }

        return [
            'id' => $supplier->id,
            'service' => $entity,
            'name' => $name,
        ];
    }

    /**
     * Compare form to updated form.
     *
     * @param array $requestData Request data
     * @param array $products Form products. Defaults to null.
     * @return array
     */
    public function compare($requestData, $products = null)
    {
        $existingForm = $this->getAttributes() ?? [];
        $existingProducts = $this->products->toArray() ?? [];
        $updatedForm = [];
        $updatedProducts = [];

        // Compare and get all modifications in forms table
        foreach ($requestData as $key => $data) {
            if ($existingForm[$key] != $data) {
                $updatedForm[] = $key;
            }
        }

        // Compare and get all modifications in form_products table
        if (!empty($products)) {
            $updatedProducts['added'] = [];
            $updatedProducts['deleted'] = [];
            $currentProductIds = [];

            // Newly added products
            foreach ($products as $product) {
                if (!isset($product['id'])) {
                    $updatedProducts['added'][] = $product['name'];
                } else {
                    $currentProductIds[] = $product['id'];
                }
            }

            // Deleted products
            foreach ($existingProducts as $existingProduct) {
                if (!in_array($existingProduct['id'], $currentProductIds)) {
                    $updatedProducts['deleted'][] = $existingProduct['name'];
                }
            }
        }

        return [
            $updatedForm,
            $updatedProducts,
        ];
    }

    /**
     * Scope a query to get list base on type.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Form> $query
     * @param int $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeFormList($query, $type)
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
                'forms.*',
            ])
            // Get Rio or Neo name
            ->selectRaw('
                (CASE
                    WHEN forms.supplier_name IS NOT NULL
                        THEN forms.supplier_name
                    WHEN forms.supplier_rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    WHEN forms.supplier_neo_id IS NOT NULL
                        THEN neos.organization_name
                    ELSE NULL
                END) AS name
            ')
            ->selectRaw('
                (CASE
                    WHEN forms.rio_id IS NOT NULL
                        THEN forms.rio_id
                    WHEN forms.neo_id IS NOT NULL
                        THEN forms.neo_id
                    ELSE NULL
                END) AS owner_id
            ')
            ->leftJoin('rios', 'rios.id', '=', 'forms.supplier_rio_id')
            ->leftJoin('rio_profiles', 'rio_profiles.rio_id', '=', 'forms.supplier_rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'forms.supplier_neo_id')
            ->leftJoin('neo_profiles', 'neo_profiles.neo_id', '=', 'forms.supplier_neo_id')
            ->where('forms.type', $type);
    }

    /**
     * Scope a query based on conditions
     *
     * @param \Illuminate\Database\Eloquent\Builder<Form> $query
     * @param mixed $options
     * @return mixed
     */
    public static function scopeCommonConditions($query, $options = null)
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        $rio = $user->rio;
        $service = ServiceSelected::getSelected();
        $issueStartDate = isset($options['issue_start_date']) ? $options['issue_start_date'] : null;
        $issueEndDate = isset($options['issue_end_date']) ? $options['issue_end_date'] : null;
        $expirationStartDate = isset($options['expiration_start_date']) ? $options['expiration_start_date'] : null;
        $expirationEndDate = isset($options['expiration_end_date']) ? $options['expiration_end_date'] : null;
        $deliveryDeadlineStartDate = isset($options['delivery_deadline_start_date']) ? $options['delivery_deadline_start_date'] : null;
        $deliveryDeadlineEndDate = isset($options['delivery_deadline_end_date']) ? $options['delivery_deadline_end_date'] : null;
        $receiptStartDate = isset($options['receipt_start_date']) ? $options['receipt_start_date'] : null;
        $receiptEndDate = isset($options['receipt_end_date']) ? $options['receipt_end_date'] : null;
        $paymentStartDate = isset($options['payment_start_date']) ? $options['payment_start_date'] : null;
        $paymentEndDate = isset($options['payment_end_date']) ? $options['payment_end_date'] : null;
        $deliveryStartDate = isset($options['delivery_start_date']) ? $options['delivery_start_date'] : null;
        $deliveryEndDate = isset($options['delivery_end_date']) ? $options['delivery_end_date'] : null;
        $amountMin = isset($options['amount_min']) ? $options['amount_min'] : null;
        $amountMax = isset($options['amount_max']) ? $options['amount_max'] : null;

        // Filter base on current service
        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                $query->where('forms.rio_id', $rio->id);
                break;
            case ServiceSelectionTypes::NEO:
                $query->where('forms.neo_id', $service->data->id);
                break;
            default:
                break;
        }

        // Sort records
        if (isset($options['sort_by'])) {
            switch ($options['sort_by']) {
                case FormSortTypes::NEWEST_ISSUE_DATE:
                    $query->orderBy('issue_date', 'DESC');
                    break;
                case FormSortTypes::OLDEST_ISSUE_DATE:
                    $query->orderBy('issue_date', 'ASC');
                    break;
                default:
                    break;
            }
        }

        // Search base on free word
        if (isset($options['free_word'])) {
            $query->having('form_no', 'LIKE', "%{$options['free_word']}%")
                ->orHaving('title', 'LIKE', "%{$options['free_word']}%")
                ->orHaving('name', 'LIKE', "%{$options['free_word']}%");
        }

        // Search for issue date
        if ($issueStartDate || $issueEndDate) {
            $query = Form::getBetweenSearch($query, 'issue_date', $issueStartDate, $issueEndDate);
        }

        // Search for expiration date
        if ($expirationStartDate || $expirationEndDate) {
            $query = Form::getBetweenSearch($query, 'expiration_date', $expirationStartDate, $expirationEndDate);
        }

        // Search for delivery deadline date
        if ($deliveryDeadlineStartDate || $deliveryDeadlineEndDate) {
            $query = Form::getBetweenSearch($query, 'delivery_deadline', $deliveryDeadlineStartDate, $deliveryDeadlineEndDate);
        }

        // Search for receipt date
        if ($receiptStartDate || $receiptEndDate) {
            $query = Form::getBetweenSearch($query, 'receipt_date', $receiptStartDate, $receiptEndDate);
        }

        // Search for payment date
        if ($paymentStartDate || $paymentEndDate) {
            $query = Form::getBetweenSearch($query, 'payment_date', $paymentStartDate, $paymentEndDate);
        }

        // Search for delivery date
        if ($deliveryStartDate || $deliveryEndDate) {
            $query = Form::getBetweenSearch($query, 'delivery_date', $deliveryStartDate, $deliveryEndDate);
        }

        // Search for amount
        if ($amountMin || $amountMax) {
            $query = Form::getBetweenSearch($query, 'price', $amountMin, $amountMax);
        }

        return $query;
    }

    /**
     * Get rio connection users pair per auth user and other rio
     *
     * @param mixed $query
     * @param mixed $column
     * @param mixed $start
     * @param mixed $end
     * @return mixed
     */
    public static function getBetweenSearch($query, $column, $start, $end)
    {
        if (isset($start) && isset($end)) {
            return $query->whereBetween($column, [$start, $end]);
        }

        if (isset($start)) {
            return $query->where($column, '>=', $start);
        }

        if (isset($end)) {
            return $query->where($column, '<=', $end);
        }
    }

    /**
     * Scope a query to get all form information based on type.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Form> $query
     * @param int|null $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeFormDetails($query, $type = null)
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

        $query
            ->select([
                'forms.*',
            ])
            ->selectRaw('
                (CASE
                    WHEN forms.supplier_rio_id IS NOT NULL
                        THEN TRIM(CONCAT(rios.family_name, " ", rios.first_name))
                    WHEN forms.supplier_neo_id IS NOT NULL
                        THEN neos.organization_name
                    ELSE NULL
                END) AS connected_supplier_name
            ')
            ->leftJoin('rios', 'rios.id', '=', 'forms.supplier_rio_id')
            ->leftJoin('rio_profiles', 'rio_profiles.rio_id', '=', 'forms.supplier_rio_id')
            ->leftJoin('neos', 'neos.id', '=', 'forms.supplier_neo_id')
            ->leftJoin('neo_profiles', 'neo_profiles.neo_id', '=', 'forms.supplier_neo_id')
            ->with('products');

        if (!empty($type)) {
            $query->where('forms.type', $type);
        }

        return $query;
    }

    /**
     * Check if form is owned by service selected.
     *
     * @param \App\Models\Form $form
     * @param int $type
     * @return bool
     */
    public function isOwned($form, $type)
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $service->data->id === $form->rio_id
                    && $form->type == $type;
            case ServiceSelectionTypes::NEO:
                return $service->data->id === $form->neo_id
                    && $form->type == $type;
            default:
                return false;
        }
    }

    /**
     * Check if able to create quotation & invoice from different form types.
     *
     * @param \App\Models\Form $form
     * @param int $type
     * @return bool
     */
    public function isFormCreatable($form, $type)
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        switch ($type) {
            case Types::RECEIPT:
                $isMatch = in_array($form->type, [Types::INVOICE, Types::RECEIPT]);
                break;
            case Types::INVOICE:
                $isMatch = in_array($form->type, [Types::INVOICE, Types::QUOTATION]);
                break;
            case Types::PURCHASE_ORDER:
                $isMatch = in_array($form->type, [Types::PURCHASE_ORDER, Types::QUOTATION]);
                break;
            case Types::DELIVERY_SLIP:
                $isMatch = in_array($form->type, [Types::DELIVERY_SLIP, Types::QUOTATION]);
                break;
            default:
                $isMatch = false;
                break;
        }

        switch ($service->type) {
            case ServiceSelectionTypes::RIO:
                return $service->data->id === $form->rio_id
                    && $isMatch;
            case ServiceSelectionTypes::NEO:
                return $service->data->id === $form->neo_id
                    && $isMatch;
            default:
                return false;
        }
    }

    /**
     * Scope a query based on conditions
     *
     * @param mixed $options
     * @param mixed $query
     * @return mixed
     */
    public static function scopeDeletedForms($query, $options = null)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $query
            ->selectRaw('users.email AS deleter_email')
            ->leftJoin('users', 'users.rio_id', '=', 'forms.deleter_rio_id')
            ->where('forms.deleter_rio_id', $user->rio->id)
            ->whereNotNull('forms.deleted_at')
            ->withTrashed()
            ->orderBy('forms.deleted_at', 'desc');

        return $query;
    }

    /**
     * Get the form' s issuer image.
     *
     * @return string
     */
    public function getIssuerDetailLogoAttribute()
    {
        if (!empty($this->attributes['issuer_image'])) {
            // Initialize image paths
            $imagePaths = [
                config('bphero.profile_image_storage_path'),
                config('bphero.profile_image_directory') . config('bphero.profile_image_filename'),
                config('bphero.basic_settings_storage_path')
            ];

            // Issuer image is stored in storage path or is the default image
            foreach ($imagePaths as $imagePath) {
                if (str_contains($this->attributes['issuer_image'], $imagePath)) {
                    return $this->attributes['issuer_image'];
                }
            }

            // Issuer image is stored in s3
            $publicStorage = Storage::disk(config('bphero.public_bucket'));
            $path = config('bphero.form_issuer_image') . '/' . $this->attributes['issuer_image'];

            return $publicStorage->url($path);
        }

        $basicSetting  = !empty($this->attributes['rio_id'])
            ? FormBasicSetting::whereRioId($this->attributes['rio_id'])->first()
            : FormBasicSetting::whereNeoId($this->attributes['neo_id'])->first();

        return $this->attributes['issuer_image'] ? $basicSetting->image : null;
    }

    /**
     * Get the form's issuer image.
     *
     * @return mixed
     */
    public function getIssuerDetailLogoRawAttribute()
    {
        if (!empty($this->attributes['issuer_image'])) {
            // Initialize image paths
            $imagePaths = [
                config('bphero.profile_image_storage_path'),
                config('bphero.profile_image_directory') . config('bphero.profile_image_filename'),
                config('bphero.basic_settings_storage_path')
            ];

            // Issuer image is stored in storage path or is the default image
            foreach ($imagePaths as $imagePath) {
                if (str_contains($this->attributes['issuer_image'], $imagePath)) {
                    return $this->attributes['issuer_image'];
                }
            }

            // Issuer image is stored in s3
            $publicStorage = Storage::disk(config('bphero.public_bucket'));
            $path = config('bphero.form_issuer_image') . '/' . $this->attributes['issuer_image'];

            return base64_encode($publicStorage->get($path));
        }

        $basicSetting  = !empty($this->attributes['rio_id'])
            ? FormBasicSetting::whereRioId($this->attributes['rio_id'])->first()
            : FormBasicSetting::whereNeoId($this->attributes['neo_id'])->first();

        /** @var string */
        $image = file_get_contents($basicSetting->image);

        return $this->attributes['issuer_image'] ? base64_encode($image) : null;
    }

    /**
     * Get the form' s issuer image.
     *
     * @return boolean
     */
    public function getIsSupplierConnectedAttribute()
    {
        if (!$this->supplier_rio_id && !$this->supplier_neo_id) {
            return false;
        }

        return true;
    }

    /**
     * Get the form' s receipt amount if form is receipt or invoice
     *
     * @return int|string
     */
    public function getReceiptAmountAttribute()
    {
        if ($this->type === Types::RECEIPT || $this->type === Types::INVOICE) {
            return $this->price;
        }

        return 0;
    }

    /**
     * Duplicate image and return image name
     *
     * @param \App\Models\Form $form
     * @param mixed $formHistory
     * @return mixed
     */
    public static function duplicateImage($form, $formHistory = null)
    {
        // Issuer image is stored in s3
        $disk = Storage::disk(config('bphero.public_bucket'));

        /** @var string */
        $image = $form->issuer_image ?? null;
        $explode = explode("_", $image)[0];
        $formId = (string)$form->id;
        $convertedName = str_replace($explode, $formId, $image);

        // Form directory
        $targetDirectory = config('bphero.form_issuer_image');

        // Storage paths
        $fullStoragePath = "{$targetDirectory}/{$form->issuer_image}";
        $updatedStoragePath = "{$targetDirectory}/{$convertedName}";

        if (!$disk->exists($fullStoragePath) || empty($form->issuer_image)) {
            return $form->issuer_image;
        }

        if ($formHistory) {
            $fileInfo =  pathinfo($image, PATHINFO_EXTENSION);
            $historyDirectory = config('bphero.form_histories_issuer_image') . $formHistory->form_id;
            $historyFilename = $formHistory->id . '_' . Carbon::now()->format('YmdHis');
            $historyStoragePath = "{$historyDirectory}/{$historyFilename}.{$fileInfo}";

            $disk->copy($fullStoragePath, $historyStoragePath);

            return $disk->url($historyStoragePath);
        }

        $disk->copy($fullStoragePath, $updatedStoragePath);

        return $convertedName;
    }

    /**
     * Handle issuer image on form duplicate
     *
     * @param \App\Models\Form $form
     * @param mixed $formHistory
     * @return mixed
     */
    public static function issuerImageDuplicate($form, $formHistory = null)
    {
        // Return when image is null
        if (empty($form->issuer_image)) {
            return $form->issuer_image;
        }

        // Generate target directory
        $targetDirectory = config('bphero.form_issuer_image');
        $imagePaths = [
            config('bphero.profile_image_storage_path'),
            config('bphero.profile_image_directory') . config('bphero.profile_image_filename'),
            config('bphero.basic_settings_storage_path')
        ];
        $image = $form->issuer_image ?? '';

        // Issuer image is stored in storage path or is the default image
        foreach ($imagePaths as $imagePath) {
            if (str_contains($image, $imagePath)) {
                return $image;
            }
        }

        // Execute when image is stored in s3
        // Prepare filename for duplicated image
        $imageName = explode('_', $image);
        $imageName[0] = $form->id;
        $targetFilename = implode('_', $imageName);
        $currentDate =  Carbon::now()->format('YmdHis');

        // Prepare image paths
        $fileExtension =  pathinfo($image);
        $extension = $fileExtension['extension'] ?? '';
        $formImagePath = "{$targetDirectory}/{$image}";
        $duplicateImagePath = "{$targetDirectory}/{$targetFilename}";

        // Execute when form history is not null
        if (!empty($formHistory)) {
            $historyDirectory = config('bphero.form_histories_issuer_image') . $formHistory->form_id;
            $historyImagePath = "{$historyDirectory}/{$formHistory->id}_{$currentDate}.{$extension}";

            // Create a copy to forms histories image directory
            Storage::disk(config('bphero.public_bucket'))->copy($formImagePath, $historyImagePath);

            // Return url to image
            return Storage::disk(config('bphero.public_bucket'))->url($historyImagePath);
        }

        // Create a duplicate to forms image directory
        Storage::disk(config('bphero.public_bucket'))->copy($formImagePath, $duplicateImagePath);

        // Return filename
        return $targetFilename;
    }
}
