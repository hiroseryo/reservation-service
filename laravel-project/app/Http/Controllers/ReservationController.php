<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ReservationRequest;

class ReservationController extends Controller
{
    // 予約登録処理
    public function store(Request $request)
    {

        $start_at = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time));

        Reservation::create([
            'shop_id' => $request->shop_id,
            'user_id' => Auth::id(),
            'num_of_users' => $request->num_of_users,
            'start_at' => $start_at,
        ]);

        return redirect()->route('reserve.done');
    }

    // 予約完了画面表示
    public function done()
    {
        return view('done');
    }

    // 予約キャンセル処理
    public function cancel($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== Auth::id()) {
            return redirect()->back()->with('error', '予約をする権限がありません');
        }

        $reservation->delete();

        return redirect()->back()->with('success', '予約をキャンセルしました');
    }

    // 予約変更処理
    public function update(ReservationRequest $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== Auth::id()) {
            return redirect()->back()->with('error', '予約を変更する権限がありません');
        }

        $start_at = date('Y-m-d H:i:s', strtotime($request->date . ' ' . $request->time));
        $reservation->start_at = $start_at;
        $reservation->num_of_users = $request->num_of_users;

        $reservation->save();

        return redirect()->back()->with('success', '予約を変更しました');
    }
}
