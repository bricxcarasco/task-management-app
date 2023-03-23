<?php

namespace App\Http\Controllers\Chat;

use App\Enums\Chat\ChatStatuses;
use App\Enums\Chat\ChatTypes;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\CreateNeoMessageRequest;
use App\Http\Requests\Chat\UpdateNeoMessageListRequest;
use App\Http\Requests\Chat\UpdateNeoMessageSearchRequest;
use App\Models\Chat;
use App\Models\Neo;
use App\Models\NeoConnection;
use App\Models\User;
use App\Objects\TalkSubject;
use DB;

class NeoMessageController extends Controller
{
    /**
     * Neo Message
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $session = TalkSubject::getSelected();

        if ($session->type !== ServiceSelectionTypes::NEO) {
            abort(404);
        }

        $lists = NeoConnection::neoMessageRecipients($session)
           ->paginate(config('bphero.paginate_count'));

        return view('chat.neo_message.index', compact(
            'lists',
            'session'
        ));
    }

    /**
     * Filter lists
     *
     * @param \App\Http\Requests\Chat\UpdateNeoMessageListRequest $request
     * @return mixed
     */
    public function filterList(UpdateNeoMessageListRequest $request)
    {
        $session = TalkSubject::getSelected();

        $data = $request->validated();

        $lists = NeoConnection::filterRecipient($session, $data['target'])
           ->paginate(config('bphero.paginate_count'));

        return response()->respondSuccess($lists);
    }

    /**
     * Create neo message
     *
     * @param \App\Http\Requests\Chat\CreateNeoMessageRequest $request
     * @return mixed
     */
    public function createNeoMessage(CreateNeoMessageRequest $request)
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;
        $session = TalkSubject::getSelected();
        $data = $request->validated();
        $neo = Neo::whereId($session->data->id)->first();
        $collection = collect();

        // Fetch profile image of owner
        $roomIcon = $neo
            ->neo_profile
            ->profile_image ?? null;

        $chat = Chat::create([
            'owner_rio_id' => $rio->id,
            'owner_neo_id' => $session->data->id,
            'created_rio_id' => $rio->id,
            'chat_type' => ChatTypes::NEO_MESSAGE,
            'room_name' => $data['title'],
            'room_icon' => $roomIcon,
            'room_caption' => null,
            'status' => ChatStatuses::ACTIVE,
       ]);

        //Include sender
        $collection->push([
            'chat_id' => $chat->id,
            'rio_id' => null,
            'neo_id' => $session->data->id
        ]);

        foreach ($data['recipients'] as $recipient) {
            $collection->push([
                'chat_id' => $chat->id,
                'rio_id' => $recipient['type'] === ServiceSelectionTypes::RIO ? $recipient['id'] : null,
                'neo_id' => $recipient['type'] === ServiceSelectionTypes::NEO ? $recipient['id'] : null
            ]);
        }

        try {
            DB::beginTransaction();

            $chat->participants()->createMany($collection->toArray());

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }

        return response()->respondSuccess($chat->id);
    }

    /**
     * Get selected pagination page
     *
     * @param \App\Http\Requests\Chat\UpdateNeoMessageListRequest $request
     * @return mixed
     */
    public function getSelectedPage(UpdateNeoMessageListRequest $request)
    {
        $session = TalkSubject::getSelected();
        $data = $request->validated();

        if (!$request['search']) {
            $result = NeoConnection::filterRecipient($session, $data['target'])
                ->paginate(config('bphero.paginate_count'), ['*'], 'page', $request['pageNo']);
        } else {
            $result = NeoConnection::neoMessageRecipients($session)
                ->searchRecipient($request['search'])
                ->paginate(config('bphero.paginate_count'), ['*'], 'page', $request['pageNo']);
        }

        return response()->respondSuccess($result);
    }

    /**
     * Update search data base on filter
     *
     * @param \App\Http\Requests\Chat\UpdateNeoMessageSearchRequest $request
     * @return mixed
     */
    public function updateList(UpdateNeoMessageSearchRequest $request)
    {
        $data = $request->validated();

        $session = TalkSubject::getSelected();

        $results = NeoConnection::filterRecipient($session, $data['target'])
            ->searchRecipient($data['search'])
            ->paginate(config('bphero.paginate_count'));

        return response()->respondSuccess($results);
    }
}
