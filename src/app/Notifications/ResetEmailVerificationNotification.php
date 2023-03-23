<?php

namespace App\Notifications;

use App\Enums\MailTemplates;
use App\Mail\CommonMailable;
use App\Models\MailTemplate;
use Crypt;

class ResetEmailVerificationNotification extends EmailNotification
{
    /**
     * @var \App\Models\UserVerification
     */
    private $verification;

    /**
     * @var int
     */
    private $template = MailTemplates::EMAIL_RESET_VERIFICATION;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\UserVerification $verification
     */
    public function __construct($verification)
    {
        $this->verification = $verification;
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
            'change_email_url' => $this->getVerifyUrl($user)
        ];

        // Generate email body using content
        $mailBody = $this->parseMailBodyWithContent($template->content, $content);

        // Initialize mail message
        $mail = new CommonMailable('mails.email_template', $mailBody);
        $mail->subject($template->name);
        $mail->to($this->verification->email);

        return $mail;
    }

    /**
     * Generate route url for reset email verification
     *
     * @param \App\Models\User $user
     * @return string
     */
    protected function getVerifyUrl($user)
    {
        $encryptedId = Crypt::encrypt($user->id);

        return route('email.reset.verify', [
            'verification' => $this->verification->id,
            'user' => $encryptedId,
            'token' => $this->verification->token,
        ]);
    }
}
