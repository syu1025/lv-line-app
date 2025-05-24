<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>シフト登録</title>
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- ヘッダー -->
    <div class="fixed top-0 left-0 w-full bg-white border-b border-gray-200 py-2 px-4 flex justify-between items-center shadow-sm z-50">
        <div class="flex items-center space-x-4">
            <h1 class="text-lg font-semibold text-gray-800">シフト登録</h1>
            <span class="text-sm text-gray-600">ログインユーザー：{{ $user->name ?? 'ゲスト' }}さん</span>
        </div>
        <div class="flex items-center space-x-2">
            <a href="/shifts-table" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                シフト表
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded text-sm">
                    ログアウト
                </button>
            </form>
        </div>
    </div>

    <!-- メインコンテンツ（ヘッダー分の余白を追加） -->
    <div style="padding-top: 45px;">
        <div id="app"></div>
    </div>
</body>
</html>
