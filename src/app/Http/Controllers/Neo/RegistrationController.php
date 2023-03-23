<?php

namespace App\Http\Controllers\Neo;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\Neo\RegistrationRequest;
use App\Enums\AttributeCodes;
use App\Enums\PrefectureTypes;
use App\Enums\Neo\OrganizationAttributeType;
use App\Enums\Neo\AcceptParticipationType;
use App\Enums\Neo\RoleType;
use App\Enums\Neo\NeoBelongStatusType;
use App\Enums\Neo\RestrictConnectionType;
use App\Enums\NeoBelongStatuses;
use App\Models\GroupConnection;
use App\Models\GroupConnectionUser;
use App\Models\User;
use App\Models\Neo;
use App\Models\NeoProfile;
use App\Models\NeoPrivacy;
use App\Models\NeoBelong;
use App\Models\NeoExpert;

class RegistrationController extends Controller
{
    /**
     * Neo registration input page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(GroupConnection $group = null)
    {
        if ($group) {
            // Group connection policy
            $this->authorize('update', [GroupConnection::class, $group]);
        }

        $prefectures = PrefectureTypes::getValues();
        $organizationAttributes = OrganizationAttributeType::getValues();
        $acceptConnections = RestrictConnectionType::getValues();
        $acceptBelongs = AcceptParticipationType::getValues();

        return view('neo.registration.index', compact('prefectures', 'organizationAttributes', 'acceptConnections', 'acceptBelongs', 'group'));
    }

    /**
     * Neo registration confirmaton process
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function confirm(RegistrationRequest $request, GroupConnection $group = null)
    {
        // this process will proceed only if already validated
        // used this->only for convenient purposes to use model fill function
        if ($group) {
            // Group connection policy
            $this->authorize('update', [GroupConnection::class, $group]);
        }

        $neo = $request->neoAttributes();
        $neoProfile = $request->neoProfileAttributes();
        $neoPrivacy = $request->neoPrivacyAttributes();
        $neoExpertEmail = $request->neoExpertEmail();
        $neoExpertUrl = $request->neoExpertUrl();

        // session of neo registration input to be use upon confirm page and saving process
        $request->session()->put('neo.registration.input', [
            'neo' => $neo,
            'neoProfile' => $neoProfile,
            'neoPrivacy' => $neoPrivacy,
            'neoExpertEmail' => $neoExpertEmail,
            'neoExpertUrl' => $neoExpertUrl,
        ]);

        return view('neo.registration.confirm', compact('neo', 'neoProfile', 'neoPrivacy', 'neoExpertEmail', 'neoExpertUrl', 'group'));
    }

    /**
     * Save NEO data after confirmation page
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function complete(Request $request, GroupConnection $group = null)
    {
        if ($group) {
            // Group connection policy
            $this->authorize('update', [GroupConnection::class, $group]);
        }

        /** @var User */
        $user = auth()->user();
        // redirect if session not exists
        if (!$request->session()->has('neo.registration.input')) {
            return redirect()
                ->route('neo.registration.index')
                ->withAlertBox('danger', __('messages.server_error'));
        }

        $neoRegistrationInput = $request->session()->get('neo.registration.input');

        // Used DB transaction for multiple and complex query process
        DB::beginTransaction();
        try {
            /** @var Neo */
            $neo = Neo::create($neoRegistrationInput['neo']);
            $neoRegistrationInput['neoProfile']['neo_id'] = $neo->id;
            $neoRegistrationInput['neoPrivacy']['neo_id'] = $neo->id;

            NeoProfile::create($neoRegistrationInput['neoProfile']);
            NeoPrivacy::create($neoRegistrationInput['neoPrivacy']);

            /** @var NeoBelong */
            $neoBelong = new NeoBelong();
            $neoBelong->rio_id = $user->rio_id;
            $neoBelong->neo_id = $neo->id;
            $neoBelong->role = RoleType::OWNER;
            $neoBelong->status = NeoBelongStatusType::AFFILIATE;
            $neoBelong->save();

            // Save Email Address
            if (!is_null($neoRegistrationInput['neoExpertEmail']['email'])) {
                $email = new NeoExpert();
                $email->neo_id = $neo->id;
                $email->content = $neoRegistrationInput['neoExpertEmail']['email'];
                $email->attribute_code = AttributeCodes::EMAIL;
                $email->sort = 0;
                $email->save();
            }

            // Create new url for neo user
            if (!is_null($neoRegistrationInput['neoExpertUrl']['site_url'])) {
                $url = new NeoExpert();
                $url->neo_id = $neo->id;
                $url->content = $neoRegistrationInput['neoExpertUrl']['organization_name'];
                $url->information = $neoRegistrationInput['neoExpertUrl']['site_url'];
                $url->attribute_code = AttributeCodes::URL;
                $url->sort = 0;
                $url->save();
            }

            if ($group) {
                $groups = GroupConnectionUser::whereGroupConnectionId($group->id)
                    ->where('rio_id', '!=', $user->rio_id)
                    ->pluck('rio_id');

                $groupCollection = collect();

                foreach ($groups as $group_rio) {
                    $groupCollection->push([
                        'neo_id' => $neoBelong->neo_id,
                        'rio_id' => $group_rio,
                        'role' => RoleType::MEMBER,
                        'status' => NeoBelongStatuses::AFFILIATED,
                        'is_display' => 0
                    ]);
                }

                NeoBelong::insert($groupCollection->toArray());

                $group->delete();

                if ($group->chat) {
                    $group->chat->delete();
                }
            }

            // If successfully processed insert all information
            DB::commit();

            return redirect()
                ->route('neo.profile.introduction', $neo->id)
                ->withAlertBox('success', __('messages.neo_registration_saved'));
        } catch (\Exception $e) {
            // Rollback query process if encountered problems like server error
            DB::rollback();
            Log::debug($e->getMessage());

            return redirect()
                ->route('neo.registration.index')
                ->withAlertBox('danger', __('messages.server_error'));
        }
    }
}
