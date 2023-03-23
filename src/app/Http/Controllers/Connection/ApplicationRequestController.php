<?php

namespace App\Http\Controllers\Connection;

use App\Enums\Chat\ChatStatuses;
use App\Enums\ConnectionStatusType;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Connection\UpdateApplicationConnectionRequest;
use App\Enums\Chat\ConnectedChatType;
use App\Enums\ConnectionStatuses;
use App\Models\Chat;
use App\Models\Neo;
use App\Models\NeoConnection;
use App\Models\Rio;
use App\Models\RioConnection;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use DB;
use Session;

class ApplicationRequestController extends Controller
{
    /**
     * Accept application
     *
     * @param UpdateApplicationConnectionRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(UpdateApplicationConnectionRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        $data = $request->validated();

        $model = $this->getRioOrNeoViaServiceType($data);
        $service = json_decode(Session::get('ServiceSelected'));
        $chatConnection = null;
        /** @var Neo */
        $currentNeo = Neo::find($service->data->id);

        if (!$model) {
            return response()->respondNotFound();
        }

        switch ($service->type) {
            case $service->type === ServiceSelectionTypes::RIO && $data['service'] === ServiceSelectionTypes::RIO:
                $checkChatConnection = Chat::getChatParticipantsPair($model, $data['service']);
                $chatConnection = $user->rio->chats->where('id', key((array)$checkChatConnection))->first();

                if (empty($chatConnection) && $data['service'] !== ServiceSelectionTypes::NEO) {
                    $chatConnection = $model->chats->where('id', key((array)$checkChatConnection))->first();
                }
                break;
            case $service->type === ServiceSelectionTypes::RIO && $data['service'] === ServiceSelectionTypes::NEO:
                $checkChatConnection = Chat::getChatParticipantsPair($model, $data['service'], ConnectedChatType::RIO_TO_NEO);
                $chatConnection = $user->rio->chats->where('id', key((array)$checkChatConnection))->first();

                if (empty($chatConnection) && $data['service'] !== ServiceSelectionTypes::NEO) {
                    $chatConnection = $model->chats->where('id', key((array)$checkChatConnection))->first();
                }
                break;
            case $service->type === ServiceSelectionTypes::NEO && $data['service'] === ServiceSelectionTypes::RIO:
                $checkChatConnection = Chat::getChatParticipantsPair($model, ServiceSelectionTypes::RIO, ConnectedChatType::NEO_TO_RIO);
                $chatConnection = $model->chats->where('id', key((array)$checkChatConnection))->first();

                if (empty($chatConnection)) {
                    $chatConnection = $currentNeo->chats->where('id', key((array)$checkChatConnection))->first();
                }
                break;
            case $service->type === ServiceSelectionTypes::NEO && $data['service'] === ServiceSelectionTypes::NEO:
                $checkChatConnection = Chat::getChatParticipantsPair($model, ServiceSelectionTypes::NEO, ConnectedChatType::NEO_TO_NEO);
                $chatConnection = $model->chats->where('id', key((array)$checkChatConnection))->first();

                if (empty($chatConnection)) {
                    $chatConnection = $currentNeo->chats->where('id', key((array)$checkChatConnection))->first();
                }
                break;
            default:
              break;
        }

