<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Area;
use App\Models\Genre;
use App\Http\Requests\StoreCreationRequest;
use App\Http\Requests\StoreUpdateRequest;


class ShopOwnerController extends Controller
{
    // 店舗情報作成フォームの表示
    public function createShop()
    {
        $areas = Area::all();
        $genres = Genre::all();

        if (Auth::user()->shop) {
            return redirect()->route('shop_owner.editShop');
        }

        return view('shop_owner.create_shop', compact('areas', 'genres'));
    }

    // 店舗情報の作成
    public function storeShop(StoreCreationRequest $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $filename = Str::random(20) . '.' . $extension;
            $path = $image->storeAs('images', $filename, 'public');
            $image_url = Storage::url($path);
        }

        Shop::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'area_id' => $request->area_id,
            'genre_id' => $request->genre_id,
            'description' => $request->description,
            'image_url' => $image_url,
        ]);

        return redirect()->route('shop_owner.editShop')->with('success', '店舗情報を登録しました。');
    }

    // 店舗情報編集フォームの表示
    public function editShop()
    {
        $areas = Area::all();
        $genres = Genre::all();

        $shop = Auth::user()->shop;

        if (!$shop) {
            return redirect()->route('shop_owner.createShop')->with('error', '店舗情報がありません。');
        }
        return view('shop_owner.edit_shop', compact('shop', 'areas', 'genres'));
    }

    // 店舗情報の更新
    public function updateShop(StoreUpdateRequest $request)
    {
        $shop = Auth::user()->shop;

        if (!$shop) {
            return redirect()->route('shop_owner.createShop')->with('error', '店舗情報がありません。');
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $filename = Str::random(20) . '.' . $extension;
            $path = $image->storeAs('images', $filename, 'public');
            $image_url = Storage::url($path);

            if ($shop->image_url) {
                Storage::disk('public')->delete(str_replace('/storage', '', $shop->image_url));
            }

            $shop->image_url = $image_url;
        }

        $shop->name = $request->name;
        $shop->area_id = $request->area_id;
        $shop->genre_id = $request->genre_id;
        $shop->description = $request->description;
        $shop->save();

        return redirect()->route('shop_owner.editShop')->with('success', '店舗情報を更新しました。');
    }

    // 店舗の予約一覧表示
    public function reservations()
    {
        $shop = Auth::user()->shop;

        if (!$shop) {
            return redirect()->route('shop_owner.reservations')->with('error', '店舗情報がありません。');
        }

        $reservations = $shop->reservations()->with('user')->orderBy('start_at', 'desc')->get();

        return view('shop_owner.reservations', compact('reservations'));
    }
}
