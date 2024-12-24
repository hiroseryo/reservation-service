<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Mail\ReservationReminderMail;
use Illuminate\Support\Facades\Mail;

class SendReservationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reservation reminders to users who have reservations today.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->format('Y-m-d');

        $reservations = Reservation::with('user', 'shop')
            ->whereDate('start_at', $today)
            ->get();

        foreach ($reservations as $reservation) {

            Mail::to($reservation->user->email)->send(new ReservationReminderMail($reservation));
        }

        $this->info('予約リマインダーを送信しました。');
    }
}
