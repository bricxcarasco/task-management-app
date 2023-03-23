<?php

namespace App\Http\Controllers\Chat;

use App\Enums\Chat\ChatStatuses;
use App\Enums\Chat\ChatTypes;
use App\Enums\ServiceSelectionTypes;
use App\Events\Chat\ReceiveMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Http\Requests\Chat\DeleteMessageRequest;
use App\Http\Resources\Chat\MessageResource;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\Document;
use App\Models\Rio;
use App\Models\User;
use App\Objects\TalkSubject;
use Illuminate\Support\Facades\DB;
use Session;

class MessageController extends Controller
{
    /**
     * Chat Rooms List Action
     *
     * @param \App\Models\Chat $chat
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Chat $chat)
    {
        /** @var User */
        $user = auth()->user();

        /** @var Rio */
        $rio = $user->rio;

        if (Session::has('isConnectedGroup')) {
            TalkSubject::setSelected($rio);
            Session::forget('isConnectedGroup');
        }

        // Get selected talk subject
        $talkSubject = TalkSubject::getSelected();

        // Get talk subject list
        $talkSubjects = TalkSubject::getList();

        // Get chat participant entity
        switch ($talkSubject->type) {
            case ServiceSelectionTypes::RIO:
                $participant = ChatParticipant::whereChatId($chat->id)
                    ->whereRioId($talkSubject->data->id)
                    ->with('rio')
                    ->orderBy('id', 'desc')
                    ->first();
                break;

            case ServiceSelectionTypes::NEO:
                $participant = ChatParticipant::whereChatId($chat->id)
                    ->whereNeoId($talkSubject->data->id)
                    ->with('neo')
                    ->orderBy('id', 'desc')
                    ->first();
                break;
            default:
                $participant = null;
                break;
        }

        // Guard clause for non-existing participant record
        if (empty($participant) || $chat->status !== ChatStatuses::ACTIVE) {
            abort(404);
        }

        // Set RIO/NEO receivers if chat type is CONNECTED
        if ($chat->chat_type == ChatTypes::CONNECTED) {
            // Get RIO chat receiver
            $rioReceiver = ChatParticipant::whereChatId($chat->id)
                ->where('rio_id', '!=', $talkSubject->data->id)
                ->with('rio')
                ->first();

            // Get NEO chat receiver
            $neoReceiver = ChatParticipant::whereChatId($chat->id)
                ->where('neo_id', '!=', $talkSubject->data->id)
                ->with('neo')
                ->first();

            $chat['rio_receiver'] = $rioReceiver->rio ?? null;
            $chat['neo_receiver'] = $neoReceiver->neo ?? null;
        }

        // Read meessage and update last message id of chat conversation
        $participant->readMessages();

        return view('chat.message.index', compact(
            'user',
            'rio',
            'talkSubject',
            'talkSubjects',
            'chat',
            'participant'
        ));
    }

    /**
     * Fetch messages of a talk room.
     *
     * @param int $id Chat ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages(int $id)
    {
        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        // Get chat
        $chat = Chat::whereId($id)->firstOrFail();

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible chat
        if (!$chat->isChatParticipant()) {
            return response()->respondNotFound();
        }

        // Get chat messages
        $messages = ChatMessage::messageList($id)->get();

        // Get messages as json resource
        $messages = MessageResource::collection($messages);

        return response()->respondSuccess($messages);
    }

    /**
     * Create new chat message data after send.
     *
     * @param \App\Http\Requests\Chat\SendMessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(SendMessageRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        // Get chat
        $chat = Chat::whereId($requestData['chat_id'])->firstOrFail();

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible chat
        if (!$chat->isChatParticipant()) {
            return response()->respondNotFound();
        }

        // Get chat participant
        /** @var ChatParticipant */
        $chatParticipant = ChatParticipant::findOrFail($requestData['chat_participant_id']);

        DB::beginTransaction();

        try {
            // Create new chat message
            $chatMessage = new ChatMessage();
            $chatMessage->fill($requestData);

            // Create attachment records for uploaded files
            $uploadCodes = $requestData['upload_file'] ?? [];
            $documentIds = Document::createAttachments($chat, $chatParticipant, $uploadCodes);

            // Inject file attachments
            if (!empty($documentIds)) {
                $chatMessage->attaches = json_encode($documentIds, JSON_FORCE_OBJECT) ?: null;
            }

            if (!empty($requestData['attaches'])) {
                $attaches = $requestData['attaches'];

                $chatMessage->attaches = json_encode($attaches, JSON_FORCE_OBJECT) ?: null;

                $chat->createDocumentAccesses($attaches[0]);
            }

            // Save chat message
            $chatMessage->save();

            // Update last message ID of chat sender
            $chatParticipant->last_read_chat_message_id = $chatMessage->id;
            $chatParticipant->save();

            // Commit database changes
            DB::commit();


            // Get chat with associated data
            $chatMessage = ChatMessage::messageList($chat->id)->find($chatMessage->id);

            // Get messages as json resource
            $chatMessage = MessageResource::collection([$chatMessage]);

            // Broadcast message to channels
            ReceiveMessages::dispatch($chat->id);

            return response()->respondSuccess($chatMessage);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);

            return response()->respondInternalServerError([$exception->getMessage()]);
        }
    }

    /**
     * Delete chat message
     *
     * @param \App\Http\Requests\Chat\DeleteMessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMessage(DeleteMessageRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                'data' => $user->rio,
                'type' => ServiceSelectionTypes::RIO
            ]));
        }
        $service = json_decode(Session::get('ServiceSelected'));

        // Get chat message
        $chatMessage = ChatMessage::whereId($requestData['message_id'])->firstOrFail();

        // Handle authorization if chat message belong to active entity
        $this->authorize('delete', [ChatMessage::class, $chatMessage, $service]);

        DB::beginTransaction();

        try {
            $chatMessage->delete();

            // Commit database changes
            DB::commit();

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);

            return response()->respondInternalServerError([$exception->getMessage()]);
        }
    }
}
