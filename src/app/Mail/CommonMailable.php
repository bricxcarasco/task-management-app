<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommonMailable extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    private $viewName;

    /**
     * @var string
     */
    private $template;

    /**
     * Create a new message instance.
     *
     * @param string $view
     * @param string $template
     * @return void
     */
    public function __construct($view, $template)
    {
        $this->viewName = $view;
        $this->template = $template;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->text($this->viewName, [
                'template' => $this->template
            ]);
    }
}
