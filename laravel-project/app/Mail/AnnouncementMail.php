<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $messageBody;
    /**
     * Create a new message instance.
     */
    public function __construct($title, $messageBody)
    {
        $this->title = $title;
        $this->messageBody = $messageBody;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject($this->title)
            ->view('emails.announcement')
            ->with([
                'messageBody' => $this->messageBody,
            ]);
    }
}
