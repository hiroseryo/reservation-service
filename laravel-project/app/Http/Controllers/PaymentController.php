<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class PaymentController extends Controller
{
    public function checkout(Request $request, $reservationId)
    {
        $user = $request->user();

        $reservation = Reservation::where('id', $reservationId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($reservation->isPaid()) {
            return redirect()->route('mypage')->with('error', '既に支払い済みです');
        }

        Stripe::setApiKey(config('cashier.secret'));

        $amount = 1200;
        $currency = 'jpy';

        $successUrl = route('payment.success', ['reservationId' => $reservation->id]);
        $cancelUrl = route('payment.cancel', ['reservationId' => $reservation->id]);

        $session = CheckoutSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => '予約' . $reservation->shop->name,
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
        ]);

        $reservation->stripe_session_id = $session->id;
        $reservation->save();

        return redirect($session->url, 303);
    }

    public function success(Request $request, $reservationId)
    {
        $user = $request->user();

        $reservation = Reservation::where('id', $reservationId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        Stripe::setApiKey(config('cashier.secret'));

        $sessionId = $request->query('session_id');
        if (!$sessionId || $sessionId !== $reservation->stripe_session_id) {
            return redirect()->route('mypage')->with('error', '不正なリクエストです');
        }

        $session = CheckoutSession::retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            $reservation->payment_status = 'paid';
            $reservation->save();

            return redirect()->route('mypage')->with('success', '支払いが完了しました');
        }

        return redirect()->route('mypage')->with('error', '支払いに失敗しました');
    }

    public function cancel(Request $request, $reservationId)
    {
        $user = $request->user();

        $reservation = Reservation::where('id', $reservationId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $reservation->payment_status = 'canceled';
        $reservation->save();

        return redirect()->route('mypage')->with('error', '支払いがキャンセルされました');
    }
}
