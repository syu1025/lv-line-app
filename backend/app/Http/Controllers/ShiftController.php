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

        // LINEユーザーIDを取得（実際の実装ではLINEログインから取得）
        $lineUserId = $request->header('X-Line-User-Id') ?? 'test_user';

        try {
            foreach ($request->shifts as $shiftData) {
                Log::info('シフトデータ保存:', $shiftData);
                Shift::updateOrCreate(
                    [
                        'date' => $shiftData['date'],
                        'line_user_id' => $lineUserId
                    ],
                    [
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

    public function index(Request $request)
    {
        // LINEユーザーIDを取得（実際の実装ではLINEログインから取得）
        $lineUserId = $request->header('X-Line-User-Id') ?? 'test_user';

        $shifts = Shift::where('line_user_id', $lineUserId)
            ->orderBy('date')
            ->get();

        return response()->json($shifts);
    }

    /**
     * シフト表のデータを取得
     */
    public function getShiftsTable()
    {
        // すべてのシフトを取得
        // 全シフトを日付でグループ化して取得
        $shifts_date = Shift::orderBy('date')
            ->get()
            ->groupBy('date');

        //dd($shifts);

        return view('shifts-table', compact('shifts_date'));
    }
}
