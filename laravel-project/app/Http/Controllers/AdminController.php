<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\AnnouncementMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CreateShopOwnerRequest;
use App\Http\Requests\SendEmailRequest;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // 店舗オーナー登録画面表示
    public function createOwner()
    {
        return view('admin.create');
    }

    // 店舗オーナー登録処理
    public function storeOwner(CreateShopOwnerRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('shop_owner');

        return redirect()->route('admin.owners.create')->with('success', '店舗オーナーを登録しました');
    }

    // お知らせメール送信画面表示
    public function announcement()
    {
        return view('admin.announcement');
    }

    // お知らせメール送信処理
    public function sendAnnouncement(SendEmailRequest $request)
    {
        $title = $request->input('title');
        $messageBody = $request->input('message');

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new AnnouncementMail($title, $messageBody));
        }

        return redirect()->back()->with('success', 'お知らせメールを送信しました');
    }

    public function destroyByAdmin($reviewId)
    {
        $review = Review::findOrFail($reviewId);

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

        return back()->with('success', '管理者として口コミを削除しました。');
    }
}
