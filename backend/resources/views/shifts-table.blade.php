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
            <!-- 日付フィルター -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">シフト表</h2>
                <div class="date-filter">
                    <label for="start_date" class="text-sm">開始日:</label>
                    <input type="date" id="start_date" name="start_date" class="date-input">
                    <label for="end_date" class="text-sm">終了日:</label>
                    <input type="date" id="end_date" name="end_date" class="date-input">
                    <button id="filter_button" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">表示</button>
                </div>
            </div>

            <!-- シフト表コンテナ -->
            <div class="sync-scroll" id="table-container">
                <!-- 講義シフト表 -->
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-2">講義指定</h3>
                    <div class="hide-scrollbar">
                        <table id="lecture-table" class="border-collapse border border-gray-300 bg-white">
                            <!-- JavaScriptで動的生成 -->
                        </table>
                    </div>
                </div>

                <!-- 時間シフト表 -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">時間指定</h3>
                    <div class="overflow-x-auto">
                        <table id="time-table" class="border-collapse border border-gray-300 bg-white">
                            <!-- JavaScriptで動的生成 -->
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // ページロード時にAPIからデータを取得
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                // APIからシフトデータを取得
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

                // ローディングを非表示
                document.getElementById('loading').style.display = 'none';

                // メインコンテンツを表示
                document.getElementById('main-content').style.display = 'block';

                // シフト表を生成
                generateShiftTables(data.shifts);

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('loading').style.display = 'none';
                document.getElementById('error').style.display = 'block';
            }
        });

        // シフト表を生成する関数
        function generateShiftTables(shifts) {
            // 日付でグループ化
            const shiftsByDate = {};
            shifts.forEach(shift => {
                if (!shiftsByDate[shift.date]) {
                    shiftsByDate[shift.date] = [];
                }
                shiftsByDate[shift.date].push(shift);
            });

            const dates = Object.keys(shiftsByDate).sort();
            //console.log("dates:", dates);
            //console.log("shiftsByDate:", shiftsByDate);

            // 講義シフト表を生成
            generateLectureTable(dates, shiftsByDate);

            // 時間シフト表を生成
            generateTimeTable(dates, shiftsByDate);

            // スクロール連動を設定（テーブル生成後）
            setupScrollSync();

            // 日付フィルター機能を設定
            setupDateFilter(dates, shiftsByDate);
        }

        // 講義シフト表生成
        function generateLectureTable(dates, shiftsByDate) {
            const table = document.getElementById('lecture-table');

            // ヘッダー生成
            let headerHtml = '<thead><tr><th class="border border-gray-300 col-fixed sticky-col">講義</th>';
            dates.forEach(date => {
                const formattedDate = new Date(date).toLocaleDateString('ja-JP', {
                    month: 'numeric',
                    day: 'numeric',
                    weekday: 'short'
                });
                headerHtml += `<th class="border border-gray-300 col-date" data-date="${date}">${formattedDate}</th>`;
            });
            headerHtml += '</tr></thead>';

            // ボディ生成
            let bodyHtml = '<tbody>';
            [4, 5, 6, 7].forEach(lecture => {
                bodyHtml += `<tr><td class="border border-gray-300 sticky-col">${lecture}講</td>`;
                dates.forEach(date => {
                    const shiftsOnDate = shiftsByDate[date] || [];
                    const lectureShifts = shiftsOnDate.filter(shift =>
                        shift.type === 'lecture' &&
                        shift.lectures &&
                        shift.lectures.includes(lecture)
                    );
                    const userNames = lectureShifts.map(shift => shift.user_name).join(', ');
                    bodyHtml += `<td class="border border-gray-300 col-date" data-date="${date}">${userNames}</td>`;
                });
                bodyHtml += '</tr>';
            });
            bodyHtml += '</tbody>';

            table.innerHTML = headerHtml + bodyHtml;
        }

        // 時間シフト表生成
        function generateTimeTable(dates, shiftsByDate) {
            const table = document.getElementById('time-table');

            // ユーザー一覧を取得
            const users = [...new Set(
                Object.values(shiftsByDate)
                    .flat()
                    .map(shift => shift.user_name)
            )].sort();

            // ヘッダー生成
            let headerHtml = '<thead><tr><th class="border border-gray-300 col-fixed sticky-col">ユーザー</th>';
            dates.forEach(date => {
                const formattedDate = new Date(date).toLocaleDateString('ja-JP', {
                    month: 'numeric',
                    day: 'numeric',
                    weekday: 'short'
                });
                headerHtml += `<th class="border border-gray-300 col-date" data-date="${date}">${formattedDate}</th>`;
            });
            headerHtml += '</tr></thead>';

            // ボディ生成
            let bodyHtml = '<tbody>';
            users.forEach(user => {
                bodyHtml += `<tr><td class="border border-gray-300 sticky-col">${user}</td>`;
                dates.forEach(date => {
                    const shiftsOnDate = shiftsByDate[date] || [];
                    const userTimeShift = shiftsOnDate.find(shift =>
                        shift.user_name === user && shift.type === 'time'
                    );
                    let timeText = '';

                    if (userTimeShift) {
                        const startTime = userTimeShift.start_time ? userTimeShift.start_time.substring(11, 16) : '';
                        const endTime = userTimeShift.end_time ? userTimeShift.end_time.substring(11, 16) : '';
                        timeText = `${startTime} ~ ${endTime}`;
                        console.log("userTimeShift:start_time" + userTimeShift.start_time)
                        console.log("userTimeShift:end_time" + userTimeShift.end_time)
                        console.log("startTime:" + startTime)
                        console.log("endTime:" + endTime)
                    }
                    bodyHtml += `<td class="border border-gray-300 col-date" data-date="${date}">${timeText}</td>`;
                });
                bodyHtml += '</tr>';
            });
            bodyHtml += '</tbody>';

            table.innerHTML = headerHtml + bodyHtml;
        }

        // テーブルスクロール連動機能
        function setupScrollSync() {
            const lectureTableContainer = document.querySelector('#lecture-table').closest('.hide-scrollbar');
            const timeTableContainer = document.querySelector('#time-table').closest('.overflow-x-auto');

            let isScrolling = false;

            // 講義テーブルのスクロールイベント
            lectureTableContainer.addEventListener('scroll', function() {
                if (isScrolling) return;
                isScrolling = true;

                // 時間テーブルのスクロール位置を同期
                timeTableContainer.scrollLeft = this.scrollLeft;

                // 次のフレームでフラグをリセット
                requestAnimationFrame(() => {
                    isScrolling = false;
                });
            });

            // 時間テーブルのスクロールイベント
            timeTableContainer.addEventListener('scroll', function() {
                if (isScrolling) return;
                isScrolling = true;

                // 講義テーブルのスクロール位置を同期
                lectureTableContainer.scrollLeft = this.scrollLeft;

                // 次のフレームでフラグをリセット
                requestAnimationFrame(() => {
                    isScrolling = false;
                });
            });
        }

        // 日付フィルター機能
        function setupDateFilter(allDates, shiftsByDate) {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const filterButton = document.getElementById('filter_button');

            // 日付選択の初期値を空に設定
            startDateInput.value = '';
            endDateInput.value = '';

            // フィルターボタンのクリックイベント
            filterButton.addEventListener('click', function() {
                if (startDateInput.value && !endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    filterByDate(startDate, null, allDates, shiftsByDate);
                } else if (startDateInput.value && endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(endDateInput.value);
                    filterByDate(startDate, endDate, allDates, shiftsByDate);
                } else if (!startDateInput.value && !endDateInput.value) {
                    // 全て表示
                    generateLectureTable(allDates, shiftsByDate);
                    generateTimeTable(allDates, shiftsByDate);
                    setupScrollSync(); // 再設定
                } else {
                    alert('開始日を入力してください');
                }
            });
        }

        // 日付フィルター実行
        function filterByDate(startDate, endDate, allDates, shiftsByDate) {
            const filteredDates = allDates.filter(date => {
                const dateObj = new Date(date);
                if (endDate) {
                    return dateObj >= startDate && dateObj <= endDate;
                } else {
                    return dateObj >= startDate;
                }
            });

            generateLectureTable(filteredDates, shiftsByDate);
            generateTimeTable(filteredDates, shiftsByDate);
            setupScrollSync(); // 再設定
        }
    </script>
</body>
</html>
