<?php

namespace App\Policies;

use App\Enums\Neo\RoleType;
use App\Enums\Neo\NeoBelongStatusType;
use App\Models\NeoBelong;
use Illuminate\Auth\Access\HandlesAuthorization;

class NeoBelongPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the rio affiliate.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\NeoBelong $affiliate
     * @return  bool
     */
    public function update($user, NeoBelong $affiliate)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return false;
        }

        // Check if neos is affiliated to rio user
        return ($user->rio_id === $affiliate->rio_id);
    }

    /**
     * Determine whether the user can delete the rio affiliate.
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\NeoBelong $affiliate
     * @return  bool
     */
    public function delete($user, NeoBelong $affiliate)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return false;
        }

        // Check if neos is affiliated to rio user
        return ($user->rio_id === $affiliate->rio_id && $affiliate->role !== RoleType::OWNER && $affiliate->status === NeoBelongStatusType::AFFILIATE);
    }

    /**
     * Determine whether the participation request from RIO is for the current NEO Owner requester.
     *
     * @param   \App\Models\User $user
     * @param   \App\Http\Requests\Neo\ParticipationRequest $requestData
     * @param   \App\Models\Neo $neoData
     * @return  \Illuminate\Auth\Access\Response
     */
    public function approveParticipant($user, $requestData, $neoData)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return NeoBelong::isParticipationRequestBelongToNeo($requestData, $neoData)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the participation request from RIO is for the current NEO Owner requester.
     *
     * @param   \App\Models\User $user
     * @param   \App\Http\Requests\Neo\ParticipationRequest $requestData
     * @param   \App\Models\Neo $neoData
     * @return  \Illuminate\Auth\Access\Response
     */
    public function rejectParticipant($user, $requestData, $neoData)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return NeoBelong::isParticipationRequestBelongToNeo($requestData, $neoData)
            ? $this->allow()
            : $this->deny();
    }

    /**
     * Determine whether the participation invitation to RIO is for the current NEO Owner requester.
     *
     * @param   \App\Models\User $user
     * @param   \App\Http\Requests\Neo\ParticipationInvitationRequest $requestData
     * @param   \App\Models\Neo $neoData
     * @return  \Illuminate\Auth\Access\Response
     */
    public function cancelInvitation($user, $requestData, $neoData)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return NeoBelong::isParticipationInvitationBelongToNeo($requestData, $neoData)
            ? $this->allow()
            : $this->deny();
    }
}
