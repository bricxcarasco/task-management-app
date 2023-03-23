<?php

namespace App\Notifications;

use App\Enums\MailTemplates;
use App\Mail\CommonMailable;
use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SignupEmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var object
     */
    private $user;

    /**
     * @var int
     */
    private $template = MailTemplates::SIGNUP_EMAIL_VERIFICATION;

    /**
     * Create a new notification instance.
     *
     * @param mixed $user
     */
    public function __construct($user)
    {
        $this->user = $user;
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail($notifiable)
    {
        // Prepare mail contents
        $template = MailTemplate::findByTemplateId($this->template);

        // Guard clause for non-existing template
        if (empty($template)) {
            /** @var string */
            $message = __('Mail template specified could not be found.');
            abort(500, $message);
        }

        // Prepare mail contents
        $mailBody = $this->getMailBody($template);

        // Configure and send mail
        $mail = new CommonMailable('mails.email_template', $mailBody);
        $mail->subject($template->name);
        $mail->to($this->user->email);

        return $mail;
    }

    /**
     * Get mail body
     *
     * @param \App\Models\MailTemplate $template
     * @return string
     */
    protected function getMailBody($template)
    {
        $email = $this->user->email;
        $token = $this->user->token;
        $url = $this->getVerifyUrl($email, $token);

        return preg_replace_callback(
            '~\{\{(.*?)\}\}~',
            function ($key) use ($email, $url) {
                $emailVariable['email'] = $email;
                $emailVariable['verify_url'] = $url;

                return $emailVariable[$key[1]];
            },
            htmlspecialchars_decode($template->content)
        ) ?: '';
    }

    /**
     * create signed url  register/uuid?expires=xxx&signature=xxxx.
     *
     * @param string $email
     * @param string $token
     *
     * @return string
     */
    protected function getVerifyUrl($email, $token)
    {
        return route('registration.email.verify', [
            'email' => $email,
            'token' => $token,
        ]);
    }
}
