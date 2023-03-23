<?php

namespace App\Http\Controllers\Neo;

use App\Enums\Neo\RoleType;
use App\Enums\PrefectureTypes;
use App\Enums\LogTypes;
use App\Enums\NeoBelongStatuses;
use App\Http\Controllers\Controller;
use App\Models\NeoProfile;
use App\Models\Neo;
use App\Models\User;
use App\Models\NeoPrivacy;
use App\Models\NeoLog;
use App\Models\NeoBelong;
use App\Models\NeoConnection;
use App\Models\NeoGroup;
use App\Models\Rio;
use Session;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * NEO profile introduction page
     *
     * @param Neo $neo
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function introduction(Neo $neo)
    {
        /** @var bool $isAccessInProfile bool to determine if the access to the page is from the profile page */
        $isAccessInProfile = (!is_null(request()->input('is_access_in_profile'))) ? true : false ;

        /** @var User */
        $user = auth()->user();

        $neoBelong = NeoBelong::where('rio_id', $user->rio->id)
            ->where('neo_id', $neo->id)
            ->first();

        $isOwnerOrAdmin = NeoBelong::whereRioId($user->rio->id)
            ->whereNeoId($neo->id)
            ->first();

        // Check if already a participant
        /** @var bool */
        $isParticipant = NeoBelong::isExistingParticipant($neo->id);

        // NEO privacy setting
        $privacySetting = NeoPrivacy::whereNeoId($neo->id)->first();

        // Service selected
        $serviceSelected = json_decode(Session::get('ServiceSelected'));

        $neoConnection = NeoConnection::IsExistingNeoConnection($neo->id)->first();

        $neoRequest = (is_null($neoConnection)) ? NeoConnection::IsExistingNeoConnection($neo->id, true)->first() : null;

        /** @var int|null $rioIdToCompare int used for view log condition */
        $rioIdToCompare = (!is_null($neoBelong)) ? $neoBelong->rio_id : null;

        if (!$isAccessInProfile && !empty($neo) && ($user->id != $rioIdToCompare)) {
            $this->createNEOviewlog(
                LogTypes::VIEWED,
                $neo->id,
                null,
                json_encode([
                    'accessed_by' => $serviceSelected->type,
                    'id' => $serviceSelected->data->id
                ])
            );
        }

        return view('neo.profile.introduction', compact(
            'user',
            'neo',
            'neoBelong',
            'isOwnerOrAdmin',
            'serviceSelected',
            'privacySetting',
            'neoConnection',
            'neoRequest',
            'isParticipant',
        ));
    }

    /**
     * RIO Profile page - Information tab
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function information(Neo $neo)
    {
        /** @var bool $isAccessInProfile bool to determine if the access to the page is from the profile page */
        $isAccessInProfile = (!is_null(request()->input('is_access_in_profile'))) ? true : false ;

        /** @var User */
        $user = auth()->user();

        $neoBelong = NeoBelong::where('rio_id', $user->rio->id)
            ->where('neo_id', $neo->id)
            ->first();

        /** @var NeoProfile */
        $neoProfile = $neo->neo_profile;

        // Initialize NEO expert resources
        $industries = $neo->industries;
        $awards = $neo->awards;
        $skills = $neo->skills;
        $qualifications = $neo->qualifications;
        $urls = $neo->urls;
        $histories = $neo->histories;
        $overseasCorrespondence = $neoProfile->overseas_support;
        $emails = $neo->emails;

        // Initialize data list
        $prefectures = PrefectureTypes::asSelectArray();

        /** @var string */
        $overseas = __('Overseas');

        // Construct present prefecture text
        if ($neoProfile->prefecture == PrefectureTypes::OTHER) {
            $nationality = $neoProfile->nationality ?? null;
            $neoProfile['address_prefecture_formatted'] = $nationality
                ? "$overseas ($nationality)" : null;
        } else {
            $neoProfile['address_prefecture_formatted'] =
                $prefectures[$neoProfile->prefecture] ?? null;
        }

        // Check if already a participant
        /** @var bool */
        $isParticipant = NeoBelong::isExistingParticipant($neo->id);

        // NEO privacy setting
        $privacySetting = NeoPrivacy::whereNeoId($neo->id)->first();

        // Service selected
        $serviceSelected = json_decode(Session::get('ServiceSelected'));

        $neoConnection = NeoConnection::IsExistingNeoConnection($neo->id)->first();

        $neoRequest = (is_null($neoConnection)) ? NeoConnection::IsExistingNeoConnection($neo->id, true)->first() : null;

        /** @var int|null $rioIdToCompare int used for view log condition */
        $rioIdToCompare = (!is_null($neoBelong)) ? $neoBelong->rio_id : null;

        // Service selected
        $serviceSelected = json_decode(Session::get('ServiceSelected'));

        if (!$isAccessInProfile && !empty($neo) && ($user->id != $rioIdToCompare)) {
            $this->createNEOviewlog(
                LogTypes::VIEWED,
                $neo->id,
                null,
                json_encode([
                    'accessed_by' => $serviceSelected->type,
                    'id' => $serviceSelected->data->id
                ])
            );
        }

        return view('neo.profile.information', compact(
            'user',
            'neo',
            'neoProfile',
            'urls',
            'industries',
            'histories',
            'qualifications',
            'awards',
            'skills',
            'overseasCorrespondence',
            'emails',
            'neoBelong',
            'serviceSelected',
            'privacySetting',
            'neoConnection',
            'neoRequest',
            'isParticipant',
        ));
    }

    /**
     * NEO profile manage participants page
     *
     * @param Neo $neo
     * @return \Illuminate\View\View
     */
    public function participants(Neo $neo)
    {
        $this->authorize('manage', [Neo::class, $neo]);

        Session::put('neoProfileId', $neo->id);

        $participants = NeoBelong::applyingList()
            ->whereNeoId($neo->id)
            ->paginate(config('bphero.paginate_count'));

        return view('neo.profile.participants', compact('participants', 'neo'));
    }

    /**
     * NEO profile participants list page
     *
     * @param Neo $neo
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function participantsList(Neo $neo)
    {
        /** @var bool $isAccessInProfile bool to determine if the access to the page is from the profile page */
        $isAccessInProfile = (!is_null(request()->input('is_access_in_profile'))) ? true : false ;

        /** @var User */
        $user = auth()->user();

        /** @var Rio */
        $rio = $user->rio;

        // Check if already a participant
        $isParticipant = NeoBelong::isExistingParticipant($neo->id);

        // Get NEO owner
        $owner = $neo->rios()
            ->with('rio_profile')
            ->wherePivot('role', RoleType::OWNER)
            ->first();

        // Get all participants except the owner
        $participants = $neo->rios()
            ->with('rio_profile')
            ->wherePivot('role', '!=', RoleType::OWNER)
            ->wherePivot('status', NeoBelongStatuses::AFFILIATED)
            ->get();

        // Prepend the owner if exists
        if ($owner) {
            $participants = $participants->prepend($owner);
        }

        $neoBelong = NeoBelong::where('rio_id', $user->rio->id)
            ->where('neo_id', $neo->id)
            ->first();

        // NEO privacy setting
        $privacySetting = NeoPrivacy::whereNeoId($neo->id)->first();

        // Service selected
        $serviceSelected = json_decode(Session::get('ServiceSelected'));

        $neoConnection = NeoConnection::IsExistingNeoConnection($neo->id)->first();

        $neoRequest = (is_null($neoConnection)) ? NeoConnection::IsExistingNeoConnection($neo->id, true)->first() : null;

        /** @var int|null $rioIdToCompare int used for view log condition */
        $rioIdToCompare = (!is_null($neoBelong)) ? $neoBelong->rio_id : null;

        // Service selected
        $serviceSelected = json_decode(Session::get('ServiceSelected'));

        if (!$isAccessInProfile && !empty($neo) && ($user->id != $rioIdToCompare)) {
            $this->createNEOviewlog(
                LogTypes::VIEWED,
                $neo->id,
                null,
                json_encode([
                    'accessed_by' => $serviceSelected->type,
                    'id' => $serviceSelected->data->id
                ])
            );
        }

        return view('neo.profile.participants-list', compact(
            'user',
            'neo',
            'isParticipant',
            'participants',
            'neoBelong',
            'serviceSelected',
            'privacySetting',
            'neoConnection',
            'neoRequest',
        ));
    }

    /**
     * NEO profile groups list page
     *
     * @param Neo $neo
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function groupsList(Neo $neo)
    {
        /** @var User */
        $user = auth()->user();

        /** @var Rio */
        $rio = $user->rio;

        // Check if already a participant
        /** @var bool */
        $isParticipant = NeoBelong::isExistingParticipant($neo->id);

        // Return 404 if non-participant accesses group list
        if (!$isParticipant) {
            abort(404);
        }

        $neoBelong = NeoBelong::where('rio_id', $user->rio->id)
            ->where('neo_id', $neo->id)
            ->first();

        // NEO groups query
        $groups = NeoGroup::whereNeoId($neo->id);

        $neoBelong = NeoBelong::where('rio_id', $user->rio->id)
            ->where('neo_id', $neo->id)
            ->first();

        // NEO privacy setting
        $privacySetting = NeoPrivacy::whereNeoId($neo->id)->first();

        // Service selected
        $serviceSelected = json_decode(Session::get('ServiceSelected'));

        $neoConnection = NeoConnection::IsExistingNeoConnection($neo->id)->first();

        $neoRequest = (is_null($neoConnection)) ? NeoConnection::IsExistingNeoConnection($neo->id, true)->first() : null;

        // Get groups list & total
        $groups = $groups->get();
        $totalGroups = $groups->count();

        return view('neo.profile.groups-list', compact(
            'user',
            'neo',
            'isParticipant',
            'groups',
            'totalGroups',
            'neoBelong',
            'serviceSelected',
            'privacySetting',
            'neoConnection',
            'neoRequest',
        ));
    }
    /**
     * Create log for accessed NEO Page
     *
     * @param int $logType type of log in neo logs
     * @param int $neo_id neo profile id
     * @param int|null $rio_id rio profile id
     * @param string|false $logDetail log detail string
     * @return void|null
     */
    private function createNEOviewlog($logType, $neo_id, $rio_id = null, $logDetail = '')
    {
        $neoLog = new NeoLog();
        /** @var int $neo_id */
        $neoLog->neo_id = $neo_id;
        /** @var int|null $rio_id */
        $neoLog->rio_id = $rio_id;
        $neoLog->logged_at = Carbon::now();
        $neoLog->log_type = $logType;
        /** @var string $logDetail */
        $neoLog->log_detail = $logDetail;
        $neoLog->save();
    }

    /**
     * NEO profile invite rio connected page
     *
     * @param Neo $neo
     * @return \Illuminate\View\View
     */
    public function invitation(Neo $neo)
    {
        $this->authorize('manage', [Neo::class, $neo]);

        Session::put('neoProfileId', $neo->id);

        $connectedLists = NeoConnection::connectionWithNeoBelong($neo->id)
            ->where('neo_belongs.status', '!=', NeoBelongStatuses::PENDING)
            ->where('neo_belongs.status', '!=', NeoBelongStatuses::AFFILIATED)
            ->paginate(config('bphero.paginate_count'));

        return view('neo.profile.invitation', compact('connectedLists', 'neo'));
    }

    /**
     * NEO participation invitation
     *
     * @param Neo $neo
     * @return \Illuminate\View\View
     */
    public function participationInvitation(Neo $neo)
    {
        $this->authorize('manage', [Neo::class, $neo]);
        $neoId = $neo->id;

        $connectedLists = NeoConnection::possibleRioInviteesForNeoBelong($neo->id)
            ->paginate(config('bphero.paginate_count'));

        return view('neo.profile.inviting', compact('connectedLists', 'neo', 'neoId'));
    }
}
