<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopOwnerController;
use App\Http\Controllers\PaymentController;

Route::get('/', [ShopController::class, 'index'])->name('shops.index');
Route::get('/detail/{shop}', [ShopController::class, 'detail'])->name('shops.detail');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware(['auth'])->name('logout');

Route::get('/email/verify', [RegisterController::class, 'showVerifyEmail'])->middleware('auth', 'role:user')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [RegisterController::class, 'verifyEmail'])->middleware('auth', 'signed', 'role:user')->name('verification.verify');
Route::post('/email/resend', [RegisterController::class, 'resendVerifyEmail'])->middleware('auth', 'throttle:6,1', 'role:user')->name('verification.resend');

Route::middleware(['guest'])->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/thanks', [ShopController::class, 'thanks']);

    Route::post('/reserve', [ReservationController::class, 'store'])->name('reserve.store');
    Route::get('/done', [ReservationController::class, 'done'])->name('reserve.done');
    Route::post('/reservation/{id}/cancel', [ReservationController::class, 'cancel'])->name('reservation.cancel');
    Route::post('/reservation/{id}/update', [ReservationController::class, 'update'])->name('reservation.update');

    Route::post('/like', [LikeController::class, 'like'])->name('like');
    Route::post('/unlike', [LikeController::class, 'unlike'])->name('unlike');
    Route::post('/like/{shop}', [LikeController::class, 'unlikeFromMypage'])->name('unlike.from_mypage');

    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/mypage', [UserController::class, 'mypage'])->name('mypage');
    Route::get('/mypage/reservations/{reservationId}/qrcode', [UserController::class, 'show'])->name('reservations.qrcode.show');

    Route::get('/reservations/{reservationId}/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/reservations/{reservationId}/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/reservations/{reservationId}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

Route::prefix('shop_owner')->middleware(['auth', 'role:shop_owner'])->group(function () {
    Route::get('shop/create', [ShopOwnerController::class, 'createShop'])->name('shop_owner.createShop');
    Route::post('shop/store', [ShopOwnerController::class, 'storeShop'])->name('shop_owner.storeShop');
    Route::get('shop/edit', [ShopOwnerController::class, 'editShop'])->name('shop_owner.editShop');
    Route::post('shop/update', [ShopOwnerController::class, 'updateShop'])->name('shop_owner.updateShop');
    Route::get('reservations', [ShopOwnerController::class, 'reservations'])->name('shop_owner.reservations');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('owners/create', [AdminController::class, 'createOwner'])->name('admin.owners.create');
    Route::post('owners', [AdminController::class, 'storeOwner'])->name('admin.owners.store');

    Route::get('announcement', [AdminController::class, 'announcement'])->name('admin.announcement');
    Route::post('announcement/send', [AdminController::class, 'sendAnnouncement'])->name('admin.sendAnnouncement');
});
