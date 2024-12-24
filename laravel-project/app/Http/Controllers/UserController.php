<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Like;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

class UserController extends Controller
{
    // マイページ表示
    public function mypage()
    {
        $user = Auth::user();

        $reservations = Reservation::where('user_id', $user->id)->with('shop')->orderBy('start_at', 'asc')->get();

        $likedShops = Like::where('user_id', $user->id)->with(['shop.area', 'shop.genre'])->get()->pluck('shop');

        return view('mypage', compact('reservations', 'likedShops'));
    }

    // QRコード表示
    public function show($reservationId)
    {
        $user = Auth::user();

        $reservation = Reservation::where('id', $reservationId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $uniqueData = 'reservation:' . $reservation->id;

        $renderer = new ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($uniqueData);

        return view('qrcode', compact('reservation', 'qrCodeSvg'));
    }
}
