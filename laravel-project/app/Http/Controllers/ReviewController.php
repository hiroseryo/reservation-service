<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Shop;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function create($shop_id)
    {
        $shop = Shop::findOrFail($shop_id);
        $user = Auth::user();
        $review = Review::where('user_id', $user->id)->where('shop_id', $shop_id)->first();

        $reservation = Reservation::where('shop_id', $shop_id)->where('user_id', $user->id)->where('start_at', '<=', now())->first();
        if (!$reservation) {
            return redirect()->route('shops.detail', $shop_id)->with('error', '来店していないため口コミを投稿できません');
        }

        return view('review', compact('shop', 'review', 'reservation'));
    }

    public function store(ReviewRequest $request, $shop_id)
    {
        Shop::findOrFail($shop_id);
        $user = Auth::user();

        $review = Review::firstOrNew([
            'user_id' => $user->id,
            'shop_id' => $shop_id,
        ]);

        $review->rating = $request->input('rating') ?? '';
        $review->comment = $request->input('comment') ?? '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = 'png';
            $filename = Str::random(20) . '.' . $extension;
            $path = $image->storeAs('images', $filename, 'public');
            if ($review->img_url) {
                $parsedUrl = parse_url($review->img_url);

                $pathFromUrl = ltrim($parsedUrl['path'], '/');
                $relativePath = preg_replace('#^storage/#', '', $pathFromUrl);
                if (Storage::disk('public')->exists($relativePath)) {
                    if (Storage::disk('public')->delete($relativePath)) {
                        Log::info("ファイルの削除に成功しました: " . $relativePath);
                    } else {
                        Log::error("ファイルの削除に失敗しました: " . $relativePath);
                    }
                } else {
                    Log::error("削除対象のファイルが見つかりません: " . $relativePath);
                }
            }
            $img_url = Storage::url($path);
            $review->img_url = $img_url;
        } else {
            $review->img_url = $request->input('old_img') ?? '';
        }
        $review->save();
        return redirect()->route('shops.detail', ['shop' => $shop_id])->with('success', '口コミを投稿しました');
    }

    public function destroyByUser($reviewId)
    {
        $review = Review::findOrFail($reviewId);

        if ($review->user_id !== Auth::id()) {
            abort(403, '権限がありません');
        }

        if ($review->img_url) {
            $parsedUrl = parse_url($review->img_url);

            $pathFromUrl = ltrim($parsedUrl['path'], '/');
            $relativePath = preg_replace('#^storage/#', '', $pathFromUrl);
            if (Storage::disk('public')->exists($relativePath)) {
                if (Storage::disk('public')->delete($relativePath)) {
                }
            }
        }

        $review->delete();

        return back()->with('success', '口コミを削除しました');
    }
}
