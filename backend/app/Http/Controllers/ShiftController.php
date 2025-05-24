<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function store(Request $request)
    {
        Log::info('シフト登録リクエスト:', $request->all());


        $validator = Validator::make($request->all(), [
            'shifts' => 'required|array',
            'shifts.*.date' => 'required|date',
            'shifts.*.type' => 'required|in:time,lecture',
            'shifts.*.start_time' => 'required_if:shifts.*.type,time|nullable|date_format:H:i',
            'shifts.*.end_time' => 'required_if:shifts.*.type,time|nullable|date_format:H:i|after:shifts.*.start_time',
            'shifts.*.lectures' => 'required_if:shifts.*.type,lecture|nullable|array',
            'shifts.*.lectures.*' => 'integer|min:1|max:7'
        ]);

        if ($validator->fails()) {
            Log::error('バリデーションエラー:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $request = request();
        $user = $request->attributes->get('user');
        try {
            foreach ($request->shifts as $shiftData) {
                Log::info('シフトデータ保存:', $shiftData);
                Shift::create([
                        'date' => $shiftData['date'],
                        'user_name' => $user->name,
                        'type' => $shiftData['type'],
                        'start_time' => $shiftData['start_time'] ?? null,
                        'end_time' => $shiftData['end_time'] ?? null,
                        'lectures' => $shiftData['lectures'] ?? null
                    ]
                );
            }

            return response()->json(['message' => 'シフトが登録されました'], 200);
        } catch (\Exception $e) {
            Log::error('シフト保存エラー:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'シフトの登録に失敗しました'], 500);
        }
    }

    //apiでシフトデータを取得
    public function index(Request $request)
    {

        // ミドルウェアで設定されたユーザー情報を取得
        $user = $request->attributes->get('user');
        $shifts = Shift::where('user_name', $user->name)
            ->orderBy('date')
            ->get();

        return response()->json([
            'shifts' => $shifts,
            'user' => $user
        ]);
        //return view('shifts-table', compact('shifts', 'user'));
    }

    /**
     * シフト表のデータを取得
     */
    public function getShiftsTable(Request $request)
    {
        $user = $request->attributes->get('user');
        $userName = $user ? $user->name : 'ゲスト';
        //dd($userName);
        // すべてのシフトを取得
        // 全シフトを日付でグループ化して取得
        //$shifts_date = Shift::orderBy('date')
        //    ->get()
        //    ->groupBy('date');

        //dd($shifts);

        return view('shifts-table', compact('userName'));
    }
}
