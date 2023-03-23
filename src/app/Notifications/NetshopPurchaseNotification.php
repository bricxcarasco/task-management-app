<?php

namespace App\Notifications;

use App\Enums\MailTemplates;
use App\Mail\CommonMailable;
use App\Models\MailTemplate;

class NetshopPurchaseNotification extends EmailNotification
{
    /**
     * @var \App\Models\Rio|\App\Models\Neo
     */
    private $seller;

    /**
     * @var \App\Models\Rio|\App\Models\Neo
     */
    private $buyer;

    /**
     * @var \App\Models\ClassifiedSale
     */
    private $product;

    /**
     * @var int
     */
    private $template = MailTemplates::NETSHOP_PURCHASE;


    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Neo|\App\Models\Rio $seller
     * @param \App\Models\Rio|\App\Models\Neo $buyer
     * @param \App\Models\ClassifiedSale $product
     */
    public function __construct($seller, $buyer, $product)
    {
        $this->seller = $seller;
        $this->buyer = $buyer;
        $this->product = $product;
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
        $buyerName = null;
        $sellerName = null;
        $template = MailTemplate::findByTemplateId($this->template);

        // Guard clause for non-existing template
        if (empty($template)) {
            /** @var string */
            $message = __('Mail template specified could not be found.');
            abort(500, $message);
        }

        // Get seller name depending on buyer service (RIO or NEO)
        if ($this->seller instanceof \App\Models\Rio) {
            $sellerName = $this->seller->full_name . 'さん';
        }

        if ($this->seller instanceof \App\Models\Neo) {
            $sellerName = $this->seller->organization_name;
        }

        // Get buyer name depending on buyer service (RIO or NEO)
        if ($this->buyer instanceof \App\Models\Rio) {
            $buyerName = $this->buyer->full_name . 'さん';
        }

        if ($this->buyer instanceof \App\Models\Neo) {
            $buyerName = $this->buyer->organization_name;
        }

        $product_url = config('app.url') . "/classifieds/". $this->product->id;

        // Generate email subject & content
        $subjectData = [];
        $contentData = [
            'buyer_name' => $buyerName ?? '-',
            'product' => $this->product->title ?? '-',
            'product_url' => $product_url,
            'price' => $this->product->price ?? '-',
            'seller_name' => $sellerName ?? '-',
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
