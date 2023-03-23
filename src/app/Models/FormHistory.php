<?php

namespace App\Models;

use App\Enums\ServiceSelectionTypes;
use App\Objects\ServiceSelected;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FormHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_histories';

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'form_id',
        'operation_datetime',
        'operation_details',
        'operator_email',
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
     * Append relationship to the model's array form.
     *
     * @var array
     */
    protected $with = [
        'products'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'issuer_detail_logo'
    ];


    /**
     * Define relationship with Form
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function forms()
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Define relationship with Form products
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(FormProductHistory::class);
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
     * Get the form history' s issuer image.
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
     * Get missing path of a file name
     *
     * @param mixed $path Storage path
     * @param mixed $form
     * @return mixed
     */
    public static function getMissingPath($path, $form)
    {
        $disk = Storage::disk(config('bphero.public_bucket'));

        if ($disk->exists($path) && $form->issuer_image) {
            return $form->issuer_detail_logo;
        }
    }

    /**
     * Copy to history image path
     *
     * @param mixed $form Form history
     * @param mixed $filepond Initialize filepond file
     * @param mixed $filename
     * @return mixed
     */
    public static function copyToHistory($form, $filepond, $filename)
    {
        $uploadFilename = $filepond->getFileName();

        // Issuer image is stored in s3
        $disk = Storage::disk(config('bphero.public_bucket'));

        // Get file information
        $fileInfo =  pathinfo($uploadFilename);
        $historyFilename = $form->id . '_' . Carbon::now()->format('YmdHis');
        $extension = $fileInfo['extension'] ?? '';

        // Form directory
        $targetDirectory = config('bphero.form_issuer_image');

        // Form history directory
        $historyDirectory = config('bphero.form_histories_issuer_image').$form->form_id;

        $fullStoragePath = "{$targetDirectory}/{$filename}";
        $historyStoragePath = "{$historyDirectory}/{$historyFilename}.{$extension}";

        $disk->copy($fullStoragePath, $historyStoragePath);

        return $disk->url($historyStoragePath);
    }

    /**
     * Create image for history record
     *
     * @param mixed $issuerImage
     * @param mixed $formId
     * @param mixed $historyId
     * @return mixed
     */
    public static function generateIssuerImage($issuerImage, $formId, $historyId)
    {
        // Return if issuer image is null
        if (empty($issuerImage)) {
            return $issuerImage;
        }

        $imagePaths = [
            config('bphero.profile_image_storage_path'),
            config('bphero.profile_image_directory') . config('bphero.profile_image_filename'),
            config('bphero.basic_settings_storage_path')
        ];

        // Return if issuer image is stored in storage path or is the default image
        foreach ($imagePaths as $imagePath) {
            if (str_contains($issuerImage, $imagePath)) {
                return $issuerImage;
            }
        }

        // Generate target directory
        $targetDirectory = config('bphero.form_issuer_image');
        $historyDirectory = config('bphero.form_histories_issuer_image') . $formId;

        $currentDate =  Carbon::now()->format('YmdHis');
        $fileExtension =  pathinfo($issuerImage);
        $extension = $fileExtension['extension'] ?? '';
        $historyImagePath = "{$historyDirectory}/{$historyId}_{$currentDate}.{$extension}";
        $formImagePath = "{$targetDirectory}/{$issuerImage}";

        // Create a copy to forms histories image directory
        Storage::disk(config('bphero.public_bucket'))->copy($formImagePath, $historyImagePath);

        return Storage::disk(config('bphero.public_bucket'))->url($historyImagePath);
    }
}
