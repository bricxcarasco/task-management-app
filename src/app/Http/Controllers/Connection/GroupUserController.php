<?php

namespace App\Http\Controllers\Connection;

use App\Enums\Connection\GroupStatuses;
use App\Enums\LogTypes;
use App\Http\Controllers\Controller;
use App\Models\GroupConnection;
use App\Models\GroupConnectionUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\RioLog;
use Carbon\Carbon;

class GroupUserController extends Controller
{
    /**
     * Accept connection group invitation request.
     *
     * @param \App\Models\GroupConnectionUser $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptInvitation(GroupConnectionUser $user)
    {
        $this->authorize('update', [GroupConnectionUser::class, $user]);

        // Begin database transaction
        DB::beginTransaction();

        try {
            /** @var GroupConnection $groupConnectionRioId */
            $groupConnectionRioId = GroupConnection::where('id', $user->group_connection_id)
                ->first();

            // Update connection user status to connected
            $user->update([
                'status' => GroupStatuses::CONNECTED
            ]);

            if (!empty($user) && !empty($groupConnectionRioId)) {
                $this->createRIOAcceptInvitelog(
                    LogTypes::INVITE_ACCEPTED,
                    $user,
                    $groupConnectionRioId->rio_id,
                );
            }

            // Get rio
            $rio = $user->rio;

            // Guard clause for non-existing rio
            if (empty($rio)) {
                throw new ModelNotFoundException();
            }

            // Fetch chat relating to group the user joined
            $chat = $user->group
                ->chat()
                ->firstOrFail();

            // Add user to chat room
            $chat->addParticipant($rio);

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
            'message' => __('Invitation has been accepted'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Decline connection group invitation request.
     *
     * @param \App\Models\GroupConnectionUser $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function declineInvitation(GroupConnectionUser $user)
    {
        $this->authorize('update', [GroupConnectionUser::class, $user]);

        // Delete to decline group invitation
        $user->delete();

        // Set success flash message
        session()->put('alert', [
            'status' => 'success',
            'message' => __('Invitation has been declined'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Delete connection group user.
     *
     * @param \App\Models\GroupConnectionUser $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(GroupConnectionUser $user)
    {
        $this->authorize('delete', [GroupConnectionUser::class, $user]);

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Delete group connection user
            $user->delete();

            // Get rio
            $rio = $user->rio;

            // Guard clause for non-existing rio
            if (empty($rio)) {
                throw new ModelNotFoundException();
            }

            // Fetch chat relating to group the user joined
            $chat = $user->group
                ->chat()
                ->firstOrFail();

            // Remove user to chat room
            $chat->removeParticipant($rio);

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
            'message' => __('I left the connection group'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Create log for accepted RIO group invitation
     *
     * @param int $logType type of log in rio logs
     * @param GroupConnectionUser $user user object
     * @param int|null $rio_id rio_id of the inviter
     * @return void|null
     */
    private function createRIOAcceptInvitelog($logType, $user, $rio_id = null)
    {
        $groupConnectionAcceptedCnt = GroupConnectionUser::withTrashed()
            ->where('group_connection_id', $user->group_connection_id)
            ->where('status', GroupStatuses::CONNECTED)
            ->count();

        $logDetails = json_encode([
            'group_connection_id' => $user->group_connection_id,
            'rio_inviter_id' => $rio_id,
        ]);

        /** @var string $logDetails */
        $isRioLogExist = RioLog::where('rio_id', $rio_id)
            ->where('log_type', $logType)
            ->where('log_detail', $logDetails)
            ->exists();

        if ($groupConnectionAcceptedCnt == 3 && !$isRioLogExist) {
            $rioLog = new RioLog();
            /** @var int $rio_id */
            $rioLog->rio_id = $rio_id;
            $rioLog->logged_at = Carbon::now();
            $rioLog->log_type = $logType;
            $rioLog->log_detail = $logDetails;
            $rioLog->save();
        }
    }
}
