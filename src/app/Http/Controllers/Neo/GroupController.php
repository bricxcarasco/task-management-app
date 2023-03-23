<?php

namespace App\Http\Controllers\Neo;

use App\Enums\Chat\ChatStatuses;
use App\Enums\Chat\ChatTypes;
use App\Enums\NeoBelongStatuses;
use App\Enums\Neo\RoleType;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Neo\UpsertNeoGroupRequest;
use App\Http\Requests\Neo\AddGroupMemberRequest;
use App\Http\Requests\Chat\CreateNeoGroupRequest;
use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\Neo;
use App\Models\NeoGroup;
use App\Models\NeoGroupUser;
use App\Models\Notification;
use App\Models\Rio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;

class GroupController extends Controller
{
    /**
     * Register a new NEO group.
     *
     * @param \App\Http\Requests\Neo\UpsertNeoGroupRequest $request
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(UpsertNeoGroupRequest $request, Neo $neo)
    {
        /** @var User */
        $user = auth()->user();

        /** @var Rio */
        $rio = $user->rio;

        // Get validated data
        $requestData = $request->validated();

        // Create Chat room
        $chat = Chat::create([
            'owner_neo_id' => $neo->id,
            'created_rio_id' => $rio->id,
            'chat_type' => ChatTypes::NEO_GROUP,
            'room_name' => $requestData['group_name'],
            'status' => ChatStatuses::ACTIVE,
        ]);

        // Create NEO group
        NeoGroup::create([
            'neo_id' => $neo->id,
            'rio_id' => $rio->id,
            'chat_id' => $chat->id,
            'group_name' => $requestData['group_name'],
        ]);

