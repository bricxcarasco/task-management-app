<?php

namespace App\Http\Controllers\Notifications;

use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Carbon\Carbon;
use Session;

class NotificationController extends Controller
{
    /**
     * Notifications list page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Get all notifications & set active tab
        $notifications = Notification::allNotifications()->get();
        $activeTab = 'index';

        return view('notifications.index', compact(
            'notifications',
            'activeTab',
        ));
    }

    /**
     * Notifications list page for all unread notifications.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function unread()
    {
        // Get unread notifications & set active tab
        $notifications = Notification::unreadNotifications()->get();
        $activeTab = 'unread';

        return view('notifications.index', compact(
            'notifications',
            'activeTab',
        ));
    }

    /**
     * Read & redirect on notification click.
     *
     * @param \App\Models\Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Notification $notification)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Update notification to read status
        if (!$notification->is_notification_read) {
            $notification->update([
                'is_notification_read' => true,
                'read_at' => Carbon::now(),
            ]);
        }

        // Change selected service based on receiver upon redirection
        if (!empty($notification->destination_url)) {
            if (!empty($notification->neo)) {
                // If notification receiver is NEO, change service selected
                Session::put('ServiceSelected', json_encode([
                    'data' => $notification->neo,
                    'type' => ServiceSelectionTypes::NEO
                ]));
            } else {
                // Set service selection to logged in RIO
                Session::put('ServiceSelected', json_encode([
                    'data' => $user->rio,
                    'type' => ServiceSelectionTypes::RIO
                ]));
            }
        }

        return response()->respondSuccess([
            'redirect_url' => $notification->destination_url
        ]);
    }
}