        // Used DB transaction for multiple and complex query process
        DB::beginTransaction();
        try {
            $connectedChatType = ConnectedChatType::RIO_TO_RIO;

            /** @var RioConnection|NeoConnection */
            $connection = (object) null;

            if ($data['service'] === ServiceSelectionTypes::RIO && $service->type === ServiceSelectionTypes::NEO) {
                $connection = NeoConnection::whereId($data['connection_id'])
                    ->whereConnectionRioId($model->id)
                    ->whereStatus(ConnectionStatusType::PENDING)
                    ->firstOrFail();

                $connectedChatType = ConnectedChatType::RIO_TO_NEO;
            }

            if ($data['service'] === ServiceSelectionTypes::NEO  && $service->type === ServiceSelectionTypes::NEO) {
                $connection = NeoConnection::whereId($data['connection_id'])
                    ->whereConnectionNeoId($model->id)
                    ->whereStatus(ConnectionStatusType::PENDING)
                    ->firstOrFail();

                $connectedChatType = ConnectedChatType::NEO_TO_NEO;
            }

            if ($data['service'] === ServiceSelectionTypes::RIO && $service->type === ServiceSelectionTypes::RIO) {
                $connection = RioConnection::whereId($data['connection_id'])
                    ->whereCreatedRioId($model->id)
                    ->whereStatus(ConnectionStatusType::PENDING)
                    ->firstOrFail();

                $connectedChatType = ConnectedChatType::RIO_TO_RIO;
            }

            if ($data['service'] === ServiceSelectionTypes::NEO && $service->type === ServiceSelectionTypes::RIO) {
                $connection = NeoConnection::whereConnectionRioId($service->data->id)
                    ->whereNeoId($data['id'])
                    ->whereStatus(ConnectionStatuses::APPLYING_BY_NEO)
                    ->firstOrFail();

                $connectedChatType = ConnectedChatType::RIO_TO_NEO;
            }

            $connection->status = ConnectionStatusType::AFFILIATED;
            $connection->save();

            if ($chatConnection) {
                $chatConnection->update([
                    'status' => ChatStatuses::ACTIVE
                ]);
            } else {
                Chat::createConnectedChat($connection, $connectedChatType);
            }

            //If successfully processed insert all information
            DB::commit();

            return response()->respondSuccess([], __('Approved the connection application'));
        } catch (\Exception $e) {
            // Rollback query process if encountered problems like server error
            DB::rollback();
            Log::debug($e->getMessage());

            return response()->respondInternalServerError();
        }
    }

    /**
     * Decline application
     *
     * @param UpdateApplicationConnectionRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function decline(UpdateApplicationConnectionRequest $request)
    {
        $data = $request->validated();

        $model = $this->getRioOrNeoViaServiceType($data);
        $service = json_decode(Session::get('ServiceSelected'));

        if (!$model) {
            return response()->respondNotFound();
        }

        // Used DB transaction for multiple and complex query process
        DB::beginTransaction();
        try {
            if ($data['service'] === ServiceSelectionTypes::RIO && $service->type === ServiceSelectionTypes::NEO) {
                $connection = NeoConnection::whereId($data['connection_id'])
                    ->whereConnectionRioId($model->id)
                    ->whereStatus(ConnectionStatusType::PENDING)
                    ->firstOrFail();

                $connection->delete();
            }

            if ($data['service'] === ServiceSelectionTypes::RIO && $service->type === ServiceSelectionTypes::RIO) {
                $connection = RioConnection::whereId($data['connection_id'])
                    ->whereCreatedRioId($model->id)
                    ->whereStatus(ConnectionStatusType::PENDING)
                    ->firstOrFail();

                $connection->delete();
            }

            if ($data['service'] === ServiceSelectionTypes::NEO  && $service->type === ServiceSelectionTypes::NEO) {
                $connection = NeoConnection::whereId($data['connection_id'])
                    ->whereConnectionNeoId($model->id)
                    ->whereStatus(ConnectionStatusType::PENDING)
                    ->firstOrFail();

                $connection->delete();
            }

            if ($data['service'] === ServiceSelectionTypes::NEO && $service->type === ServiceSelectionTypes::RIO) {
                $connection = NeoConnection::whereConnectionRioId($service->data->id)
                    ->whereNeoId($data['id'])
                    ->whereStatus(ConnectionStatuses::APPLYING_BY_NEO)
                    ->firstOrFail();

                $connection->delete();
            }

            // If successfully processed insert all information
            DB::commit();

            return response()->respondSuccess([], __('The application request has been rejected'));
        } catch (\Exception $e) {
            // Rollback query process if encountered problems like server error
            DB::rollback();
            Log::debug($e->getMessage());

            return response()->respondInternalServerError();
        }
    }

    /**
     * Get model instance RIO or NEO
     *
     * @param array $data
     *
     * @return object
     */
    private function getRioOrNeoViaServiceType($data)
    {
        return $data['service'] === ServiceSelectionTypes::RIO ? Rio::findOrFail($data['id']) : Neo::findOrFail($data['id']);
    }
}