        // Set success flash message
        session()->put('alert', [
            'status' => 'success',
            'message' => __('Group has been created'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Update NEO group information.
     *
     * @param \App\Http\Requests\Neo\UpsertNeoGroupRequest $request
     * @param \App\Models\NeoGroup $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpsertNeoGroupRequest $request, NeoGroup $group)
    {
        $this->authorize('update', [NeoGroup::class, $group]);

        // Get request data
        $requestData = $request->validated();

        // Update neo group
        $group->update($requestData);

        // Update chat room name
        Chat::whereId($group->chat_id)
            ->update([
                'room_name' => $requestData['group_name']
            ]);

        // Set success flash message
        session()->put('alert', [
            'status' => 'success',
            'message' => __('Group has been updated'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Delete NEO group and associated group users.
     *
     * @param \App\Models\NeoGroup $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(NeoGroup $group)
    {
        $this->authorize('delete', [NeoGroup::class, $group]);

        // Delete neo group & remove group users
        $group->delete();

        // Set success flash message
        session()->put('alert', [
            'status' => 'success',
            'message' => __('Group has been deleted'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Fetch NEO group members list.
     *
     * @param \App\Models\NeoGroup $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function membersList(NeoGroup $group)
    {
        /** @var User */
        $user = auth()->user();

        // Get NEO group users
        $groupMembers = $group
            ->users()
            ->with('rio')
            ->get();

        // Set leave group route url per group member
        $groupMembers->each(function ($member) {
            $member['leave_url'] = route('neo.profile.group.remove-member', [
                'id' => $member->id
            ]);

            /** @var Rio */
            $currentRio = Rio::whereId($member->rio_id)->with('rio_profile')->first();

            $member['rio_details'] = $currentRio;

            return $member;
        });

        return response()->respondSuccess([
            'user' => $user,
            'members' => $groupMembers,
        ]);
    }

    /**
     * Registers NEO group user upon joining a group.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function join($id)
    {
        /** @var User */
        $user = auth()->user();

        /** @var Rio */
        $rio = $user->rio;

        // Get NEO group
        $group = NeoGroup::whereId($id)->first();

        if (!$group) {
            // Set error flash message
            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Failed to join the group'),
            ]);

            return response()->respondNotFound();
        }

        // Create NEO group user
        NeoGroupUser::create([
            'neo_group_id' => $group->id,
            'rio_id' => $rio->id,
        ]);

        // Create Chat participant
        ChatParticipant::create([
            'chat_id' => $group->chat_id,
            'rio_id' => $rio->id,
        ]);

        // Set success flash message
        session()->put('alert', [
            'status' => 'success',
            'message' => __('I joined the group'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Deletes NEO group user upon leaving a group.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function leave($id)
    {
        /** @var User */
        $user = auth()->user();

        // Get NEO group
        $group = NeoGroup::whereId($id)->first();

        if (!$group) {
            // Set error flash message
            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Failed to leave the group'),
            ]);

            return response()->respondNotFound();
        }

        // Delete NEO group user
        NeoGroupUser::whereRioId($user->rio_id)
            ->whereNeoGroupId($group->id)
            ->delete();

        // Delete Chat participant
        ChatParticipant::whereRioId($user->rio_id)
            ->whereChatId($group->chat_id)
            ->delete();

        // Set success flash message
        session()->put('alert', [
            'status' => 'success',
            'message' => __('I left the group'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Deletes NEO group user upon removal.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeMember($id)
    {
        // Get NEO group user
        $groupUser = NeoGroupUser::whereId($id)
            ->with('group')
            ->first();

        if (!$groupUser) {
            // Set error flash message
            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Failed to remove the member'),
            ]);

            return response()->respondNotFound();
        }

        // Delete Chat participant
        ChatParticipant::whereRioId($groupUser->rio_id)
            ->whereChatId($groupUser->group->chat_id)
            ->delete();

        // Delete NEO group user
        $groupUser->delete();

        return response()->respondSuccess();
    }

    /**
     * Register a new NEO group.
     *
     * @param \App\Http\Requests\Chat\CreateNeoGroupRequest $request
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function chatCreateNeoGroup(CreateNeoGroupRequest $request, Neo $neo)
    {
        DB::beginTransaction();

        try {
            /** @var User */
            $user = auth()->user();

            /** @var Rio */
            $rio = $user->rio;

            // Get validated data
            $requestData = $request->validated();

            // Create Chat room
            $chat = Chat::create([
                'owner_neo_id' => $neo->id,
                'created_rio_id' => $rio->id,
                'chat_type' => ChatTypes::NEO_GROUP,
                'room_name' => $requestData['group_name'],
                'status' => ChatStatuses::ACTIVE,
            ]);

            // Create NEO group
            NeoGroup::create([
                'neo_id' => $neo->id,
                'rio_id' => $rio->id,
                'chat_id' => $chat->id,
                'group_name' => $requestData['group_name'],
            ]);

            DB::Commit();

            //Get Neo belongs of authenticated user
            $neoBelong =  $user->rio->neos->where('id', $neo->id)->first();

            if (!$neoBelong) {
                return response()->respondNotFound();
            }

            Session::put('ServiceSelected', json_encode([
                "data" => $neoBelong,
                "type" => ServiceSelectionTypes::NEO
            ]));

            session()->put('alert', [
                'status' => 'success',
                'message' => __('Group has been created'),
            ]);

            return response()->respondSuccess(null, __('Group has been created'));
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Fetch NEO participants list.
     *
     * @param \App\Models\NeoGroup $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function participatingUserList(NeoGroup $group)
    {
        /** @var User */
        $user = auth()->user();

        $groupName = $group->group_name;

        /** @var Neo */
        $neo = Neo::whereId($group->neo_id)->first();

        // Get all participants except the owner
        $participants = $neo->rios()
            ->with('rio_profile')
            ->wherePivot('role', '!=', RoleType::OWNER)
            ->wherePivot('status', NeoBelongStatuses::AFFILIATED)
            ->get();

        // Check if participants are already a group member
        foreach ($participants as $participant) {
            // Get NEO group user
            $groupUser = NeoGroupUser::whereRioId($participant->id)
                ->whereNeoGroupId($group->id)
                ->with('group')
                ->first();

            $participant['is_not_participating'] = is_null($groupUser);
        }

        $addUrl = route('neo.profile.group.add-members', ['id' => $group->id]);

        return response()->respondSuccess([
            'user' => $user,
            'participants' => $participants,
            'group_name' => $groupName,
            'add_url' => $addUrl,
        ]);
    }

    /**
     * Add selected participants for a NEO group
     *
     * @param \App\Http\Requests\Neo\AddGroupMemberRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addGroupMember(AddGroupMemberRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $neoGroupUser = [];
            $chatParticipant = [];
            $notifications = [];
            $now = Carbon::now();

            // Get validated data
            $requestData = $request->validated();

            // Get NEO group
            $group = NeoGroup::whereId($id)->first();

            if (!$group) {
                // Set error flash message
                session()->put('alert', [
                    'status' => 'danger',
                    'message' => __('Failed to join the group'),
                ]);

                return response()->respondNotFound();
            }

            if (!empty($requestData['rio_id'])) {
                foreach ($requestData['rio_id'] as $rioID) {
                    array_push($neoGroupUser, [
                        'neo_group_id' => $group->id,
                        'rio_id' => $rioID,
                    ]);

                    array_push($chatParticipant, [
                        'chat_id' => $group->chat_id,
                        'rio_id' => $rioID,
                    ]);

                    array_push($notifications, [
                        'rio_id' => $rioID,
                        'notification_content' => __('Notification Content - Added to NEO group', [
                            'neo_name' => $group->neo->organization_name,
                            'neo_group_name' => $group->group_name
                        ]),
                        'destination_url' => route('neo.profile.groups-list', $group->neo->id),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            } else {
                // Set error flash message
                session()->put('alert', [
                    'status' => 'danger',
                    'message' => __('Invalid Parameters'),
                ]);

                return response()->respondInvalidParameters();
            }

            // Create Chat participants
            NeoGroupUser::insert($neoGroupUser);

            // Create Chat participants
            ChatParticipant::insert($chatParticipant);

            // Send notification to newly added members
            Notification::insert($notifications);

            DB::commit();

            // Set success flash message
            session()->put('alert', [
                'status' => 'success',
                'message' => __('Participants have been added to the group'),
            ]);

            return response()->respondSuccess(null, __('Participants have been added to the group'));
        } catch (\Exception $e) {
            DB::rollBack();

            // Set error flash message
            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Server Error'),
            ]);

            return response()->respondInternalServerError();
        }
    }
}
