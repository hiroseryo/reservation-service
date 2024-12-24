<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class ReservationReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    /**
     * Create a new message instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('本日のご予約のリマインド')
            ->view('emails.reservation_reminder')
            ->with([
                'userName' => $this->reservation->user->name,
                'shopName' => $this->reservation->shop->name,
                'startAt' => $this->reservation->start_at->format('H:i'),
                'numOfUsers' => $this->reservation->num_of_users,
            ]);
    }
}
