<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // レビュー投稿処理
    public function store(Request $request)
    {
        $user = Auth::user();

        $reservation = Reservation::where('id', $request->reservation_id)
            ->where('user_id', $user->id)
            ->where('start_at', '<=', now())
            ->first();

        if (!$reservation) {
            return redirect()->back()->with('error', '評価できる予約がありません。');
        }

        $existingReview = Review::where('user_id', $user->id)
            ->where('shop_id', $request->shop_id)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'この店舗はすでに評価済みです。');
        }

        Review::create([
            'user_id' => $user->id,
            'shop_id' => $request->shop_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', '評価を投稿しました。');
    }
}
