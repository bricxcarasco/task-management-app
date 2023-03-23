<?php

namespace App\Http\Controllers\Connection;

use App\Enums\Connection\GroupStatuses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Connection\CreateGroupConnectionRequest;
use App\Http\Requests\Connection\Group\InviteMemberRequest;
use App\Http\Requests\Connection\UpdateGroupConnectionRequest;
use App\Http\Resources\Connection\Group\InviteMemberResource;
use App\Models\Chat;
use App\Models\GroupConnection;
use App\Models\GroupConnectionUser;
use App\Models\Notification;
use App\Models\Rio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class GroupController extends Controller
{
    /**
     * Connection groups list page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        /** @var User */
        $user = auth()->user();

        /** @var Rio */
        $rio = $user->rio;

        // Get list of connected groups query
        $connections = GroupConnectionUser::groupConnections()
            ->connected();

        Session::flash('isConnectedGroup', true);

        // Get group connection list & total
        $connectionList = $connections->get();
        $totalConnections = $connections->count();

        return view('connection.groups.index', compact(
            'rio',
            'connectionList',
            'totalConnections'
        ));
    }

    /**
     * Connection groups invitation requests list page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function invitations()
    {
        /** @var User */
        $user = auth()->user();

        /** @var Rio */
        $rio = $user->rio;

        // Get list of group invitation requests query
        $invitations = GroupConnectionUser::groupConnections()
            ->invitationRequests();

        // Get group invitations & total
        $invitationList = $invitations->get();
        $totalInvitations = $invitations->count();

        return view('connection.groups.invitation', compact(
            'rio',
            'invitationList',
            'totalInvitations'
        ));
    }

    /**
     * Connection groups registration page.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        /** @var User */
        $user = auth()->user();

        /** @var Rio */
        $rio = $user->rio;

        // Generate default group name
        $now = Carbon::now()->format('Ymd');
        $defaultName = __('Connection group default name', [
            'name' => $rio->full_name,
            'date' => $now,
        ]);

        return view('connection.groups.create', compact('rio', 'defaultName'));
    }

    /**
     * Store connection group information.
     *
     * @param \App\Http\Requests\Connection\CreateGroupConnectionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateGroupConnectionRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        /** @var Rio */
        $rio = $user->rio;

        try {
            DB::beginTransaction();

            // Get validated data
            $requestData = $request->validated();

            // Create chat room
            $chat = Chat::createConnectedGroupChat($rio, $requestData['group_name']);

            // Create group connection
            $connection = GroupConnection::create([
                'group_name' => $requestData['group_name'],
                'status' => GroupStatuses::REQUESTING,
                'rio_id' => $rio->id,
                'chat_id' => $chat->id,
            ]);

            // Create group connection user
            GroupConnectionUser::create([
                'group_connection_id' => $connection->id,
                'rio_id' => $rio->id,
                'status' => GroupStatuses::CONNECTED,
                'invite_message' => '', // Not nullable field
            ]);

            // Add rio to chat participants
            $chat->addParticipant($rio);

            DB::commit();

            return redirect()
                ->route('connection.groups.index')
                ->withAlertBox('success', __('Group has been created'));
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return redirect()
                ->route('connection.groups.index')
                ->withAlertBox('danger', __('Failed to save data'));
        }
    }

    /**
     * Update connection group information.
     *
     * @param \App\Http\Requests\Connection\UpdateGroupConnectionRequest $request
     * @param \App\Models\GroupConnection $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateGroupConnectionRequest $request, GroupConnection $group)
    {
        $this->authorize('update', [GroupConnection::class, $group]);

        // Get request data
        $requestData = $request->validated();

        DB::beginTransaction();

        try {
            // Update group connection
            $group->update($requestData);

            // Change chat room name when group name changes
            if (!empty($requestData['group_name'])) {
                $chat = $group->chat;

                if (!empty($chat)) {
                    $chat->update([
                        'room_name' => $requestData['group_name'],
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            // Set error flash message
            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Server Error'),
            ]);

            return response()->respondInternalServerError();
        }

        // Set success flash message
        session()->put('alert', [
            'status' => 'success',
            'message' => __('Connection group has been updated'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Delete connection group and associated group users.
     *
     * @param \App\Models\GroupConnection $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(GroupConnection $group)
    {
        $this->authorize('delete', [GroupConnection::class, $group]);

        // Delete group connection & remove group users
        $group->delete();

        // Set success flash message
        session()->put('alert', [
            'status' => 'success',
            'message' => __('Connection group has been deleted'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Fetch group connection members list.
     *
     * @param \App\Models\GroupConnection $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function membersList(GroupConnection $group)
    {
        // Get connected group users
        $groupMembers = $group
            ->users()
            ->connected()
            ->with('rio')
            ->get();

        return response()->respondSuccess($groupMembers);
    }

    /**
     * Display invite members page
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function inviteMembers(Request $request, GroupConnection $group)
    {
        $this->authorize('view', [GroupConnection::class, $group]);

        /** @var User */
        $user = auth()->user();

        /** @var Rio */
        $rio = $user->rio;

        return view('connection.groups.invite-members', compact(
            'rio',
            'group'
        ));
    }

    /**
     * Search API endpoint for connected rio with invite statuses
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\GroupConnection $group
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function searchInviteMembers(Request $request, GroupConnection $group)
    {
        $this->authorize('view', [GroupConnection::class, $group]);

        // Get request data
        $requestData = $request->all();

        /** @var User */
        $user = auth()->user();

        // Check if group is full
        $isGroupFull = $group->isFull();

        // Get connected rios to authenticated user
        $rios = Rio::connectionGroupInviteMembers($group->id, $user->rio_id)
            ->commonConditions($requestData)
            ->paginate(config('bphero.paginate_count'));

        // Return resouce collection
        return InviteMemberResource::collection($rios)
            ->additional(['meta' => [
                'is_full' => $isGroupFull,
            ]]);
    }

    /**
     * Invite connected rio user to group
     *
     * @param \App\Http\Requests\Connection\Group\InviteMemberRequest $request
     * @param \App\Models\GroupConnection $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function inviteMember(InviteMemberRequest $request, GroupConnection $group)
    {
        $this->authorize('invite', [GroupConnection::class, $group]);

        // Get request data
        $requestData = $request->validated();

        /** @var User */
        $user = auth()->user();

        // Check if group is full
        if ($group->isFull()) {
            return response()->respondForbidden();
        }

        // Get connected rio with no pending invite
        $rio = Rio::connectionGroupInviteMembers($group->id, $user->id)
            ->where('rios.id', $requestData['rio_id'])
            ->whereNull('group_connection_statuses.status')
            ->first();

        // Check if rio exists
        if (empty($rio)) {
            return response()->respondForbidden();
        }

        DB::beginTransaction();

        try {
            // Create invite connection
            $invite = new GroupConnectionUser();
            $invite->fill($requestData);
            $invite->status = GroupStatuses::REQUESTING;
            $invite->group_connection_id = $group->id;
            $invite->save();

            // Send email to the invited member
            $invite->sendEmailToConnectionGroupUser($group, $rio);

            // Record new notification
            Notification::createNotification([
                'rio_id' => $rio->id,
                'destination_url' => route('connection.groups.invitations'),
                'notification_content' => __('Notification Content - Connection Group Invitation', [
                    'group_name' => $group->group_name
                ]),
            ]);

            DB::commit();

            return response()->respondSuccess($invite);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Delete invite member request
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\GroupConnectionUser $invite
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteInvite(Request $request, GroupConnectionUser $invite)
    {
        $this->authorize('deleteInvite', [GroupConnectionUser::class, $invite]);

        // Delete invite
        $invite->delete();

        return response()->respondSuccess();
    }
}
