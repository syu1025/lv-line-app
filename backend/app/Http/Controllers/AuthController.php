<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // 認証済みユーザーの配列
    private $users = [
        [
            'username' => 'a',
            'password' => 'aass1122'
        ],
        [
            'username' => 'b',
            'password' => 'aass2233'
        ]
    ];

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

        // ユーザー認証
        foreach ($this->users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                Session::put('authenticated', true);
                Session::put('username', $username);
                return redirect('/');
            }
        }

        // 認証失敗
        return redirect()->route('login')->with('error', 'ユーザー名またはパスワードが正しくありません。');
    }

    // ログアウト処理
    public function logout()
    {
        Session::forget('authenticated');
        Session::forget('username');
        return redirect()->route('login');
    }
}
