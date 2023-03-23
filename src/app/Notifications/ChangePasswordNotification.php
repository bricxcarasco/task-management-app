<?php

namespace App\Notifications;

use App\Enums\MailTemplates;
use App\Mail\CommonMailable;
use App\Models\MailTemplate;

class ChangePasswordNotification extends EmailNotification
{
    /**
     * @var \App\Models\User
     */
    private $user;

    /**
     * @var int
     */
    private $template = MailTemplates::CHANGE_PASSWORD;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\User $user
     */
    public function __construct($user)
    {
        $this->user = $user;
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

        // Generate email content
        $content = [
            'rio_name' => $user->rio->full_name ?? '-',
            'email' => $user->email ?? '-',
            'bphero_url' => config('app.url'),
        ];

        // Generate email body using content
        $body = $this->parseMailBodyWithContent($template->content, $content);

        // Initialize mail message
        $mail = new CommonMailable('mails.email_template', $body);
        $mail->subject($template->name);
        $mail->to($user->email);

        return $mail;
    }
}
