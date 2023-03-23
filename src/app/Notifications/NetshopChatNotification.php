<?php

namespace App\Notifications;

use App\Enums\MailTemplates;
use App\Mail\CommonMailable;
use App\Models\MailTemplate;

class NetshopChatNotification extends EmailNotification
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
     * @var \App\Models\ClassifiedContactMessage
     */
    private $message;

    /**
     * @var int
     */
    private $template = MailTemplates::NETSHOP_CHAT_MESSAGE;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Neo|\App\Models\Rio $sender
     * @param \App\Models\Rio|\App\Models\Neo $receiver
     * @param \App\Models\ClassifiedContactMessage $message
     */
    public function __construct($sender, $receiver, $message)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->message = $message;
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
        $senderName = null;
        $receiverName = null;
        $template = MailTemplate::findByTemplateId($this->template);

        // Guard clause for non-existing template
        if (empty($template)) {
            /** @var string */
            $message = __('Mail template specified could not be found.');
            abort(500, $message);
        }

        // Get sender name depending on sender service (RIO or NEO)
        if ($this->sender instanceof \App\Models\Rio) {
            $senderName = $this->sender->full_name . 'さん';
        }

        if ($this->sender instanceof \App\Models\Neo) {
            $senderName = $this->sender->organization_name;
        }

        // Get recipient name depending on recipient service (RIO or NEO)
        if ($this->receiver instanceof \App\Models\Rio) {
            $receiverName = $this->receiver->full_name . 'さん';
        }

        if ($this->receiver instanceof \App\Models\Neo) {
            $receiverName = $this->receiver->organization_name;
        }

        // Generate email subject & content
        $subjectData = [
            'sender_name' => $senderName ?? '-'
        ];
        $contentData = [
            'receiver_name' => $receiverName ?? '-',
            'sender_name' => $senderName ?? '-',
            'message_id' => $this->message->classified_contact_id ?? '-',
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
