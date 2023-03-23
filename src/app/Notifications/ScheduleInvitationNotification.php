<?php

namespace App\Notifications;

use App\Enums\MailTemplates;
use App\Mail\CommonMailable;
use App\Models\MailTemplate;

class ScheduleInvitationNotification extends EmailNotification
{
    /**
     * @var \App\Models\Rio|\App\Models\Neo
     */
    private $sender;

    /**
     * @var \App\Models\Rio|\App\Models\Neo
     */
    private $receiver;

    /**
     * @var int
     */
    private $template = MailTemplates::SCHEDULE_INVITATION;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Rio|\App\Models\Neo $sender
     * @param \App\Models\Rio|\App\Models\Neo $receiver
     */
    public function __construct($sender, $receiver)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
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

        // Get sender name depending on schedule owner
        if ($this->sender instanceof \App\Models\Rio) {
            $senderName = $this->sender->full_name . 'さん';
        } elseif ($this->sender instanceof \App\Models\Neo) {
            $senderName = $this->sender->organization_name;
        }

        // Get receiver name depending on invited guest
        if ($this->receiver instanceof \App\Models\Rio) {
            $receiverName = $this->receiver->full_name . 'さん';
        } elseif ($this->receiver instanceof \App\Models\Neo) {
            $receiverName = $this->receiver->organization_name;
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

        // Generate email subject & body using variables
        $subject = $this->parseMailSubjectWithVariables($template->name, $subjectData);
        $body = $this->parseMailBodyWithContent($template->content, $contentData);

        // Initialize mail message
        $mail = new CommonMailable('mails.email_template', $body);
        $mail->subject($subject);
        $mail->to($user->email);

        return $mail;
    }
}
