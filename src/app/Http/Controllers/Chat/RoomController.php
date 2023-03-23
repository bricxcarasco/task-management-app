<?php

namespace App\Http\Controllers\Chat;

use App\Enums\Chat\ChatStatuses;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\SearchChatRoomsRequest;
use App\Http\Requests\Chat\UpdateTalkSubjectRequest;
use App\Http\Resources\Chat\ChatListResource;
use App\Models\Chat;
use App\Models\Rio;
use App\Models\User;
use App\Objects\TalkSubject;
use Session;

class RoomController extends Controller
{
    /**
     * Chat Rooms List Action
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        // Set talk subject
        if (!Session::has('talkSubject')) {
            /** @var User */
            $user = auth()->user();

            // Fetch RIO record
            $rio = $user->rio;
            TalkSubject::setSelected($rio);
        }

        // Get talk subject list
        $talkSubjects = TalkSubject::getList();

        // Get selected talk subject
        $session = TalkSubject::getSelected();

        return view('chat.room.index', compact(
            'talkSubjects',
            'session'
        ));
    }

    /**
     * Search API endpoint for chat room list
     *
     * @param \App\Http\Requests\Chat\SearchChatRoomsRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|array
     */
    public function searchChatRooms(SearchChatRoomsRequest $request)
    {
        $this->authorize('list', [Chat::class]);

        // Get request data
        $requestData = $request->validated();

        // Get selected talk subject
        $session = TalkSubject::getSelected();

        // Get chat room list
        $rooms = Chat::roomList($session)
            ->active()
            ->commonConditions($requestData)
            ->get();

        $result = [
            'data' => ChatListResource::collection($rooms),
            'current_session' => $session,
        ];

        return $result;
    }

    /**
    * Update talk subject
    *
    * @param \App\Http\Requests\Chat\UpdateTalkSubjectRequest $request
    * @return mixed
    */
    public function updateTalkSubject(UpdateTalkSubjectRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        $data = $request->validated();

        if ($data['type'] === ServiceSelectionTypes::RIO) {
            $taskSubject = $user->rio;
        } else {
            $taskSubject = $user->rio->neos->where('id', $request['id'])->first();
        }

        if (!$taskSubject) {
            return response()->respondNotFound();
        }

        TalkSubject::setSelected($taskSubject);

        return response()->respondSuccess($data);
    }

    /**
    * Archive Room
    *
    * @param \App\Models\Chat $chat
    * @return mixed
    */
    public function archiveRoom(Chat $chat)
    {
        $chat->update([
            'status' => ChatStatuses::ARCHIVE
        ]);

        return response()->respondSuccess();
    }

    /**
    * Restore Room
    *
    * @param \App\Http\Requests\Chat\SearchChatRoomsRequest $request
    * @return mixed
    */
    public function restoreRoom(SearchChatRoomsRequest $request)
    {
        $requestData = $request->validated();

        // Get selected talk subject
        $session = TalkSubject::getSelected();

        Chat::roomList($session)
            ->archive()
            ->commonConditions($requestData)
            ->update([
                'status' => ChatStatuses::ACTIVE
            ]);

        return response()->respondSuccess();
    }
}
