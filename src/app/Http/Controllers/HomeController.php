<?php

namespace App\Http\Controllers;

use App\Enums\NeoBelongStatuses;
use App\Enums\Workflow\StatusTypes;
use App\Models\ElectronicContract;
use App\Models\User;
use App\Models\WorkFlow;
use App\Models\Notification;
use Session;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        /** @var User */
        $user = auth()->user();
        $currentUser = $user->rio;
        $serviceSession = json_decode(Session::get('ServiceSelected'));
        $document = [];
        $neosFiltered = [];
        $isRioAllowed = false;

        $neos = $user->rio->neos()
            ->whereStatus(NeoBelongStatuses::AFFILIATED)
            ->paginate(config('bphero.paginate_count'));

        $notifications = Notification::announcementNotifications()->get();
        $totalCount = $this->getWorkflowNotificationBadgeCount($currentUser);

        $availableSlot = ElectronicContract::availableSlot($serviceSession);

        return view('home', compact(
            'serviceSession',
            'neos',
            'currentUser',
            'document',
            'neosFiltered',
            'isRioAllowed',
            'notifications',
            'totalCount',
            'availableSlot'
        ));
    }

    /**
     * Gets Workflow Dashboard Notification count.
     *
     * @param object $currentUser
     * @return integer|null
     */
    public function getWorkflowNotificationBadgeCount($currentUser)
    {
        $workflowForYouCount = 0;

        // Query for you workflow that are not cancelled
        $workflows = WorkFlow::forYouLists()
            ->with(['actions'])
            ->where('status', '<', StatusTypes::REJECTED)
            ->get();

        // Count workflows with pending status for action of current rio id
        if (!empty($workflows)) {
            foreach ($workflows as $workflow) {
                if ($workflow->currentApprover()) {
                    if ($workflow->currentApprover()->rio_id == $currentUser->id) {
                        $workflowForYouCount++;
                    }
                }
            }
        }

        //Query created workflows that are returned
        $countReturned = WorkFlow::registeredList()
            ->returned()
            ->count();

        return ($workflowForYouCount + $countReturned);
    }
}
