<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mailtrap extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $view, $with)
    {
        $this->subject = $subject;
        $this->view = $view;
        $this->with = $with;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS', 'admin@eklanku.com'), env('MAIL_FROM_NAME', 'admin'))
            ->subject($this->subject)
            ->view($this->view)
            ->with($this->with);
            // ->with([
            //     'name' => 'New Mailtrap User',
            //     'link' => 'https://mailtrap.io/inboxes'
            // ]);
    }
}
