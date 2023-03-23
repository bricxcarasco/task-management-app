<?php

namespace App\Http\Controllers\Neo;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Neo\UpdateMemberRolesRequest;
use App\Http\Resources\Neo\AdministratorMemberResource;
use App\Enums\Neo\RoleType;
use App\Models\Neo;
use App\Models\NeoBelong;

class AdministratorController extends Controller
{
    /**
     * NEO administrator pae for assigning member, admin and owner roles
     *
     * @param Neo $neo
     * @return \Illuminate\View\View
     */
    public function index(Neo $neo)
    {
        $this->authorize('owner', [Neo::class, $neo]);

        $owner = new AdministratorMemberResource($neo->owner);
        $administrators = AdministratorMemberResource::collection($neo->administrators);
        $members = AdministratorMemberResource::collection($neo->members);

        $neoProfileLink = route('neo.profile.introduction', $neo->id);
        $transferOwnerLink = route('neo.administrator.owner', $neo->id);

        return view('neo.administrator.index', compact(
            'neo',
            'owner',
            'administrators',
            'members',
            'neoProfileLink',
            'transferOwnerLink'
        ));
    }

    /**
     * NEO administrator pae for assigning member, admin and owner roles
     *
     * @param Neo $neo
     * @return \Illuminate\View\View
     */
    public function owner(Neo $neo)
    {
        $this->authorize('owner', [Neo::class, $neo]);

        $owner = new AdministratorMemberResource($neo->owner);
        $administrators = $neo->administrators;
        $members = $neo->members;

        $members = AdministratorMemberResource::collection($members->merge($administrators));

        $neoAdministratorLink = route('neo.administrator.index', $neo->id);
        $transferOwnershipPost = route('neo.administrator.update.owner', $neo->id);

        return view('neo.administrator.transfer_ownership', compact(
            'neo',
            'owner',
            'members',
            'neoAdministratorLink',
            'transferOwnershipPost',
        ));
    }

    /**
     * API for setting or removing RIO as an adinistrator in NEO
     *
     * @param UpdateMemberRolesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setRemoveAdministrator(UpdateMemberRolesRequest $request, Neo $neo)
    {
        $this->authorize('owner', [Neo::class, $neo]);

        $neoBelong = NeoBelong::getRioConnected($neo->id, $request->rio_id);

        if (!$neoBelong) {
            return response()->respondNotFound();
        }

        // Used DB transaction for multiple and complex query process
        DB::beginTransaction();
        try {
            $neoBelong->role = $request->type;
            $neoBelong->save();

            $administrators = AdministratorMemberResource::collection($neo->administrators);
            $members = AdministratorMemberResource::collection($neo->members);

            // If successfully processed insert all information
            DB::commit();

            return response()->respondSuccess(
                [
                    "administrators" => $administrators,
                    "members" => $members
                ],
                __('messages.permission_changed')
            );
        } catch (\Exception $e) {
            // Rollback query process if encountered problems like server error
            DB::rollback();
            Log::debug($e->getMessage());

            return response()->respondInternalServerError();
        }
    }

    /**
     * API for transferring ownership od current user to assigned RIO
     *
     * @param UpdateMemberRolesRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function setOwner(UpdateMemberRolesRequest $request, Neo $neo)
    {
        $this->authorize('owner', [Neo::class, $neo]);

        /** @var \App\Models\User */
        $user  = auth()->user();

        $neoBelongCurrentOwner = NeoBelong::getRioConnected($neo->id, $user->rio_id);
        $neoBelongNewOwner = NeoBelong::getRioConnected($neo->id, $request->rio_id);

        if (!$neoBelongCurrentOwner || !$neoBelongNewOwner) {
            return redirect()
                ->back()
                ->withAlertBox('danger', __('Not Found'));
        }

        // Used DB transaction for multiple and complex query process
        DB::beginTransaction();
        try {
            $neoBelongCurrentOwner->role = RoleType::MEMBER;
            $neoBelongCurrentOwner->save();

            $neoBelongNewOwner->role = RoleType::OWNER;
            $neoBelongNewOwner->save();

            // If successfully processed insert all information
            DB::commit();

            return redirect()
                ->route('neo.profile.introduction', [
                    "neo" => $neo->id
                ])
                ->withAlertBox('success', __('messages.permission_changed'));
        } catch (\Exception $e) {
            // Rollback query process if encountered problems like server error
            DB::rollback();
            Log::debug($e->getMessage());

            return redirect()
                ->back()
                ->withAlertBox('danger', __('Server Error'));
        }
    }
}
