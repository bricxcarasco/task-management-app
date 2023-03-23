<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Enums\Connection\ListFilters;
use App\Enums\ElectronicContract\ElectronicContractStatuses;
use App\Http\Requests\ElectronicContract\SelectedRecipientRequest;
use App\Http\Requests\ElectronicContract\CmSignCallbackRequest;
use App\Http\Resources\ElectronicContract\ConnectionListResource;
use App\Http\Resources\ElectronicContract\NeoEmailListResource;
use App\Http\Resources\ElectronicContract\RioEmailListResource;
use App\Enums\ServiceSelectionTypes;
use App\Exceptions\CmComException;
use App\Helpers\ElectronicContractHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ElectronicContract\StoreRequest;
use App\Models\ElectronicContract;
use App\Models\NeoExpert;
use App\Models\User;
use App\Models\RioConnection;
use App\Objects\ServiceSelected;
use App\Services\CmCom\SignService;
use Illuminate\Support\Facades\DB;
use Session;

class ElectronicContractController extends Controller
{
    /**
     * Display Connection list.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function connectionList(Request $request)
    {
        // Get request data
        $requestData = $request->all();
        $requestData['mode'] = $requestData['mode'] ?? ListFilters::SHOW_ALL;

        $service = json_decode(Session::get('ServiceSelected'));

        //Manipulate search options
        if ((isset($requestData['kana_search']) && !is_null($requestData['kana_search']))
            && RioConnection::connectedList($service, $requestData)->count() < 1
        ) {
            $requestData['search'] = $requestData['kana_search'];
            unset($requestData['kana_search']);
        }

        //Get connected list
        $result = RioConnection::connectedList($service, $requestData)
            ->paginate(config('bphero.paginate_count'))
            ->withQueryString();

        return ConnectionListResource::collection($result);
    }

    /**
     * Display Emails.
     *
     * @param \App\Http\Requests\ElectronicContract\SelectedRecipientRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function emailList(SelectedRecipientRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        if ($requestData['service'] === ServiceSelectionTypes::NEO) {
            $neoEmails = NeoExpert::where('neo_experts.attribute_code', 'email')
                ->where('neo_experts.neo_id', $requestData['id'])->get();

            if (empty($neoEmails)) {
                return response()->respondNotFound();
            }

            return NeoEmailListResource::collection($neoEmails);
        } else {
            $rioEmails = User::where('users.rio_id', $requestData['id'])->get();

            if (empty($rioEmails)) {
                return response()->respondNotFound();
            }

            return RioEmailListResource::collection($rioEmails);
        }
    }

    /**
     * Store electronic contracts entity
     *
     * @param \App\Http\Requests\ElectronicContract\StoreRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function store(StoreRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        $service = ServiceSelected::getSelected();

        $this->authorize('store', [ElectronicContract::class, $service]);

        try {
            $signService = new SignService();
            $contract = new ElectronicContract();

            // Set action user
            $contract->setActionUser($service);

            // Set recipient
            $recipient = $contract->setRecipient($requestData);

            // Get document file path
            $documentPath = ElectronicContractHelper::getRequestFilePath($requestData);

            // Guard clause for non-existing upload document file
            if (empty($documentPath)) {
                return response()->respondBadRequest();
            }

            // Upload document
            $uploadResponse = $signService->uploadDocument($documentPath);

            // Guard clause for non-existing upload document file
            if (empty($uploadResponse['id'])) {
                return response()->respondBadRequest($uploadResponse);
            }

            // Prepare sender name
            $senderName = $service->data->full_name ?? $service->data->organization_name;

            // Create dossier
            $signResponse = $signService->createDossier($uploadResponse['id'], [
                'contract_name' => __('Electronic Contract Header', ['name' => $senderName]),
                'sender_name' => $senderName,
                'sender_email' => $requestData['sender_email'],
                'recipient_name' => $recipient['recipient_name'],
                'recipient_email' => $recipient['recipient_email'],
            ]);

            // Guard clause for non-existing upload document file
            if (empty($signResponse['id'])) {
                return response()->respondBadRequest($signResponse);
            }

            // Get invitee id from response
            $inviteeId = $signResponse['invitees'][0]['id'] ?? null;

            // Guard clause for non-existing invitee information
            if (empty($inviteeId)) {
                return response()->respondBadRequest($signResponse);
            }

            // Set CMCOM-related data
            $contract->fill([
                'contract_document_id' => $uploadResponse['id'],
                'dossier_id' => $signResponse['id'],
                'invitee_id' => $inviteeId,
            ]);

            // Save electronic contract
            $contract->save();

            // Get available slot information
            /** @var array */
            $availableSlot = ElectronicContract::availableSlot($service);

            // Prepare response data
            $responseData = [
                'slot_data' => $availableSlot,
                'prepare_url' => $signResponse['prepareUrl'],
            ];

            return response()->respondSuccess($responseData);
        } catch (\Exception $exception) {
            report($exception);

            return response()->respondInternalServerError();
        }
    }

    /**
     * CM Sign API will this method whenever the status of a Document/Contract in CM sign is updated.
     *
     * @param \App\Http\Requests\ElectronicContract\CmSignCallbackRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function updateContractStatus(CmSignCallbackRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        DB::beginTransaction();

        try {
            // Update
            $electronicContract = ElectronicContract::unprepared()
                ->whereDossierId($requestData['dossier_id'])
                ->first();

            // Handle non-existing electronic contracts
            if (empty($electronicContract)) {
                return response()->respondNotFound();
            }

            // Disregard non-prepared events
            if ($requestData['type'] !== 'dossier.prepared') {
                return response()->respondSuccess();
            }

            // Disregard non-draft document
            if ($requestData['dossier_state'] !== 'draft') {
                return response()->respondSuccess();
            }

            // Update record to prepared
            $electronicContract->status = ElectronicContractStatuses::PREPARED;
            $electronicContract->save();

            // Initialize sign service
            $signService = new SignService();

            // Send invite to recipient
            $sendInviteResponse = $signService->sendInvite($requestData['dossier_id'], $electronicContract->invitee_id);

            // Get id as a success result
            $sendInviteId = $sendInviteResponse[0]['id'] ?? null;

            // Handle non-existing send invite transaction id
            if (empty($sendInviteId)) {
                throw new CmComException('Unable to submit invite.');
            }

            // Send notification to receiver once electronic contract is prepared
            ElectronicContract::sendNotification($electronicContract);

            // Commit changes
            DB::commit();

            return response()->respondSuccess();
        } catch (CmComException $exception) {
            report($exception);
            DB::rollBack();

            return response()->respondBadRequest();
        } catch (\Exception $exception) {
            report($exception);
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }
}
