<?php

namespace App\Notifications;

use App\Enums\MailTemplates;
use App\Mail\CommonMailable;
use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RegistrationVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var object
     */
    private $user;

    /**
     * @var int
     */
    private $template = MailTemplates::REGISTRATION_VERIFIED;

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
     * @param mixed $notifiable
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail($notifiable)
    {
        // Get mail template
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
        $name = $this->user->rio->family_name . ' ' . $this->user->rio->first_name;
        $email = $this->user->email;
        $url = route('login.get');

        return preg_replace_callback(
            '~\{\{(.*?)\}\}~',
            function ($key) use ($name, $url, $email) {
                $emailVariable['rio_name'] = $name;
                $emailVariable['login_url'] = $url;
                $emailVariable['email'] = $email;

                return $emailVariable[$key[1]];
            },
            htmlspecialchars_decode($template->content)
        ) ?: '';
    }
}
