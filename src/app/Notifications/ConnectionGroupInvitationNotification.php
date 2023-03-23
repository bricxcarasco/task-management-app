<?php

namespace App\Notifications;

use App\Enums\MailTemplates;
use App\Mail\CommonMailable;
use App\Models\MailTemplate;

class ConnectionGroupInvitationNotification extends EmailNotification
{
    /**
     * @var \App\Models\GroupConnection
     */
    private $group;

    /**
     * @var \App\Models\Rio
     */
    private $rio;

    /**
     * @var int
     */
    private $template = MailTemplates::CONNECTION_GROUP_INVITATION;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\GroupConnection $group
     * @param \App\Models\Rio $rio
     */
    public function __construct($group, $rio)
    {
        $this->group = $group;
        $this->rio = $rio;
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

        // Generate email subject & content
        $subjectData = [
            'group_name' => $this->group->group_name ?? '-',
        ];
        $contentData = [
            'group_name' => $this->group->group_name ?? '-',
            'rio_name' => $this->rio->full_name ?? '-',
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
