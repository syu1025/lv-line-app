<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ログイン - シフト管理システム</title>
    <!-- Tailwind CSSを使用 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f3f4f6;
        }
        .login-form {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .login-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
        }
        .login-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #10b981;
            color: white;
            border-radius: 0.375rem;
            font-weight: 500;
        }
        .login-btn:hover {
            background-color: #059669;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="login-form">
            <h1 class="text-2xl font-bold text-center mb-6">シフト管理システム</h1>

            @if(session('error'))
                <div class="error-message">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div>
                    <label for="username" class="block text-gray-700">ユーザー名</label>
                    <input type="text" id="username" name="username" class="login-input" required autofocus>
                </div>

                <div>
                    <label for="password" class="block text-gray-700">パスワード</label>
                    <input type="password" id="password" name="password" class="login-input" required>
                </div>

                <button type="submit" class="login-btn">ログイン</button>
            </form>
        </div>
    </div>
</body>
</html>
