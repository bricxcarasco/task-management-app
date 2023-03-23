<?php

namespace App\Http\Controllers\Rio;

use App\Enums\Chat\ChatStatuses;
use App\Enums\ConnectionStatuses as EnumsConnectionStatuses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Rio\Connection\UpdateConnectionRequest;
use App\Enums\ConnectionStatusType;
use App\Enums\Rio\ConnectionStatusType as RioConnectionStatus;
use App\Enums\ServiceSelectionTypes;
use App\Models\Chat;
use App\Models\Neo;
use App\Models\NeoConnection;
use App\Models\Notification;
use App\Models\User;
use App\Models\Rio;
use App\Models\RioConnection;
use App\Models\RioConnectionUser;
use Session;

class ConnectionController extends Controller
{
    /**
     * Apply connection to other Rio
     *
     * @param UpdateConnectionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function connect(UpdateConnectionRequest $request)
    {
        /** @var User */
        $user = auth()->user();
        $data = $request->validated();
        $status = "";

        /** @var Rio */
        $otherRio = Rio::findOrFail($data['rio_id']);

        $checkConnectionUsers = RioConnectionUser::getRioConnectionUsersPair($otherRio);

        // Used DB transaction for multiple and complex query process
        DB::beginTransaction();
        try {
            $service = json_decode(Session::get('ServiceSelected'));

            // Set notification data
            $notification = [
                'rio_id' => $otherRio->id,
                'destination_url' => route('connection.application-list'),
            ];

            if ($service->type === ServiceSelectionTypes::RIO) {
                if (!empty($checkConnectionUsers)) {
                    return response()->respondBadRequest();
                }

                $connection = new RioConnection();
                $connection->created_rio_id = $user->rio_id;
                $connection->status = ConnectionStatusType::PENDING;
                $connection->message = $data['message'] ?? null;
                $connection->save();

                $connectionUsers = [
                    [
                        "rio_connection_id" => $connection->id,
                        "rio_id" => $user->rio_id,
                    ],
                    [
                        "rio_connection_id" => $connection->id,
                        "rio_id" => $otherRio->id,
                    ]
                ];
                RioConnectionUser::insert($connectionUsers);

                // Get RIO sender
                /** @var Rio */
                $sender = Rio::whereId($service->data->id)->first();

                // Send email notification to RIO connection application
                // For RIO to RIO connection application
                $connection->sendEmailToConnection($sender, $otherRio);

                // Update notification data
                $notification += [
                    'rio_id' => $otherRio->id,
                    'notification_content' => __('Notification Content - Connection Application', [
                        'sender_name' => $sender->full_name . 'さん'
                    ]),
                ];

                $status = RioConnectionStatus::CANCELLATION;
            } else {
                $connection = new NeoConnection();
                $connection->neo_id = $service->data->id;
                $connection->connection_rio_id = $otherRio->id;
                $connection->status = EnumsConnectionStatuses::APPLYING_BY_NEO;
                $connection->message = $data['message'] ?? null;
                $connection->save();

                // Get NEO sender
                /** @var Neo */
                $sender = Neo::whereId($service->data->id)->first();

                // Send email notification to RIO connection application
                // For NEO to RIO application
                $connection->sendEmailToConnection($sender, $otherRio);

                // Update notification data
                $notification += [
                    'rio_id' => $otherRio->id,
                    'notification_content' => __('Notification Content - Connection Application', [
                        'sender_name' => $sender->organization_name
                    ]),
                ];

                $status = EnumsConnectionStatuses::APPLYING_BY_NEO;
            }

            // Record new notification
            Notification::createNotification($notification);

            // If successfully processed insert all information
            DB::commit();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            Log::debug($e);

            return response()->respondInternalServerError();
        }

        return response()->respondSuccess(["connection_status" => $status]);
    }

    /**
     * Cancel or disconnect Rio connection to other Rio
     *
     * @param UpdateConnectionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelDisconnect(UpdateConnectionRequest $request)
    {
        /** @var User */
        $user = auth()->user();
        $data = $request->validated();
        $status = "";

        /** @var Rio */
        $otherRio = Rio::findOrFail($data['rio_id']);

        $checkConnectionUsers = RioConnectionUser::getRioConnectionUsersPair($otherRio);
        $connection = $user->rio->rio_connections->where('id', key((array)$checkConnectionUsers))->first();
        if (empty($connection)) {
            $connection = $otherRio->rio_connections->where('id', key((array)$checkConnectionUsers))->first();
        }
        $service = json_decode(Session::get('ServiceSelected'));

        /** @var Object */
        $checkNeoConnection = NeoConnection::whereNeoId($service->data->id)
            ->whereConnectionRioId($otherRio->id);

        $checkChatConnection = Chat::getChatParticipantsPair($otherRio, $service->type);
        $chatConnection = $user->rio->chats->where('id', key((array)$checkChatConnection))->first();

        if (empty($chatConnection)) {
            $chatConnection = $otherRio->chats->where('id', key((array)$checkChatConnection))->first();
        }

        // Used DB transaction for multiple and complex query process
        DB::beginTransaction();
        try {
            if ($service->type === ServiceSelectionTypes::RIO) {
                if (empty($checkConnectionUsers) || empty($connection)) {
                    return response()->respondBadRequest();
                }

                $connection->delete();

                $status = RioConnection::connectionStatus($otherRio, false);
            } else {
                $checkNeoConnection->delete();

                $status = EnumsConnectionStatuses::PENDING;
            }

            if ($chatConnection) {
                $chatConnection->update([
                    'status' => ChatStatuses::ARCHIVE
                ]);
            }
            // If successfully processed update all information
            DB::commit();
        } catch (\Exception $e) {
            // Rollback query process if encountered problems like server error
            DB::rollback();
            Log::debug($e->getMessage());

            return response()->respondInternalServerError();
        }

        return response()->respondSuccess(["connection_status" => $status]);
    }
}
