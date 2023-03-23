<?php

namespace App\Models;

use App\Enums\ElectronicContract\ElectronicContractStatuses;
use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ServiceSelectionTypes;
use App\Exceptions\ServiceSessionNotFoundException;
use App\Objects\ServiceSelected;
use Carbon\Carbon;

class ElectronicContract extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'electronic_contracts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rio_id',
        'neo_id',
        'created_rio_id',
        'contract_document_id',
        'dossier_id',
        'invitee_id',
        'recipient_rio_id',
        'recipient_neo_id',
        'recipient_email',
        'status'
    ];

    /**
     * Define relationship for RIO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rio()
    {
        return $this->belongsTo(Rio::class);
    }

    /**
     * Define relationship for NEO
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function neo()
    {
        return $this->belongsTo(Neo::class);
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
     * Set action user of electronic contract record
     *
     * @param object|null $service
     * @return \App\Models\ElectronicContract
     */
    public function setActionUser($service = null)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $entity = empty($service)
            ? ServiceSelected::getSelected()
            : $service;

        // Prepare action user
        switch ($entity->type) {
            case ServiceSelectionTypes::RIO:
                $this->fill([
                    'rio_id' => $entity->data->id,
                    'neo_id' => null,
                ]);
                break;
            case ServiceSelectionTypes::NEO:
                $this->fill([
                    'rio_id' => null,
                    'neo_id' => $entity->data->id,
                ]);
                break;
            default:
                throw new ServiceSessionNotFoundException();
        }

        // Set created rio id
        $this->created_rio_id = $user->rio_id;

        return $this;
    }

    /**
     * Set recipient of electronic contract entity
     *
     * @param array $data
     * @return array
     */
    public function setRecipient($data)
    {
        // Initialize recipient information
        $recipient = [
            'recipient_name' => $data['recipient_name'] ?? '',
            'recipient_email' => $data['recipient_email'] ?? '',
        ];

        // Set recipient email
        $this->recipient_email = $recipient['recipient_email'];

        // Prepare recipient when connection is selected
        if (!empty($data['selected_connection_type'])) {
            switch ($data['selected_connection_type']) {
                case ServiceSelectionTypes::RIO:
                    /** @var Rio */
                    $rio = Rio::findOrFail($data['selected_connection_id']);
                    $this->fill([
                        'recipient_rio_id' => $data['selected_connection_id'],
                        'recipient_neo_id' => null,
                    ]);

                    // Get recipient name
                    $recipient['recipient_name'] = $rio->full_name_kana;

                    break;
                case ServiceSelectionTypes::NEO:
                    /** @var Neo */
                    $neo = Neo::findOrFail($data['selected_connection_id']);
                    $this->fill([
                        'recipient_rio_id' => null,
                        'recipient_neo_id' => $data['selected_connection_id'],
                    ]);

                    // Get recipient name
                    $recipient['recipient_name'] = $neo->organization_kana;

                    break;
            }
        }

        return $recipient;
    }

    /**
     * Scope a query to get available electronic contract slots value
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param object $service
     * @return mixed
     */
    public function scopeAvailableSlot($query, $service)
    {
        /** @var User */
        $user = auth()->user();

        $fullSlot = config('bphero.electronic_contract_free_slots');
        $validityPeriod = config('bphero.electronic_contract_expiration_date');
        $availableSlot = 0;
        $expired = true;

        $expirationDate = Carbon::parse($validityPeriod);
        $dateNow = Carbon::now()->toDateString();
        if ($expirationDate->gte($dateNow)) {
            $query->when($service->type === ServiceSelectionTypes::NEO, function ($neoQuery) use ($service) {
                return $neoQuery->where('neo_id', '=', $service->data->id);
            })
                ->when($service->type === ServiceSelectionTypes::RIO, function ($rioQuery) use ($user) {
                    return $rioQuery->where('rio_id', '=', $user->rio_id);
                });

            $availableSlot = $fullSlot - $query->count();
            $expired = false;
        }

        return [
            'slot' => max($availableSlot, 0),
            'expiration_date' => $validityPeriod,
            'expired' => $expired,
        ];
    }

    /**
     * Scope query for unprepared contracts
     *
     * @param mixed $query
     * @return mixed
     */
    public function scopeUnprepared($query)
    {
        return $query->where('electronic_contracts.status', ElectronicContractStatuses::CREATED);
    }

    /**
     * Send notification to the recipient of Electronic contract.
     *
     * @param \App\Models\ElectronicContract $electronicContract
     * @return void
     */
    public static function sendNotification($electronicContract)
    {
        // Initialize default sender and receiver
        $rioReceiverId = null;
        $senderName = null;

        // RIO sender
        if (!empty($electronicContract->rio_id)) {
            /** @phpstan-ignore-next-line */
            $senderName = $electronicContract->rio->full_name . 'さん';
        }

        // NEO sender
        if (!empty($electronicContract->neo_id)) {
            /** @phpstan-ignore-next-line */
            $senderName = $electronicContract->neo->organization_name;
        }

        // RIO receiver
        if (!empty($electronicContract->recipient_rio_id)) {
            $rioReceiverId = $electronicContract->recipient_rio_id;
        }

        // NEO receiver
        if (!empty($electronicContract->recipient_neo_id)) {
            /** @var Neo */
            $neoReceiver = Neo::whereId($electronicContract->recipient_neo_id)->first();
            /** @var int @phpstan-ignore-next-line */
            $rioReceiverId = $neoReceiver->owner ? $neoReceiver->owner->rio_id : null;
        }

        if (!empty($rioReceiverId)) {
            Notification::createNotification([
                'rio_id' => $rioReceiverId,
                'receive_neo_id' => $neoReceiver->id ?? null,
                'notification_content' => __('Notification Content - Electronic Contract Recipient', [
                    'sender_name' => $senderName
                ]),
            ]);
        }
    }
}
