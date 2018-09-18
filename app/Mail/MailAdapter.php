<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailAdapter extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject;
    public $view;
    public $info;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $view, $info)
    {
        $this->subject = $subject;
        $this->view = $view;
        $this->info = $info;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->subject)
            ->view($this->view)
            ->with($this->info);
    }
}
