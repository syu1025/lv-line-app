<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>シフト表</title>
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
        <h1 class="text-lg font-semibold text-gray-800">シフト管理システム</h1>
        <div class="flex items-center space-x-2">
            <a href="/" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
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
        <main>
            <!-- 共通スクロールコンテナ -->
            <div class="sync-scroll" id="table-container">
                <!-- 講義シフト表 -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2 sm:mb-4">
                        <h2 class="text-lg sm:text-xl font-semibold">講義指定</h2>
                        <div class="date-filter">
                            <label for="start_date" class="text-sm">開始日:</label>
                            <input type="date" id="start_date" name="start_date" class="date-input">
                            <label for="end_date" class="text-sm">終了日:</label>
                            <input type="date" id="end_date" name="end_date" class="date-input">
                            <button id="filter_button" class="filter-button">表示</button>
                        </div>
                    </div>
                    <div class="hide-scrollbar">
                        <table class="border-collapse border border-gray-300 bg-white">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 col-fixed sticky-col">講義</th>
                                    @php
                                        // シフトデータからユニークな日付を抽出
                                        $dates = $shifts_date->keys()->toArray();
                                        // 現在の日付を取得（表示用）
                                        $today = \Carbon\Carbon::today();

                                        // フィルタリングを削除し、すべての日付を表示
                                        $filteredDates = $dates;
                                    @endphp

                                    @foreach ($filteredDates as $date)
                                        <th class="border border-gray-300 col-date" data-date="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('n/j') }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([4, 5, 6, 7] as $lecture)
                                    <tr>
                                        <td class="border border-gray-300 sticky-col">{{ $lecture }}講</td>
                                        @foreach ($filteredDates as $date)
                                            <td id="lecture-{{ $lecture }}-{{ $loop->index }}" class="border border-gray-300 col-date" data-date="{{ $date }}">
                                                @if (isset($shifts_date[$date]))
                                                    @foreach ($shifts_date[$date] as $shift)
                                                        @if ($shift->type === 'lecture' && is_array($shift->lectures) && in_array($lecture, $shift->lectures))
                                                            {{ $shift->line_user_id }}
                                                            @if (!$loop->last), @endif
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 時間シフト表 -->
                <div>
                    <h2 class="text-lg sm:text-xl font-semibold mb-2 sm:mb-4">時間指定</h2>
                    <div class="overflow-x-auto">
                        <table class="border-collapse border border-gray-300 bg-white">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 col-fixed sticky-col">ユーザー</th>
                                    @foreach ($filteredDates as $date)
                                        <th class="border border-gray-300 col-date" data-date="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('n/j') }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // 時間帯シフトをユーザーごとにグループ化
                                    $userIds = [];
                                    foreach ($shifts_date as $date => $shifts) {
                                        if (in_array($date, $filteredDates)) {
                                            foreach ($shifts as $shift) {
                                                if (!in_array($shift->line_user_id, $userIds)) {
                                                    $userIds[] = $shift->line_user_id;
                                                }
                                            }
                                        }
                                    }
                                    sort($userIds);
                                @endphp

                                @foreach ($userIds as $userId)
                                    <tr>
                                        <td class="border border-gray-300 sticky-col">{{ $userId }}</td>
                                        @foreach ($filteredDates as $date)
                                            <td class="border border-gray-300 col-date" data-date="{{ $date }}">
                                                @if (isset($shifts_date[$date]))
                                                    @foreach ($shifts_date[$date] as $shift)
                                                        @if ($shift->line_user_id === $userId && $shift->type === 'time')
                                                            @php
                                                                $startTime = $shift->start_time ? \Carbon\Carbon::parse($shift->start_time)->format('H:i') : '';
                                                                $endTime = $shift->end_time ? \Carbon\Carbon::parse($shift->end_time)->format('H:i') : '';
                                                            @endphp
                                                            {{ $startTime }} ~ {{ $endTime }}
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-4 mt-8 text-center text-gray-500 text-sm">
            <p>© {{ date('Y') }} シフト管理システム</p>
        </footer>
    </div>

    <script>
        // スクロールを同期させる
        document.addEventListener('DOMContentLoaded', function() {
            const tableContainer = document.getElementById('table-container');
            const tables = tableContainer.querySelectorAll('.hide-scrollbar, .overflow-x-auto');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const filterButton = document.getElementById('filter_button');

            // 現在の日付を取得
            const today = new Date();
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            // 日付選択の初期値を空に設定（すべての日付を表示）
            startDateInput.value = '';
            endDateInput.value = '';

            // フィルターボタンのクリックイベント
            filterButton.addEventListener('click', function() {
                // 開始日だけが指定されている場合は、その日以降をすべて表示
                if (startDateInput.value && !endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    filterFutureDate(startDate);
                }
                // 両方指定されている場合は範囲指定
                else if (startDateInput.value && endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(endDateInput.value);
                    filterTables(startDate, endDate);
                }
                // 両方空の場合はすべて表示
                else if (!startDateInput.value && !endDateInput.value) {
                    showAllDates();
                }
                // 終了日だけの場合はエラー
                else {
                    alert('開始日を入力してください');
                }
            });

            // すべての日付を表示する関数
            function showAllDates() {
                const allDateColumns = document.querySelectorAll('th[data-date], td[data-date]');
                allDateColumns.forEach(column => {
                    column.style.display = '';
                });
                adjustTableWidths();
            }

            // 指定した日付以降のみ表示する関数
            function filterFutureDate(startDate) {
                const allDateColumns = document.querySelectorAll('th[data-date], td[data-date]');

                allDateColumns.forEach(column => {
                    const dateStr = column.getAttribute('data-date');
                    const columnDate = new Date(dateStr);

                    // startDate以降かどうかを判定
                    if (columnDate >= startDate) {
                        column.style.display = '';
                    } else {
                        column.style.display = 'none';
                    }
                });

                // テーブル幅を再調整
                adjustTableWidths();
            }

            // テーブルのフィルタリング関数
            function filterTables(startDate, endDate) {
                const allDateColumns = document.querySelectorAll('th[data-date], td[data-date]');

                allDateColumns.forEach(column => {
                    const dateStr = column.getAttribute('data-date');
                    const columnDate = new Date(dateStr);

                    // 指定した期間内かどうかを判定
                    if (columnDate >= startDate && columnDate <= endDate) {
                        column.style.display = '';
                    } else {
                        column.style.display = 'none';
                    }
                });

                // テーブル幅を再調整
                adjustTableWidths();
            }

            tables.forEach(table => {
                table.addEventListener('scroll', function() {
                    const scrollLeft = this.scrollLeft;
                    tables.forEach(otherTable => {
                        if (otherTable !== this) {
                            otherTable.scrollLeft = scrollLeft;
                        }
                    });
                });
            });

            // 初期表示時にテーブル幅を調整
            function adjustTableWidths() {
                const tableWidth = Math.max(
                    document.documentElement.clientWidth,
                    window.innerWidth || 0
                );
                const tables = document.querySelectorAll('table');

                tables.forEach(table => {
                    // 表示されている日付の列数を取得
                    const dateColumns = table.querySelectorAll('th:not(.sticky-col):not([style*="display: none"])').length;
                    // 使用可能な幅から固定列の幅を引く
                    const availableWidth = tableWidth - 120; // 100px for fixed column + margins
                    // 各日付列に割り当てる幅（最小値として100pxを設定）
                    const columnWidth = Math.max(100, Math.floor(availableWidth / dateColumns));

                    // 日付列のスタイルを更新
                    const dateColNodes = table.querySelectorAll('.col-date:not([style*="display: none"])');
                    dateColNodes.forEach(col => {
                        col.style.width = `${columnWidth}px`;
                        col.style.minWidth = `${columnWidth}px`;
                    });
                });
            }

            // 初回実行（全ての日付を表示）
            showAllDates();

            // ウィンドウサイズ変更時に再計算
            window.addEventListener('resize', adjustTableWidths);
        });
    </script>
</body>
</html>
