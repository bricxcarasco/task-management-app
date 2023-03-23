<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class SimplePushFcmNotification extends Notification
{
    /**
     * Notification title
     *
     * @var string
     */
    private $title;

    /**
     * Notification message
     *
     * @var string
     */
    private $message;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $message
     */
    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    /**
     * Get the fcm representation of the notification.
     *
     * @param object $notifiable
     *
     * @return \NotificationChannels\Fcm\FcmMessage
     */
    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setNotification(FcmNotification::create()
                ->setTitle($this->title)
                ->setBody($this->message));
    }
}
