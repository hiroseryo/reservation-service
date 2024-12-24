<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;


class RegisterController extends Controller
{
    //ユーザー登録画面表示
    public function create()
    {
        return view('auth.register');
    }

    //ユーザー登録処理
    public function store(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        return redirect('/email/verify');
    }

    //メール認証画面表示
    public function showVerifyEmail()
    {
        return view('auth.verify-email');
    }

    //メール認証処理
    public function verifyEmail(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/thanks');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect('/thanks')->with('verified', true);
    }

    //メール認証メール再送信
    public function resendVerifyEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/thanks');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', '認証メールを再送信しました。');
    }
}
