<?php

namespace App\Http\Controllers\Schedule;

use App\Enums\Neo\RoleType;
use App\Enums\NeoBelongStatuses;
use App\Enums\Schedule\GuestStatuses;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\ScheduleExportRequest;
use App\Http\Requests\Schedule\ScheduleIssuerRequest;
use App\Http\Requests\Schedule\UpsertScheduleRequest;
use App\Http\Resources\Schedule\SchedulesResource;
use App\Models\Neo;
use App\Models\Notification;
use App\Models\Rio;
use App\Models\RioConnection;
use App\Models\Schedule;
use App\Objects\Schedules;
use Carbon\Carbon;
use DB;
use Session;
use App\Models\ScheduleGuest;
use App\Models\ScheduleNotification;
use App\Models\User;
use App\Services\CsvService;
use Illuminate\Support\Collection;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|mixed
     */
    public function index()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));

        // Invitation notification count
        $invitationsCount = ScheduleNotification::notifications($service)->count();

        return view('schedules.index', compact(
            'rio',
            'service',
            'invitationsCount',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|mixed
     */
    public function create()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));

        // Get selections
        $timeSelections = Schedules::getTimeSelections();
        $serviceSelections = Schedules::getServiceSelections($rio);
        $searchData = [];

        //Create object to get only RIO in existing function
        $array =  ["type" => ServiceSelectionTypes::RIO, "data" => $rio];
        $rioService = (object) $array;
        $connectedList = RioConnection::connectedList($rioService, $searchData)
            ->get();

        $memberList = [];
        $mergedList = [];
        if ($service->type === 'NEO') {
            //Get all neo connections
            $connectedList = RioConnection::connectedList($service, $searchData)->get();

            //Get all neo participants/members except the owner
            $neo = Neo::where('id', $service->data->id)->first();
            $memberList = $this->getNeoMemberList($neo);
        }

        $mergedList = $connectedList->toBase()->merge($memberList)->unique(function ($item) {
            return $item['id'].$item['name'];
        })->flatten();

        $owner = Neo::currentUser($service->type, $service->data->id)
            ->first();

        return view('schedules.create', compact(
            'rio',
            'service',
            'timeSelections',
            'serviceSelections',
            'connectedList',
            'memberList',
            'mergedList',
            'owner'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Schedule\UpsertScheduleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UpsertScheduleRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get request data
        $requestData = $request->validated();

        // Initialize data
        $guests = [];
        $notifications = [];
        $scheduleInvitations = [];
        $emailReceivers = [];

        if ($requestData['all_day']) {
            $requestData['start_time'] = null;
            $requestData['end_time'] = null;
        }

        DB::beginTransaction();

        try {
            $now = date('Y-m-d H:i:s');

            // Set created rio & save schedule
            $requestData['created_rio_id'] = $user->rio_id;

            /** @phpstan-ignore-next-line */
            $schedule = Schedule::create($requestData);

            // Get owner information & name
            $owner = null;
            $ownerName = null;
            if (!empty($requestData['owner_rio_id'])) {
                /** @var Rio */
                $owner = Rio::whereId($requestData['owner_rio_id'])->first();
                $ownerName = $owner->full_name . 'さん';
            } elseif (!empty($requestData['owner_neo_id'])) {
                /** @var Neo */
                $owner = Neo::whereId($requestData['owner_neo_id'])->first();
                $ownerName = $owner->organization_name;
            }

            // Guard clause if non-existing owner
            if (empty($owner)) {
                return response()->respondNotFound();
            }

            $guests[] = [
                'schedule_id' => $schedule->id,
                'rio_id' => $requestData['owner_rio_id'],
                'neo_id' => $requestData['owner_neo_id'],
                'status' => GuestStatuses::PARTICIPATE,
            ];

            foreach ($request['guests'] as $guest) {
                // Initialize data
                $receiver = null;

                // If notification receiver is RIO
                if ($guest['type'] === ServiceSelectionTypes::RIO) {
                    $receiver = Rio::whereId($guest['id'])->firstOrFail();

                    $notifications[] = [
                        'destination_url' => route('schedules.show', $schedule->id),
                        'notification_content' => __('Notification Content - Schedule Invitation', [
                            'sender_name' => $ownerName
                        ]),
                        'rio_id' => $guest['id'],
                        'receive_neo_id' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // If notification receiver is NEO
                if ($guest['type'] === ServiceSelectionTypes::NEO) {
                    /** @var Neo */
                    $receiver = Neo::whereId($guest['id'])->firstOrFail();

                    /** @var \App\Models\NeoBelong */
                    $neoOwner = $receiver->owner;

                    if (!empty($neoOwner)) {
                        $notifications[] = [
                            'destination_url' => route('schedules.show', $schedule->id),
                            'notification_content' => __('Notification Content - Schedule Invitation', [
                                'sender_name' => $ownerName
                            ]),
                            'rio_id' => $neoOwner->rio_id,
                            'receive_neo_id' => $guest['id'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if (!empty($receiver)) {
                    // Append to email receivers
                    $emailReceivers[] = $receiver;
                }

                // Append data to guests & invitations array
                $guestInvitationInformation = [
                    'schedule_id' => $schedule->id,
                    'rio_id' => $guest['type'] === ServiceSelectionTypes::RIO ? $guest['id'] : null,
                    'neo_id' => $guest['type'] === ServiceSelectionTypes::NEO ? $guest['id'] : null,
                    'status' => GuestStatuses::WAITING_FOR_RESPONSE,
                ];
                $guests[] = $guestInvitationInformation;
                $scheduleInvitations[] = $guestInvitationInformation;
            }

            // Insert bulk schedule guests
            $schedule->guests()->createMany($guests);

            // Insert bulk schedule invitation notifications
            $schedule->notifications()->createMany($scheduleInvitations);

            // Insert bulk notifications
            Notification::insert($notifications);

            // Send email to invited guests
            $schedule->sendEmailToScheduleInvitee($owner, $emailReceivers);

            DB::commit();

            return response()->respondSuccess();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Schedule $schedule
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show(Schedule $schedule)
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        $service = json_decode(Session::get('ServiceSelected'));

        /** @var object */
        $schedules = $schedule
            ->with('owner_rio.rio_profile')
            ->with('guests.rio.rio_profile', 'guests.neo')
            ->whereId($schedule->id)
            ->first();

        switch ($service->type) {
            case $service->type === ServiceSelectionTypes::RIO:
                $isGuest = ScheduleGuest::whereScheduleId($schedule->id)
                    ->whereRioId($rio->id)
                    ->exists();

                $isOwner = Schedule::whereId($schedule->id)
                    ->whereOwnerRioId($rio->id)
                    ->exists();
                break;
            case $service->type === ServiceSelectionTypes::NEO:
                $isGuest = ScheduleGuest::whereScheduleId($schedule->id)
                    ->whereNeoId($service->data->id)
                    ->exists();

                $isOwner = Schedule::whereId($schedule->id)
                    ->whereOwnerNeoId($service->data->id)
                    ->exists();
                break;
            default:
                $isOwner = null;
                $isGuest = null;
        }

        if (!$isGuest && !$isOwner) {
            abort(404);
        }

        $schedule = $schedules
            ->append('equivalent_status')
            ->append('profile_photo');

        return view('schedules.show', compact('schedule', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Models\Schedule $schedule
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function update(UpsertScheduleRequest $request, Schedule $schedule)
    {
        $requestData = $request->validated();

        // Initialize data
        $notifications = [];
        $emailReceivers = [];

        // Get owner information & name
        $owner = null;
        $ownerName = null;
        if (!empty($requestData['owner_rio_id'])) {
            /** @var Rio */
            $owner = Rio::whereId($requestData['owner_rio_id'])->first();
            $ownerName = $owner->full_name . 'さん';
        } elseif (!empty($requestData['owner_neo_id'])) {
            /** @var Neo */
            $owner = Neo::whereId($requestData['owner_neo_id'])->first();
            $ownerName = $owner->organization_name;
        }

        // Guard clause if non-existing owner
        if (empty($owner)) {
            return response()->respondNotFound();
        }

        if (
            $schedule->owner_rio_id && $requestData['owner_rio_id'] === $schedule->owner_rio_id ||
            $schedule->owner_neo_id && $requestData['owner_neo_id'] === $schedule->owner_neo_id
        ) {
            $now = date('Y-m-d H:i:s');
            $results = collect();
            $guestLists = collect();
            $scheduleInvitations = collect();
            $rioId = collect();
            $neoId = collect();

            if ($requestData['all_day']) {
                $requestData['start_time'] = null;
                $requestData['end_time'] = null;
            }

            // Update schedule table
            $schedule->update($requestData);

            $lists = $schedule->guests()->get();

            // Schedule guests data collection
            foreach ($lists as $list) {
                $results->push([
                    'schedule_id' => $schedule->id,
                    'rio_id' => $list->rio_id,
                    'neo_id' => $list->neo_id
                ]);
            }

            // Selected guests data collection
            foreach ($request['guests'] as $guest) {
                if (($guest['type'] === 'RIO' && !$results->contains('rio_id', $guest['id'])) || ($guest['type'] === 'NEO' && !$results->contains('neo_id', $guest['id']))) {
                    // Initialize data
                    $receiver = null;

                    // If notification receiver is RIO
                    if ($guest['type'] === ServiceSelectionTypes::RIO) {
                        $receiver = Rio::whereId($guest['id'])->firstOrFail();
                        $notifications[] = [
                            'destination_url' => route('schedules.show', $schedule->id),
                            'notification_content' => __('Notification Content - Schedule Invitation', [
                                'sender_name' => $ownerName
                            ]),
                            'rio_id' => $guest['id'],
                            'receive_neo_id' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    // If notification receiver is NEO
                    if ($guest['type'] === ServiceSelectionTypes::NEO) {
                        /** @var Neo */
                        $receiver = Neo::whereId($guest['id'])->firstOrFail();

                        /** @var \App\Models\NeoBelong */
                        $neoOwner = $receiver->owner;

                        if (!empty($neoOwner)) {
                            $notifications[] = [
                                'destination_url' => route('schedules.show', $schedule->id),
                                'notification_content' => __('Notification Content - Schedule Invitation', [
                                    'sender_name' => $ownerName
                                ]),
                                'rio_id' => $neoOwner->rio_id,
                                'receive_neo_id' => $guest['id'],
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }

                    if (!empty($receiver)) {
                        // Append to email receivers
                        $emailReceivers[] = $receiver;
                    }

                    $guestInvitationInformation = [
                        'schedule_id' => $schedule->id,
                        'rio_id' => $guest['type'] === ServiceSelectionTypes::RIO ? $guest['id'] : null,
                        'neo_id' => $guest['type'] === ServiceSelectionTypes::NEO ? $guest['id'] : null,
                        'status' => GuestStatuses::WAITING_FOR_RESPONSE,
                    ];
                    $guestLists->push($guestInvitationInformation);
                    $scheduleInvitations->push($guestInvitationInformation);
                }
            }

            // Get all non-existing data base on selected guest
            foreach ($request['guests'] as $guest) {
                $rioId->push([
                    'rio_id' => $guest['type'] === ServiceSelectionTypes::RIO ? $guest['id'] : '',
                ]);

                $neoId->push([
                    'neo_id' => $guest['type'] === ServiceSelectionTypes::NEO ? $guest['id'] : '',
                ]);

                $notExistingGuests = $schedule->guests()
                    ->where(function ($q) use ($rioId, $neoId) {
                        $q->whereNotIn('rio_id', $rioId);
                        $q->OrwhereNotIn('neo_id', $neoId);
                    });

                $notExistingNotifications = $schedule->notifications()
                    ->where(function ($q) use ($rioId, $neoId) {
                        $q->whereNotIn('rio_id', $rioId);
                        $q->OrwhereNotIn('neo_id', $neoId);
                    });
            }

            DB::beginTransaction();

            try {
                // Delete removed guests
                if (!empty($notExistingGuests)) {
                    $notExistingGuests->delete();
                }
                // Delete notifications of removed guests
                if (!empty($notExistingNotifications)) {
                    $notExistingNotifications->delete();
                }

                // Insert bulk schedule guests
                $schedule->guests()->createMany($guestLists->toArray());

                // Insert bulk schedule invitation notifications
                $schedule->notifications()->createMany($scheduleInvitations);

                // Insert bulk notifications
                Notification::insert($notifications);

                // Send email to invited guests
                $schedule->sendEmailToScheduleInvitee($owner, $emailReceivers);

                DB::commit();

                return response()->respondSuccess();
            } catch (\Exception $e) {
                report($e);
                DB::rollBack();

                return response()->respondInternalServerError();
            }
        } else {
            // Create collection
            $newIssuerData = collect();
            $scheduleInvitations = collect();

            DB::beginTransaction();

            if ($requestData['all_day']) {
                $requestData['start_time'] = null;
                $requestData['end_time'] = null;
            }

            try {
                $now = date('Y-m-d H:i:s');

                // Delete guests if issuer changed
                $schedule->guests()->delete();

                // Update schedule
                $schedule->update($requestData);

                $newIssuerData->push([
                    'schedule_id' => $schedule->id,
                    'rio_id' => $requestData['owner_rio_id'],
                    'neo_id' => $requestData['owner_neo_id'],
                    'status' => GuestStatuses::PARTICIPATE,
                ]);

                foreach ($request['guests'] as $guest) {
                    // Initialize data
                    $receiver = null;

                    // If notification receiver is RIO
                    if ($guest['type'] === ServiceSelectionTypes::RIO) {
                        $receiver = Rio::whereId($guest['id'])->firstOrFail();
                        $notifications[] = [
                            'destination_url' => route('schedules.show', $schedule->id),
                            'notification_content' => __('Notification Content - Schedule Invitation', [
                                'sender_name' => $ownerName
                            ]),
                            'rio_id' => $guest['id'],
                            'receive_neo_id' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    // If notification receiver is NEO
                    if ($guest['type'] === ServiceSelectionTypes::NEO) {
                        /** @var Neo */
                        $receiver = Neo::whereId($guest['id'])->firstOrFail();

                        /** @var \App\Models\NeoBelong */
                        $neoOwner = $receiver->owner;

                        if (!empty($neoOwner)) {
                            $notifications[] = [
                                'destination_url' => route('schedules.show', $schedule->id),
                                'notification_content' => __('Notification Content - Schedule Invitation', [
                                    'sender_name' => $ownerName
                                ]),
                                'rio_id' => $neoOwner->rio_id,
                                'receive_neo_id' => $guest['id'],
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }

                    if (!empty($receiver)) {
                        // Append to email receivers
                        $emailReceivers[] = $receiver;
                    }

                    $guestInvitationInformation = [
                        'schedule_id' => $schedule->id,
                        'rio_id' => $guest['type'] === ServiceSelectionTypes::RIO ? $guest['id'] : null,
                        'neo_id' => $guest['type'] === ServiceSelectionTypes::NEO ? $guest['id'] : null,
                        'status' => GuestStatuses::WAITING_FOR_RESPONSE,
                    ];
                    $newIssuerData->push($guestInvitationInformation);
                    $scheduleInvitations->push($guestInvitationInformation);
                }

                // Insert bulk schedule guests
                $schedule->guests()->createMany($newIssuerData->toArray());

                // Insert bulk schedule invitation notifications
                $schedule->notifications()->createMany($scheduleInvitations);

                // Insert bulk notifications
                Notification::insert($notifications);

                // Send email to invited guests
                $schedule->sendEmailToScheduleInvitee($owner, $emailReceivers);

                DB::commit();

                return response()->respondSuccess();
            } catch (\Exception $e) {
                report($e);
                DB::rollBack();

                return response()->respondInternalServerError();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Schedule $schedule
     * @return \Illuminate\Http\Response|mixed
     */
    public function destroy(Schedule $schedule)
    {
        DB::beginTransaction();

        try {
            $schedule->delete();

            DB::commit();

            return response()->respondSuccess();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Fetch schedules list based on year and month.
     *
     * @param string $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSchedulesByMonth($date)
    {
        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));

        // Get schedule list
        $schedules = Schedule::getSchedulesPerMonth($service, $date)->get();

        $modifiedSchedules = [];
        foreach ($schedules as $schedule) {
            if ($schedule->start_date !== $schedule->end_date) {
                $startDate = Carbon::parse($schedule->start_date);
                $endDate = Carbon::parse($schedule->end_date);

                while ($startDate <= $endDate) {
                    $date = $startDate->format('Y-m-d');

                    // Add reconstructed schedule
                    $modifiedSchedules[] = [
                        'id' => $schedule->id,
                        'guest_id' => $schedule['schedule_guest_id'],
                        'title' => $schedule->schedule_title,
                        'start_date' => $date,
                        'end_date' => $date,
                    ];

                    // Increment start date
                    $startDate = $startDate->addDay();
                }
            } else {
                $modifiedSchedules[] = [
                    'id' => $schedule->id,
                    'guest_id' => $schedule['schedule_guest_id'],
                    'title' => $schedule->schedule_title,
                    'start_date' => $schedule->start_date,
                    'end_date' => $schedule->end_date,
                ];
            }
        }

        return response()->respondSuccess($modifiedSchedules);
    }

    /**
     * Fetch schedules list based on selected day.
     *
     * @param string $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSchedulesByDay($date)
    {
        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));

        // Get schedule list
        $schedules = Schedule::getSchedulesPerDay($service, $date)->get();

        // JSON resource
        $schedules = SchedulesResource::collection($schedules);

        return response()->respondSuccess($schedules);
    }

    /**
     * Accept schedule invite
     *
     * @param mixed $id
     * @return mixed
     */
    public function acceptParticipation($id)
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        $service = json_decode(Session::get('ServiceSelected'));
        $serviceId = $service->data->id;

        if ($service->type === ServiceSelectionTypes::NEO) {
            $scheduleGuest = ScheduleGuest::whereScheduleId($id)
                ->whereNeoId($serviceId);

            $scheduleNotification = ScheduleNotification::whereScheduleId($id)
                ->whereNeoId($serviceId);
        } else {
            $scheduleGuest = ScheduleGuest::whereScheduleId($id)
                ->whereRioId($rio->id);

            $scheduleNotification = ScheduleNotification::whereScheduleId($id)
                ->whereRioId($rio->id);
        }

        try {
            // Update guest status to "Participating"
            $scheduleGuest->update([
                'status' => GuestStatuses::PARTICIPATE
            ]);

            // Delete schedule notification
            $scheduleNotification->delete();

            return redirect()->back()
                ->withAlertBox('success', __('I would participate'));
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            return redirect()->back()
                ->withAlertBox('danger', __('Server Error'));
        }
    }

    /**
     * Accept schedule invite
     *
     * @param  int  $id
     * @return mixed
     */
    public function declineParticipation($id)
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        $service = json_decode(Session::get('ServiceSelected'));
        $serviceId = $service->data->id;

        if ($service->type === ServiceSelectionTypes::NEO) {
            $scheduleGuest = ScheduleGuest::whereScheduleId($id)
                ->whereNeoId($serviceId);

            $scheduleNotification = ScheduleNotification::whereScheduleId($id)
                ->whereNeoId($serviceId);
        } else {
            $scheduleGuest = ScheduleGuest::whereScheduleId($id)
                ->whereRioId($rio->id);

            $scheduleNotification = ScheduleNotification::whereScheduleId($id)
                ->whereRioId($rio->id);
        }

        try {
            // Update guest status to "Not Participating"
            $scheduleGuest->update([
                'status' => GuestStatuses::NOT_PARTICIPATE
            ]);

            // Delete schedule notification
            $scheduleNotification->delete();

            return redirect()->back()
                ->withAlertBox('success', __('I would not participate'));
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            return redirect()->back()
                ->withAlertBox('danger', __('Server Error'));
        }
    }

    /**
     * Update connection list based on issuer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateConnectionList(ScheduleIssuerRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));

        // Get request data
        $requestData = $request->validated();

        $array =  ["type" => ServiceSelectionTypes::RIO, "data" => $user->rio];
        $rioService = (object) $array;
        $searchData = [];
        $mergedList = [];
        $memberList = [];

        if ($requestData['owner_rio_id']) {
            $issuer = Neo::currentUser(ServiceSelectionTypes::RIO, $requestData['owner_rio_id'])
                ->first();
            $guestList = RioConnection::connectedList($rioService, $searchData)
                ->get();
        } else {
            $neo = Neo::where('id', $requestData['owner_neo_id'])->first();
            $array =  ["type" => ServiceSelectionTypes::NEO, "data" => $neo];
            $neoService = (object) $array;

            $issuer = Neo::currentUser(ServiceSelectionTypes::NEO, $requestData['owner_neo_id'])
                ->first();
            $guestList = RioConnection::connectedList($neoService, $searchData)
                ->get();

            $memberList = $this->getNeoMemberList($neo);
        }

        $mergedList = $guestList->toBase()->merge($memberList)->unique(function ($item) {
            return $item['id'].$item['name'];
        })->flatten();

        return response()->respondSuccess([
            'guests' => $guestList,
            'members' => $memberList,
            'mergedList' => $mergedList,
            'issuer' => $issuer
        ]);
    }

    /**
     * Update connection list based on issuer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateExistingScheduleList(ScheduleIssuerRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));
        $searchData = [];
        $mergedList = [];
        $memberList = [];

        /** @var object */
        $schedule = Schedule::whereId($request['id'])->first();

        //Check if Owner
        switch ($service->type) {
            case $service->type === ServiceSelectionTypes::RIO && $schedule->owner_rio_id === $requestData['owner_rio_id']:
                $isCurrentOwner = true;
                $getResults = ScheduleGuest::selectedGuests($request['id'])->get();
                $selected = $getResults->makeHidden(['equivalent_status']);
                break;
            case $service->type === ServiceSelectionTypes::NEO && $schedule->owner_neo_id === $requestData['owner_neo_id']:
                $isCurrentOwner = true;
                $getResults = ScheduleGuest::selectedGuests($request['id'])->get();
                $selected = $getResults->makeHidden(['equivalent_status']);
                break;
            default:
                $isCurrentOwner = false;
                $selected = null;
        }

        //Update guest list and issuer
        if ($requestData['owner_rio_id']) {
            $rio = Rio::findOrFail($requestData['owner_rio_id']);
            $array =  ["type" => ServiceSelectionTypes::RIO, "data" => $rio];
            $rioService = (object) $array;

            $issuer = Neo::currentUser(ServiceSelectionTypes::RIO, $requestData['owner_rio_id'])
                ->first();
            $guestList = RioConnection::connectedList($rioService, $searchData, true)
                ->get();
        } else {
            $neo = Neo::where('id', $requestData['owner_neo_id'])->first();
            $array =  ["type" => ServiceSelectionTypes::NEO, "data" => $neo];
            $neoService = (object) $array;

            $issuer = Neo::currentUser(ServiceSelectionTypes::NEO, $requestData['owner_neo_id'])
                ->first();
            $guestList = RioConnection::connectedList($neoService, $searchData, true)
                ->get();
            $memberList = $this->getNeoMemberList($neo);
        }

        $mergedList = $guestList->toBase()->merge($memberList)->unique(function ($item) {
            return $item['id'].$item['name'];
        })->flatten();

        return response()->respondSuccess([
            'guests' => $guestList,
            'issuer' => $issuer,
            'members' => $memberList,
            'mergedList' => $mergedList,
            'isCurrentOwner' => $isCurrentOwner,
            'selected' => $selected
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Models\Schedule $schedule
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit(Schedule $schedule)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));

        //Check if Owner
        switch ($service->type) {
            case $service->type === ServiceSelectionTypes::RIO:
                $isOwner = Schedule::whereId($schedule->id)
                    ->whereOwnerRioId($rio->id)
                    ->exists();
                break;
            case $service->type === ServiceSelectionTypes::NEO && !$service->data->is_member:
                $isOwner = Schedule::whereId($schedule->id)
                    ->whereOwnerNeoId($service->data->id)
                    ->exists();
                break;
            default:
                $isOwner = null;
        }

        if (!$isOwner) {
            abort(404);
        }

        // Get selections
        $timeSelections = Schedules::getTimeSelections();
        $serviceSelections = Schedules::getServiceSelections($rio);
        $searchData = [];

        if ($schedule->owner_rio_id) {
            $ownerType = ServiceSelectionTypes::RIO;
            $ownerId = $schedule->owner_rio_id;
        } else {
            $ownerType = ServiceSelectionTypes::NEO;
            $ownerId = $schedule->owner_neo_id;
        }

        $connectedList = RioConnection::connectedList($service, $searchData, true)->get();
        $memberList = [];
        if ($service->type === 'NEO') {
            //Get all neo connections
            $connectedList = RioConnection::connectedList($service, $searchData)->get();

            //Get all neo participants/members except the owner
            // $neo = Neo::find($service->data->id);
            $neo = Neo::where('id', $service->data->id)->first();
            $memberList = $this->getNeoMemberList($neo);
        }


        $mergedList = $connectedList->toBase()->merge($memberList)->unique(function ($item) {
            return $item['id'].$item['name'];
        })->flatten();

        $owner = Neo::currentUser($ownerType, $ownerId)->first();

        $results = ScheduleGuest::selectedGuests($schedule->id)
            ->addSelect([
                'schedule_guests.status',
                'schedule_guests.rio_id',
                'schedule_guests.neo_id',
                'schedule_guests.schedule_id'
            ])
            ->get();

        $startTime = '';
        $endTime = '';

        if ($schedule->start_time && $schedule->end_time) {
            // Format time to minutes and hours
            $startTime = Carbon::parse($schedule->start_time)->format('H:i');
            $endTime = Carbon::parse($schedule->end_time)->format('H:i');
        }

        $schedule->start_time = $startTime;
        $schedule->end_time = $endTime;
        $schedule->all_day =  $schedule->start_time ? false : true;

        $currentGuests = $results->append('equivalent_status');
        $getResults = ScheduleGuest::selectedGuests($schedule->id)->get();
        $selected = $getResults->makeHidden(['equivalent_status']);

        return view('schedules.edit', compact(
            'rio',
            'service',
            'timeSelections',
            'serviceSelections',
            'connectedList',
            'memberList',
            'mergedList',
            'owner',
            'schedule',
            'currentGuests',
            'selected'
        ));
    }

    /**
     * Schedule export page
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function export()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        $startDate = Carbon::now()->startOfMonth()->toDateString();
        $endDate = Carbon::now()->endOfMonth()->toDateString();
        $serviceSelections = Schedules::getServiceSelections($rio);

        return view('schedules.export', compact(
            'rio',
            'startDate',
            'endDate',
            'serviceSelections'
        ));
    }

    /**
     * Export schedule data list function
     *
     * @param \App\Http\Requests\Schedule\ScheduleExportRequest $request
     * @param \App\Services\CsvService $csvService
     * @return mixed
     */
    public function exportSchedule(ScheduleExportRequest $request, CsvService $csvService)
    {
        // Get validated data
        $requestData = $request->validated();
        [$type, $id] = explode('_', $request->input('issuer'), 2);
        $from = $requestData['start_date'];
        $to = $requestData['end_date'];

        // Initialize query
        $schedules = Schedule::where(function ($query) use ($from, $to) {
            $query
                ->where('start_date', '>=', $from)
                ->where('end_date', '<=', $to);
        });

        if (strtoupper($type) === ServiceSelectionTypes::RIO) {
            $schedules = $schedules->whereHas('guests', function ($q) use ($id) {
                $q->where('rio_id', $id);
            })->get();

            /** @var object */
            $user = Neo::currentUser(ServiceSelectionTypes::RIO, $id)->first();
            $fileName = $user->name . '_schedules.csv';
        } else {
            $schedules = $schedules->whereHas('guests', function ($q) use ($id) {
                $q->where('neo_id', $id);
            })->get();

            /** @var object */
            $user = Neo::currentUser(ServiceSelectionTypes::NEO, $id)->first();
            $fileName = $user->name . '_schedule.csv';
        }

        // Define and set rows and columns
        $rows = [];
        $columns = [
            'Subject',
            'Start date',
            'Start time',
            'End Date',
            'End Time',
            'All Day Event',
            'Description',
        ];

        // Create array base on item
        foreach ($schedules as $list) {
            $startDate = Carbon::parse($list->start_date)->format('Y/m/d');
            $endDate = Carbon::parse($list->end_date)->format('Y/m/d');
            $startTime = !is_null($list->start_time)
                ? Carbon::parse($list->start_time)->format('h:i A')
                : null;
            $endTime = !is_null($list->end_time)
                ? Carbon::parse($list->end_time)->format('h:i A')
                : null;
            $isAllDayEvent = is_null($list->start_time) && is_null($list->end_time)
                ? 'TRUE'
                : 'FALSE';

            $rows[] = [
                'Subject' => $list->schedule_title ?? '-',
                'Start date' => $startDate,
                'Start time' => $startTime,
                'End Date' => $endDate,
                'End Time' => $endTime,
                'All Day Event' => $isAllDayEvent,
                'Description' => $list->caption,
            ];
        }

        try {
            // Export CSV file
            return $csvService->export($columns, $rows, $fileName);
        } catch (\Exception $e) {
            report($e);

            return redirect()
                ->route('schedule.export')
                ->withAlertBox('danger', __('Failed to download CSV'));
        }
    }

    /**
     * Display the Schedule notifications.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function notifications()
    {
        /** @var User */
        $user = auth()->user();

        // Get rio and service selected
        $rio = $user->rio;
        $service = json_decode(Session::get('ServiceSelected'));

        // Get notification list
        $notifications = ScheduleNotification::notifications($service)->get();

        return view('schedules.notifications', compact(
            'notifications',
            'service',
            'rio',
        ));
    }

    /**
     * Get Neo members
     *
     * @param mixed | null $neo
     * @return mixed | null
     */
    public function getNeoMemberList($neo)
    {
        $service = json_decode(Session::get('ServiceSelected'));
        $defaultProfileImage = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');
        $rioProfileImagePath = config('app.url') . "/" . "storage/" . config('bphero.rio_profile_image');

        if ($neo !== null) {
            return $neo->rios()
                ->select(
                    'rios.id',
                    DB::raw("CONCAT(rios.family_name,' ',rios.first_name) as name"),
                    DB::raw("CONCAT(rios.family_kana,' ',rios.first_kana) as kana"),
                    DB::raw(
                        "CASE 
                        WHEN rio_profiles.profile_photo IS NULL
                        THEN '" . $defaultProfileImage . "'
                        ELSE CONCAT('" . $rioProfileImagePath . "', rios.id, '/', rio_profiles.profile_photo)
                        END AS profile_picture"
                    ),
                    DB::raw("'RIO' as service"),
                )
                ->join('rio_profiles', 'rios.id', '=', 'rio_profiles.rio_id')
                ->wherePivot('role', '!=', RoleType::OWNER)
                ->wherePivot('status', NeoBelongStatuses::AFFILIATED)
                ->get();
        }
    }
}
