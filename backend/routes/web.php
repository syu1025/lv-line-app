<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShiftController;

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

Route::get('/', function () {
    return view('app');
});

// シフト表示ページ
Route::get('/shifts-table', function () {
    return view('shifts-table');
});

// シフト表のデータを取得するAPI
Route::get('/api/shifts-table', [ShiftController::class, 'getShiftsTable']);
