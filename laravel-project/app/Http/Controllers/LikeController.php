<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;

class LikeController extends Controller
{
    // HOMEから「いいね」を追加
    public function like(Request $request)
    {
        $shop_id = $request->shop_id;

        Like::firstOrCreate([
            'user_id' => Auth::id(),
            'shop_id' => $shop_id,
        ]);

        return response()->json(['result' => true]);
    }

    // HOMEから「いいね」を削除
    public function unlike(Request $request)
    {
        $shop_id = $request->shop_id;

        Like::where('user_id', Auth::id())->where('shop_id', $shop_id)->delete();

        return response()->json(['result' => true]);
    }

    // マイページから「いいね」を削除
    public function unlikeFromMypage($shop_id)
    {
        $user = Auth::user();

        $like = Like::where('user_id', $user->id)->where('shop_id', $shop_id)->first();

        if ($like) {
            $like->delete();
            return redirect()->route('mypage')->with('success', 'いいねを取り消しました');
        } else {
            return redirect()->route('mypage')->with('error', 'いいねをとり消すことができませんでした');
        }
    }
}
