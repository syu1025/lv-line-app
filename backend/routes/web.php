<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 認証ルート
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 認証が必要なルート
Route::middleware(['auth.check'])->group(function () {
    // メインアプリ
    Route::get('/', function () {
        return view('app');
    });

    // シフト表表示ページへ
    Route::get('/shifts-table', [ShiftController::class, 'getShiftsTable'])
        ->name('shifts-table');

    // API用ルート（これもセッション認証が必要）
    Route::post('/api/shifts', [ShiftController::class, 'store']);
    Route::get('/api/shifts', [ShiftController::class, 'index']);
});
