<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>シフト表 - シフト管理システム</title>
    <!-- Tailwind CSSを使用 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* 共通の列幅を設定 */
        .col-fixed {
            width: 100px;
            min-width: 100px;
        }
        .col-date {
            width: 100px;
            min-width: 100px;
        }

        /* スクロール同期用のコンテナ */
        .sync-scroll {
            overflow-x: auto;
            position: relative;
            max-width: 100%;
        }

        /* スクロールバーを最後のテーブルにのみ表示 */
        .hide-scrollbar {
            overflow-x: scroll;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        /* テーブルセルのスタイル調整 */
        th, td {
            padding: 0.5rem;
            font-size: 0.9rem;
            text-align: center;
        }

        /* 固定列のスタイル */
        .sticky-col {
            position: sticky;
            left: 0;
            z-index: 10;
            background-color: white;
            font-weight: 500;
            text-align: left;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        /* テーブルのサイズ調整 */
        table {
            width: max-content;
            min-width: 100%;
        }

        /* 日付フィルターのスタイル */
        .date-filter {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 1rem;
        }

        .date-filter input {
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        .date-filter button {
            background-color: #10b981;
            color: white;
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        /* レスポンシブ対応 */
        @media (max-width: 640px) {
            .col-fixed {
                width: 80px;
                min-width: 80px;
            }
            .col-date {
                width: 80px;
                min-width: 80px;
            }
            th, td {
                padding: 0.4rem;
                font-size: 0.8rem;
            }
            .date-filter {
                flex-direction: column;
                align-items: stretch;
            }
        }

        /* タイムライン用のスタイル */
        .timeline-day {
            min-width: 300px;
            border-right: 2px solid #e5e7eb;
            position: relative;
        }

        .timeline-header {
            background-color: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            padding: 1rem;
            font-weight: 600;
            text-align: center;
        }

        .timeline-grid {
            position: relative;
            height: 480px; /* 14:00-22:00の8時間 × 60px */
            background: linear-gradient(to bottom, transparent 59px, #e5e7eb 59px, #e5e7eb 60px, transparent 60px);
            background-size: 100% 60px; /* 1時間 = 60px */
        }

        .time-axis {
            width: 80px;
            position: relative;
            flex-shrink: 0;
        }

        .time-label {
            position: absolute;
            left: 0;
            width: 70px;
            text-align: right;
            font-size: 0.75rem;
            color: #6b7280;
            transform: translateY(-50%);
            padding-right: 10px;
        }

        .shift-block {
            position: absolute;
            left: 85px;
            right: 10px;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 0.75rem;
            color: white;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .shift-block.lecture {
            background-color: #3b82f6; /* 青色：講義指定 */
            border-left: 4px solid #1d4ed8;
        }

        .shift-block.time {
            background-color: #10b981; /* 緑色：時間指定 */
            border-left: 4px solid #047857;
        }

        .shift-block.overlap {
            opacity: 0.8;
            z-index: 15;
        }

        .timeline-container-wrapper {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            min-height: 580px;
            overflow: hidden;
        }

        .timeline-content {
            flex: 1;
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            overflow-y: hidden;
        }

        /* メインコンテナのスクロール設定 */
        #timeline-container {
            overflow: hidden;
        }

        /* モバイル専用スタイル */
        @media (max-width: 768px) {
            /* 週間表示用のコンパクトスタイル */
            .mobile-week-view .timeline-container-wrapper {
                flex-direction: row;
                gap: 0;
                padding: 0.5rem;
                min-height: 550px; /* 400px から 550px 程度に調整（ヘッダー分も考慮） */
                overflow-x: auto;
            }

            .mobile-week-view .time-axis {
                width: 50px;
                position: sticky;
                left: 0;
                background: white;
                z-index: 20;
                border-right: 1px solid #e5e7eb;
            }

            .mobile-week-view .timeline-content {
                flex-direction: row;
                gap: 1px;
                overflow-x: visible;
                min-width: max-content;
            }

            .mobile-week-view .timeline-day {
                min-width: 60px;
                max-width: 60px;
                border-right: 1px solid #e5e7eb;
                cursor: pointer;
                transition: background-color 0.2s;
            }

            .mobile-week-view .timeline-day:hover {
                background-color: #f9fafb;
            }

            .mobile-week-view .timeline-day.selected {
                background-color: #eff6ff;
                border-right: 2px solid #3b82f6;
            }

            .mobile-week-view .timeline-header {
                padding: 0.5rem 0.25rem;
                font-size: 0.7rem;
                line-height: 1.2;
                min-height: 60px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                text-align: center;
            }

            .mobile-week-view .timeline-grid {
                height: 480px; /* 300px から 480px に変更 */
                position: relative;
            }

            .mobile-week-view .shift-block {
                left: 2px;
                right: 2px;
                font-size: 0.6rem;
                padding: 1px 2px;
                border-radius: 2px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                min-height: 20px; /* 最小高さを追加 */
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .mobile-week-view .shift-block .font-semibold {
                font-size: 0.55rem;
                line-height: 1;
                margin-bottom: 1px;
            }

            .mobile-week-view .shift-block .text-xs {
                font-size: 0.5rem;
                line-height: 1;
                opacity: 0.8;
            }

            .mobile-week-view .time-label {
                font-size: 0.6rem;
                width: 45px;
                padding-right: 5px;
            }

            /* シングルデー詳細表示用 */
            .mobile-day-view .timeline-container-wrapper {
                flex-direction: column;
                gap: 0;
                padding: 0.5rem;
                min-height: auto;
            }

            .mobile-day-view .time-axis {
                width: 70px;
                position: sticky;
                top: 0;
                background: white;
                z-index: 20;
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 0.5rem;
            }

            .mobile-day-view .timeline-content {
                flex-direction: column;
                gap: 0;
                overflow-x: hidden;
            }

            .mobile-day-view .timeline-day {
                min-width: 100%;
                border-right: none;
                margin-bottom: 1rem;
            }

            .mobile-day-view .timeline-grid {
                height: 600px;
            }

            .mobile-day-view .shift-block {
                left: 80px;
                right: 10px;
                font-size: 0.75rem;
                padding: 4px 8px;
            }

            .mobile-day-view .time-label {
                font-size: 0.75rem;
                width: 65px;
            }
        }

        /* 日付ヘッダーのスタイル改善 */
        .date-header-compact {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .date-number {
            font-size: 0.8rem;
            font-weight: 600;
            line-height: 1;
        }

        .date-weekday {
            font-size: 0.6rem;
            color: #6b7280;
            line-height: 1;
            margin-top: 2px;
        }

        .today-indicator {
            background-color: #3b82f6 !important;
            color: white !important;
        }

        .today-indicator .date-weekday {
            color: #bfdbfe !important;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- ヘッダー -->
    <div id="header" class="fixed top-0 left-0 w-full bg-white border-b border-gray-200 py-2 px-4 flex justify-between items-center shadow-sm z-50">
        <div class="flex items-center space-x-4">
            <h1 class="text-lg font-semibold text-gray-800">シフト管理</h1>
            <span class="text-sm text-gray-600">ログインユーザー：{{ $user->name ?? 'ゲスト' }}さん</span>
        </div>
        <div class="flex items-center space-x-2">
            <a href="/" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                シフト登録
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded text-sm">
                    ログアウト
                </button>
            </form>
        </div>
    </div>

    <div class="w-full max-w-full px-4 mx-auto" style="padding-top: 60px;">
        <!-- ローディング表示 -->
        <div id="loading" class="text-center py-8">
            <p>シフトデータを読み込み中...</p>
        </div>

        <!-- エラー表示 -->
        <div id="error" class="text-center py-8 text-red-600" style="display: none;">
            <p>データの読み込みに失敗しました。</p>
        </div>

        <main id="main-content" style="display: none;">
            <!-- 日付フィルター（デスクトップ用） -->
            <div class="hidden md:flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold"></h2>
                <div class="date-filter">
                    <label for="start_date" class="text-sm">開始日:</label>
                    <input type="date" id="start_date" name="start_date" class="date-input">
                    <label for="end_date" class="text-sm">終了日:</label>
                    <input type="date" id="end_date" name="end_date" class="date-input">
                    <button id="filter_button" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">表示</button>
                </div>
            </div>

            <!-- モバイル用ヘッダー -->
            <div class="md:hidden mb-4">
                <h2 class="text-lg font-semibold mb-3">シフト表</h2>

                <!-- 週間ナビゲーション -->
                <div class="bg-white rounded-lg shadow-sm border p-3 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <button id="prev-week" class="flex items-center px-2 py-1 text-gray-600 hover:text-gray-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>

                        <div class="text-center">
                            <div class="text-sm font-medium text-gray-800" id="week-range">
                                <!-- JavaScriptで設定 -->
                            </div>
                        </div>

                        <button id="next-week" class="flex items-center px-2 py-1 text-gray-600 hover:text-gray-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>

                    <button id="today-week-btn" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded text-sm">
                        今週
                    </button>
                </div>

                <!-- ビュー切り替えタブ -->
                <div class="flex bg-gray-100 rounded-lg p-1 mb-4">
                    <button id="week-tab" class="flex-1 py-2 px-3 rounded-md text-sm font-medium bg-white text-gray-900 shadow-sm">
                        週間表示
                    </button>
                    <button id="day-tab" class="flex-1 py-2 px-3 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700">
                        日別詳細
                    </button>
                </div>
            </div>

            <!-- タイムラインコンテナ -->
            <div id="timeline-container" class="w-full">
                <!-- JavaScriptで動的生成 -->
            </div>

            <!-- モバイル用シングルデー詳細ビュー -->
            <div id="single-day-container" class="hidden md:hidden">
                <div class="bg-white rounded-lg shadow-sm border p-3 mb-4">
                    <div class="flex items-center justify-between">
                        <button id="back-to-week" class="flex items-center text-blue-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            週間表示に戻る
                        </button>
                        <div class="text-center">
                            <div class="font-semibold" id="single-day-date"></div>
                            <div class="text-xs text-gray-500" id="single-day-weekday"></div>
                        </div>
                        <div class="w-20"></div> <!-- スペーサー -->
                    </div>
                </div>
                <div id="single-day-timeline">
                    <!-- シングルデーのタイムライン -->
                </div>
            </div>
        </main>
    </div>

    <script>
        // 講義コマと時間のマッピング
        const lectureDefinitions = {
            4: { name: '4講', start: '15:00', end: '16:30' },
            5: { name: '5講', start: '16:40', end: '18:10' },
            6: { name: '6講', start: '18:20', end: '19:50' },
            7: { name: '7講', start: '20:00', end: '21:30' }
        };

        // タイムライン表示の設定
        const timelineConfig = {
            startHour: 14,
            endHour: 22,
            hourHeight: 60, // 1時間あたりのピクセル数
            minuteHeight: 1 // 1分あたりのピクセル数
        };

        // モバイル表示の状態管理
        let mobileViewState = {
            currentView: 'week', // 'week' or 'day'
            selectedDate: null,
            weekStartDate: null
        };

        // ページロード時にAPIからデータを取得
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const response = await fetch('/api/shifts', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error('データの取得に失敗しました');
                }

                const data = await response.json();

                // デバッグ用ログ
                console.log('取得したシフトデータ:', data.shifts);

                document.getElementById('loading').style.display = 'none';
                document.getElementById('main-content').style.display = 'block';

                // レスポンシブ表示の初期化
                initializeResponsiveView(data.shifts);

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('loading').style.display = 'none';
                document.getElementById('error').style.display = 'block';
            }
        });

        // レスポンシブ表示の初期化
        function initializeResponsiveView(shifts) {
            const isMobile = window.innerWidth < 768;

            if (isMobile) {
                setupMobileView(shifts);
            } else {
                generateTimelineView(shifts);
            }

            // リサイズイベントの監視
            window.addEventListener('resize', () => {
                const newIsMobile = window.innerWidth < 768;
                if (newIsMobile !== isMobile) {
                    location.reload(); // 簡易的にリロード
                }
            });
        }

        // モバイルビューのセットアップ
        function setupMobileView(shifts) {
            console.log('setupMobileView - 受信したシフト:', shifts);

            // 今日を基準とした週の開始日を設定
            const today = new Date();
            // mobileViewState.weekStartDate = getWeekStartDate(today); // 元のコード

            // --- テスト用日付設定 ---
            // 例: 2025年5月7日を含む週を表示したい場合 (2025年5月5日月曜日を開始日とする)
            mobileViewState.weekStartDate = new Date("2025-05-05");
            // --- ここまでテスト用日付設定 ---

            mobileViewState.selectedDate = new Date(mobileViewState.weekStartDate); // 週の初日を選択状態にするか、todayのままにするか検討
            // mobileViewState.selectedDate = today; // もし「今日」を選択状態にしたい場合はこちら

            // データを日付でグループ化
            const shiftsByDate = {};
            shifts.forEach(shift => {
                if (!shiftsByDate[shift.date]) {
                    shiftsByDate[shift.date] = [];
                }
                shiftsByDate[shift.date].push(shift);
            });

            console.log('setupMobileView - グループ化後:', shiftsByDate);
            console.log('setupMobileView - 表示対象の週の開始日:', mobileViewState.weekStartDate.toISOString().split('T')[0]);


            // 週間表示を初期表示
            showMobileWeekView(shiftsByDate);
            setupMobileNavigation(shiftsByDate, shifts);
        }

        // 週の開始日を取得（月曜日開始）
        function getWeekStartDate(date) {
            const d = new Date(date);
            const day = d.getDay();
            const diff = d.getDate() - day + (day === 0 ? -6 : 1); // 月曜日を週の開始とする
            return new Date(d.setDate(diff));
        }

        // 5日間の日付配列を生成
        function getFiveDayDates(startDate) {
            const dates = [];
            for (let i = 0; i < 5; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);
                dates.push(date.toISOString().split('T')[0]);
            }
            return dates;
        }

        // モバイル週間表示
        function showMobileWeekView(shiftsByDate) {
            const container = document.getElementById('timeline-container');
            const singleDayContainer = document.getElementById('single-day-container');

            // ビューの切り替え
            container.style.display = 'block';
            singleDayContainer.style.display = 'none';
            container.className = 'w-full mobile-week-view';

            // 5日間の日付を取得
            const dates = getFiveDayDates(mobileViewState.weekStartDate);

            // 週間範囲の表示更新
            updateWeekRangeDisplay(dates);

            // タイムラインを描画
            renderMobileWeekTimeline(dates, shiftsByDate);
        }

        // 週間範囲の表示更新
        function updateWeekRangeDisplay(dates) {
            const weekRangeElement = document.getElementById('week-range');
            if (weekRangeElement) {
                const startDate = new Date(dates[0]);
                const endDate = new Date(dates[dates.length - 1]);
                const startStr = `${startDate.getMonth() + 1}/${startDate.getDate()}`;
                const endStr = `${endDate.getMonth() + 1}/${endDate.getDate()}`;
                weekRangeElement.textContent = `${startStr} - ${endStr}`;
            }
        }

        // モバイル週間タイムライン描画
        function renderMobileWeekTimeline(dates, shiftsByDate) {
            const container = document.getElementById('timeline-container');
            const today = new Date().toISOString().split('T')[0];

            // デバッグ用ログ
            console.log('renderMobileWeekTimeline - dates:', dates);
            console.log('renderMobileWeekTimeline - shiftsByDate:', shiftsByDate);

            let html = `
                <div class="timeline-container-wrapper">
                    <div class="time-axis">
                        ${generateTimeAxis()}
                    </div>
                    <div class="timeline-content">
                        ${dates.map(date => {
                            const isToday = date === today;
                            const dateObj = new Date(date);
                            const dayNum = dateObj.getDate();
                            const weekday = dateObj.toLocaleDateString('ja-JP', { weekday: 'short' });

                            // デバッグ用ログ
                            console.log(`日付 ${date} のシフト:`, shiftsByDate[date] || []);

                            return `
                                <div class="timeline-day ${isToday ? 'today-indicator' : ''}" data-date="${date}" onclick="showDayDetail('${date}')">
                                    <div class="timeline-header">
                                        <div class="date-header-compact">
                                            <div class="date-number">${dayNum}</div>
                                            <div class="date-weekday">${weekday}</div>
                                        </div>
                                    </div>
                                    <div class="timeline-grid">
                                        ${generateMobileShiftBlocks(shiftsByDate[date] || [])}
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            `;

            container.innerHTML = html;
        }

        // モバイル用シフトブロック生成（修正版）
        function generateMobileShiftBlocks(shifts) {
            console.log('[DEBUG] generateMobileShiftBlocks - 入力シフト (生データ):', JSON.parse(JSON.stringify(shifts)));

            const processedShifts = processShiftsForTimeline(shifts);
            console.log('[DEBUG] generateMobileShiftBlocks - 処理後シフト (processedShifts):', JSON.parse(JSON.stringify(processedShifts)));

            if (!processedShifts || processedShifts.length === 0) {
                console.log('[DEBUG] generateMobileShiftBlocks - 表示する処理済みシフトはありません。');
                return '';
            }

            return processedShifts.map(shift => {
                if (!shift.startTime || !shift.endTime) {
                    console.error('[DEBUG] generateMobileShiftBlocks - startTimeまたはendTimeが未定義です:', shift);
                    return ''; // エラーケース
                }
                const startMinutes = timeToMinutes(shift.startTime);
                const endMinutes = timeToMinutes(shift.endTime);

                if (isNaN(startMinutes) || isNaN(endMinutes)) {
                    console.error('[DEBUG] generateMobileShiftBlocks - startMinutesまたはendMinutesがNaNです:', shift, shift.startTime, shift.endTime);
                    return '';
                }

                const duration = endMinutes - startMinutes;

                const baseMinutes = timelineConfig.startHour * 60;
                const topPosition = (startMinutes - baseMinutes) * timelineConfig.minuteHeight;
                const height = duration * timelineConfig.minuteHeight;

                console.log(`[DEBUG] generateMobileShiftBlocks - シフトブロック計算値:
                    ユーザー: ${shift.user_name},
                    時間: ${shift.startTime}-${shift.endTime},
                    startMin: ${startMinutes}, endMin: ${endMinutes}, durationMin: ${duration},
                    top: ${topPosition}px, height: ${height}px,
                    displayName: ${shift.displayName}`);

                if (height <= 0) {
                    console.warn(`[DEBUG] generateMobileShiftBlocks - 計算されたheightが0以下です: height=${height}, shift:`, shift);
                }
                 if (topPosition < 0 || topPosition > 480) { // 480はグリッドの高さ
                    console.warn(`[DEBUG] generateMobileShiftBlocks - topPositionがグリッド範囲外の可能性があります: top=${topPosition}, shift:`, shift);
                }


                return `
                    <div class="shift-block ${shift.shiftType}"
                         style="top: ${topPosition}px; height: ${height}px;"
                         title="${shift.displayName}">
                        <div class="font-semibold">${shift.user_name}</div>
                        <div class="text-xs">${shift.startTime} - ${shift.endTime}</div>
                    </div>
                `;
            }).join('');
        }

        // 日別詳細表示
        function showDayDetail(date) {
            mobileViewState.selectedDate = new Date(date);
            mobileViewState.currentView = 'day';

            const container = document.getElementById('timeline-container');
            const singleDayContainer = document.getElementById('single-day-container');

            // ビューの切り替え
            container.style.display = 'none';
            singleDayContainer.style.display = 'block';

            // 日付情報の更新
            const dateObj = new Date(date);
            document.getElementById('single-day-date').textContent =
                `${dateObj.getMonth() + 1}月${dateObj.getDate()}日`;
            document.getElementById('single-day-weekday').textContent =
                dateObj.toLocaleDateString('ja-JP', { weekday: 'long' });

            // 現在のシフトデータから該当日のデータを取得
            renderSingleDayTimelineFromCache(date);
        }

        // キャッシュからシングルデータイムライン描画
        function renderSingleDayTimelineFromCache(date) {
            // グローバルなシフトデータを使用
            if (window.cachedShifts) {
                const shiftsByDate = {};
                window.cachedShifts.forEach(shift => {
                    if (!shiftsByDate[shift.date]) {
                        shiftsByDate[shift.date] = [];
                    }
                    shiftsByDate[shift.date].push(shift);
                });

                const singleDayTimeline = document.getElementById('single-day-timeline');
                singleDayTimeline.className = 'mobile-day-view';

                const html = `
                    <div class="timeline-container-wrapper">
                        <div class="time-axis">
                            ${generateTimeAxis()}
                        </div>
                        <div class="timeline-content">
                            ${generateDayTimeline(date, shiftsByDate[date] || [])}
                        </div>
                    </div>
                `;

                singleDayTimeline.innerHTML = html;
            }
        }

        // モバイルナビゲーションのセットアップ
        function setupMobileNavigation(shiftsByDate, allShifts) {
            // シフトデータをキャッシュ
            window.cachedShifts = allShifts;

            // 週間ナビゲーション
            document.getElementById('prev-week')?.addEventListener('click', () => {
                mobileViewState.weekStartDate.setDate(mobileViewState.weekStartDate.getDate() - 7);
                showMobileWeekView(shiftsByDate);
            });

            document.getElementById('next-week')?.addEventListener('click', () => {
                mobileViewState.weekStartDate.setDate(mobileViewState.weekStartDate.getDate() + 7);
                showMobileWeekView(shiftsByDate);
            });

            document.getElementById('today-week-btn')?.addEventListener('click', () => {
                const today = new Date();
                mobileViewState.weekStartDate = getWeekStartDate(today);
                showMobileWeekView(shiftsByDate);
            });

            // ビュー切り替えタブ
            document.getElementById('week-tab')?.addEventListener('click', () => {
                if (mobileViewState.currentView !== 'week') {
                    mobileViewState.currentView = 'week';
                    showMobileWeekView(shiftsByDate);
                    updateTabState('week');
                }
            });

            document.getElementById('day-tab')?.addEventListener('click', () => {
                if (mobileViewState.currentView !== 'day') {
                    const today = new Date().toISOString().split('T')[0];
                    showDayDetail(today);
                    updateTabState('day');
                }
            });

            // 週間表示に戻るボタン
            document.getElementById('back-to-week')?.addEventListener('click', () => {
                mobileViewState.currentView = 'week';
                showMobileWeekView(shiftsByDate);
                updateTabState('week');
            });
        }

        // タブの状態更新
        function updateTabState(activeTab) {
            const weekTab = document.getElementById('week-tab');
            const dayTab = document.getElementById('day-tab');

            if (activeTab === 'week') {
                weekTab?.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                weekTab?.classList.remove('text-gray-500');
                dayTab?.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                dayTab?.classList.add('text-gray-500');
            } else {
                dayTab?.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                dayTab?.classList.remove('text-gray-500');
                weekTab?.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                weekTab?.classList.add('text-gray-500');
            }
        }

        // 既存の関数（デスクトップ用）
        function generateTimelineView(shifts) {
            // 日付でグループ化
            const shiftsByDate = {};
            shifts.forEach(shift => {
                if (!shiftsByDate[shift.date]) {
                    shiftsByDate[shift.date] = [];
                }
                shiftsByDate[shift.date].push(shift);
            });

            const dates = Object.keys(shiftsByDate).sort();

            // タイムラインを描画
            renderTimeline(dates, shiftsByDate);

            // 日付フィルター機能を設定
            setupTimelineFilter(dates, shiftsByDate);
        }

        // タイムライン描画関数
        function renderTimeline(dates, shiftsByDate) {
            const container = document.getElementById('timeline-container');

            // コンテナのHTML構造を作成
            let html = `
                <div class="timeline-container-wrapper">
                    <div class="time-axis">
                        ${generateTimeAxis()}
                    </div>
                    <div class="timeline-content">
                        ${dates.map(date => generateDayTimeline(date, shiftsByDate[date] || [])).join('')}
                    </div>
                </div>
            `;

            container.innerHTML = html;
        }

        // 時間軸を生成
        function generateTimeAxis() {
            let html = '';
            for (let hour = timelineConfig.startHour; hour <= timelineConfig.endHour; hour++) {
                const topPosition = (hour - timelineConfig.startHour) * timelineConfig.hourHeight;
                html += `
                    <div class="time-label" style="top: ${topPosition}px;">
                        ${hour.toString().padStart(2, '0')}:00
                    </div>
                `;
            }
            return html;
        }

        // 1日分のタイムラインを生成
        function generateDayTimeline(date, shifts) {
            const formattedDate = new Date(date).toLocaleDateString('ja-JP', {
                month: 'numeric',
                day: 'numeric',
                weekday: 'short'
            });

            // シフトデータを時間情報付きで変換
            const processedShifts = processShiftsForTimeline(shifts);

            return `
                <div class="timeline-day">
                    <div class="timeline-header">
                        ${formattedDate}
                    </div>
                    <div class="timeline-grid">
                        ${processedShifts.map(shift => generateShiftBlock(shift)).join('')}
                    </div>
                </div>
            `;
        }

        // シフトデータを時間情報付きで処理（修正版）
        function processShiftsForTimeline(shifts) {
            console.log('[DEBUG] processShiftsForTimeline - 入力シフト:', JSON.parse(JSON.stringify(shifts))); // 入力データをディープコピーしてログ
            const processedShifts = [];

            if (!Array.isArray(shifts)) { // shiftsが配列でない場合のガード
                console.error('[DEBUG] processShiftsForTimeline - 入力シフトが配列ではありません:', shifts);
                return processedShifts;
            }

            shifts.forEach(shift => {
                console.log('[DEBUG] processShiftsForTimeline - 処理中の個別シフト:', JSON.parse(JSON.stringify(shift)));

                if (shift.type === 'lecture' && shift.lectures && Array.isArray(shift.lectures)) {
                    shift.lectures.forEach(lectureNum => {
                        if (lectureDefinitions[lectureNum]) {
                            const lecture = lectureDefinitions[lectureNum];
                            processedShifts.push({
                                ...shift,
                                startTime: lecture.start,
                                endTime: lecture.end,
                                displayName: `${shift.user_name} (${lecture.name})`,
                                shiftType: 'lecture'
                            });
                        } else {
                            console.warn(`[DEBUG] processShiftsForTimeline - 講義定義が見つかりません: lectureNum=${lectureNum}`);
                        }
                    });
                } else if (shift.type === 'time' && shift.start_time && shift.end_time) {
                    processedShifts.push({
                        ...shift,
                        startTime: shift.start_time.substring(0, 5),
                        endTime: shift.end_time.substring(0, 5),
                        displayName: `${shift.user_name} (${shift.start_time.substring(0, 5)}-${shift.end_time.substring(0, 5)})`,
                        shiftType: 'time'
                    });
                } else {
                     console.warn('[DEBUG] processShiftsForTimeline - 未対応のシフトタイプまたはデータ不足:', JSON.parse(JSON.stringify(shift)));
                }
            });

            console.log('[DEBUG] processShiftsForTimeline - 出力（処理済みシフト）:', JSON.parse(JSON.stringify(processedShifts)));
            return processedShifts;
        }

        // シフトブロックを生成
        function generateShiftBlock(shift) {
            const startMinutes = timeToMinutes(shift.startTime);
            const endMinutes = timeToMinutes(shift.endTime);
            const duration = endMinutes - startMinutes;

            // 表示位置を計算（9:00を基準点とする）
            const baseMinutes = timelineConfig.startHour * 60;
            const topPosition = (startMinutes - baseMinutes) * timelineConfig.minuteHeight;
            const height = duration * timelineConfig.minuteHeight;

            // 重複チェック（簡易版）
            const overlapClass = checkForOverlap(shift) ? ' overlap' : '';

            return `
                <div class="shift-block ${shift.shiftType}${overlapClass}"
                     style="top: ${topPosition}px; height: ${height}px;"
                     title="${shift.displayName}">
                    <div class="font-semibold">${shift.user_name}</div>
                    <div class="text-xs">${shift.startTime} - ${shift.endTime}</div>
                    ${shift.shiftType === 'lecture' ? `<div class="text-xs opacity-75">講義</div>` : ''}
                </div>
            `;
        }

        // 時間文字列を分に変換
        function timeToMinutes(timeString) {
            const [hours, minutes] = timeString.split(':').map(Number);
            return hours * 60 + minutes;
        }

        // 重複チェック（簡易実装）
        function checkForOverlap(shift) {
            // 実際の実装では、同じ日の他のシフトとの重複をチェック
            // ここでは簡易的にfalseを返す
            return false;
        }

        // 日付フィルター機能（タイムライン用）
        function setupTimelineFilter(allDates, shiftsByDate) {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const filterButton = document.getElementById('filter_button');

            startDateInput.value = '';
            endDateInput.value = '';

            filterButton.addEventListener('click', function() {
                if (startDateInput.value && !endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    filterTimelineByDate(startDate, null, allDates, shiftsByDate);
                } else if (startDateInput.value && endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(endDateInput.value);
                    filterTimelineByDate(startDate, endDate, allDates, shiftsByDate);
                } else if (!startDateInput.value && !endDateInput.value) {
                    renderTimeline(allDates, shiftsByDate);
                } else {
                    alert('開始日を入力してください');
                }
            });
        }

        // 日付フィルター実行（タイムライン用）
        function filterTimelineByDate(startDate, endDate, allDates, shiftsByDate) {
            const filteredDates = allDates.filter(date => {
                const dateObj = new Date(date);
                if (endDate) {
                    return dateObj >= startDate && dateObj <= endDate;
                } else {
                    return dateObj >= startDate;
                }
            });

            renderTimeline(filteredDates, shiftsByDate);
        }
    </script>
</body>
</html>
