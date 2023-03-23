<?php

namespace App\Notifications;

use App\Enums\MailTemplates;
use App\Mail\CommonMailable;
use App\Models\MailTemplate;

class ConnectionApplicationNotification extends EmailNotification
{
    /**
     * @var \App\Models\Rio|\App\Models\Neo
     */
    private $connectionSender;

    /**
     * @var \App\Models\Rio|\App\Models\Neo
     */
    private $connectionReceiver;

    /**
     * @var int
     */
    private $template = MailTemplates::CONNECTION_APPLICATION;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Rio|\App\Models\Neo $connectionSender
     * @param \App\Models\Rio|\App\Models\Neo $connectionReceiver
     */
    public function __construct($connectionSender, $connectionReceiver)
    {
        $this->connectionSender = $connectionSender;
        $this->connectionReceiver = $connectionReceiver;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail($user)
    {
        $template = MailTemplate::findByTemplateId($this->template);

        // Guard clause for non-existing template
        if (empty($template)) {
            /** @var string */
            $message = __('Mail template specified could not be found.');
            abort(500, $message);
        }

        // Get sender name depending on sending service (RIO or NEO)
        if ($this->connectionSender instanceof \App\Models\Rio) {
            $senderName = $this->connectionSender->full_name . 'さん';
        } elseif ($this->connectionSender instanceof \App\Models\Neo) {
            $senderName = $this->connectionSender->organization_name;
        }

        // Get receiver name depending on receiving service (RIO or NEO)
        if ($this->connectionReceiver instanceof \App\Models\Rio) {
            $receiverName = $this->connectionReceiver->full_name . 'さん';
        } elseif ($this->connectionReceiver instanceof \App\Models\Neo) {
            $receiverName = $this->connectionReceiver->organization_name;
        }

        // Generate email subject & content
        $subjectData = [
            'sender_name' => $senderName ?? '-',
        ];
        $contentData = [
            'sender_name' => $senderName ?? '-',
            'receiver_name' => $receiverName ?? '-',
            'bphero_url' => config('app.url'),
        ];

        // Generate email body using content
        $subject = $this->parseMailSubjectWithVariables($template->name, $subjectData);
        $body = $this->parseMailBodyWithContent($template->content, $contentData);

        // Initialize mail message
        $mail = new CommonMailable('mails.email_template', $body);
        $mail->subject($subject);
        $mail->to($user->email);

        return $mail;
    }
}
