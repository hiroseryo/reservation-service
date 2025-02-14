<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Genre;
use App\Models\Area;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    // HOME画面表示
    public function index(Request $request)
    {
        $genres = Genre::all();
        $areas = Area::all();

        $sort = $request->input('sort', 'random');

        $query = Shop::withAvg('reviews', 'rating')->with(['area', 'genre']);

        if ($request->filled('area_id') && $request->area_id != 'All') {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('genre_id') && $request->genre_id != 'All') {
            $query->where('genre_id', $request->genre_id);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($sort === 'random') {
            $query->inRandomOrder();
        } elseif ($sort === 'low') {
            $query->orderByRaw('reviews_avg_rating IS NULL, reviews_avg_rating ASC');
        } elseif ($sort === 'high') {
            $query->orderByRaw('reviews_avg_rating IS NULL, reviews_avg_rating DESC');
        }

        $shops = $query->get();

        return view('index', compact('shops', 'genres', 'areas', 'request', 'sort'));
    }

    // 詳細画面表示
    public function detail(Shop $shop)
    {
        $shop->load(['area', 'genre', 'reviews.user']);

        return view('detail', compact('shop'));
    }

    // 会員登録後の画面表示
    public function thanks()
    {
        return view('thanks');
    }
}
