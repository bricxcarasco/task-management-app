<?php

namespace App\Notifications;

use App\Enums\Form\Types;
use App\Enums\MailTemplates;
use App\Mail\CommonMailable;
use App\Models\MailTemplate;
use Type;

class FormRecipientNotification extends EmailNotification
{
    /**
     * @var string
     */
    private $issuer;

    /**
     * @var \App\Models\Rio|\App\Models\Neo
     */
    private $recipientConnection;

    /**
     * @var \App\Models\Form
     */
    private $form;

    /**
     * @var \App\Enums\Form\Types
     */
    private $formType;

    /**
     * @var int
     */
    private $template = MailTemplates::FORM_RECIPIENT_CONNECTION;

    /**
     * Create a new notification instance.
     *
     * @param string $issuer
     * @param \App\Models\Rio|\App\Models\Neo $recipientConnection
     * @param \App\Models\Form $form
     */
    public function __construct($issuer, $recipientConnection, $form)
    {
        $this->issuer = $issuer;
        $this->recipientConnection = $recipientConnection;
        $this->form = $form;
        $this->formType = Types::fromValue($form->type ?? 0);
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
        $recipientName = null;

        $template = MailTemplate::findByTemplateId($this->template);

        // Guard clause for non-existing template
        if (empty($template)) {
            /** @var string */
            $message = __('Mail template specified could not be found.');
            abort(500, $message);
        }

        // Get recipient name depending on recipient service (RIO or NEO)
        if ($this->recipientConnection instanceof \App\Models\Rio) {
            $recipientName = $this->recipientConnection->full_name . 'さん';
        }

        if ($this->recipientConnection instanceof \App\Models\Neo) {
            $recipientName = $this->recipientConnection->organization_name;
        }

        //Translate form type into JP text
        $formType = ucfirst(strtolower(strval(str_replace("_", " ", $this->formType->key))));

        // Generate email subject & content
        $subjectData = [
            'issuer_name' => $this->issuer ?? '-',
            'form_type' => __($formType) ?? '-',
        ];
        $contentData = [
            'recipient_name' => $recipientName ?? '-',
            'issuer_name' => $this->issuer ?? '-',
            'form_type' => __($formType) ?? '-',
            'form_no' => $this->form->form_no ?? '-',
            'form_title' => $this->form->title ?? '-',
            'form_issue_date' => $this->form->issue_date ?? '-',
            'form_expiration_date' => $this->form->expiration_date ?? '-',
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
