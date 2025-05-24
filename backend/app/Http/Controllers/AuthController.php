<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // ログインフォームを表示
    public function showLogin()
    {
        // すでにログイン済みの場合はリダイレクト
        if (Session::has('authenticated')) {
            return redirect('/');
        }

        return view('auth.login');
    }

    // ログイン処理
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        // データベースからユーザーを検索
        $user = User::where('name', $username)->first();

        if ($user && Hash::check($password, $user->password)) {
            Session::put('authenticated', true);
            Session::put('username', $username);
            return redirect('/');
        }

        // 認証失敗
        return redirect()->route('login')->with('error', 'ユーザー名またはパスワードが正しくありません。');
    }

    // ログアウト処理
    public function logout(Request $request)
    {
        Session::forget('authenticated');
        Session::forget('username');
        return redirect()->route('login');
    }
}
