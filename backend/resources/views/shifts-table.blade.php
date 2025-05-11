<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>シフト表</title>
    <!-- Tailwind CSSを使用 -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8 px-4">
        <header class="mb-8">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">シフト表</h1>
                <a href="/" class="bg-green-500 text-white px-4 py-2 rounded">シフト登録へ戻る</a>
            </div>
        </header>

        <main>
            <!-- 講義シフト表 -->
            <div class="mb-10">
                <h2 class="text-xl font-semibold mb-4"></h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">講義</th>
                                <th id="lecture-date-0" class="border border-gray-300 px-4 py-2">5/11</th>
                                <th id="lecture-date-1" class="border border-gray-300 px-4 py-2">5/12</th>
                                <th id="lecture-date-2" class="border border-gray-300 px-4 py-2"></th>
                                <th id="lecture-date-3" class="border border-gray-300 px-4 py-2"></th>
                                <th id="lecture-date-4" class="border border-gray-300 px-4 py-2"></th>
                                <th id="lecture-date-5" class="border border-gray-300 px-4 py-2"></th>                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">4講</td>
                                <td id="lecture-4-0" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-4-1" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-4-2" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-4-3" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-4-4" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-4-5" class="border border-gray-300 px-4 py-2"></td>                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">5講</td>
                                <td id="lecture-5-0" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-5-1" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-5-2" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-5-3" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-5-4" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-5-5" class="border border-gray-300 px-4 py-2"></td>                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">6講</td>
                                <td id="lecture-6-0" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-6-1" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-6-2" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-6-3" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-6-4" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-6-5" class="border border-gray-300 px-4 py-2"></td>                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">7講</td>
                                <td id="lecture-7-0" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-7-1" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-7-2" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-7-3" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-7-4" class="border border-gray-300 px-4 py-2"></td>
                                <td id="lecture-7-5" class="border border-gray-300 px-4 py-2"></td>                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 時間シフト表 -->
            <div>
                <h2 class="text-xl font-semibold mb-4"></h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">時間</th>
                                <th id="time-date-0" class="border border-gray-300 px-4 py-2">5/11</th>
                                <th id="time-date-1" class="border border-gray-300 px-4 py-2">5/12</th>
                                <th id="time-date-2" class="border border-gray-300 px-4 py-2"></th>
                                <th id="time-date-3" class="border border-gray-300 px-4 py-2"></th>
                                <th id="time-date-4" class="border border-gray-300 px-4 py-2"></th>
                                <th id="time-date-5" class="border border-gray-300 px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody id="time-shifts-body">
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">user_name</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">user_name_test</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                                <td class="border border-gray-300 px-4 py-2">15:00 ~ 20:00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // ページ読み込み時にシフトデータを取得
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                // APIからシフトデータを取得
                const response = await fetch('/api/shifts-table');
                const data = await response.json();

                // データの表示
                displayShiftTable(data);
            } catch (error) {
                console.error('シフトデータの取得に失敗しました:', error);
            }
        });

        // シフト表を表示する関数
        function displayShiftTable(data) {
            const { dates, lecture_shifts, time_shifts } = data;

            // 日付ヘッダーを設定
            dates.forEach((date, index) => {
                document.getElementById(`lecture-date-${index}`).textContent = date.display;
                document.getElementById(`time-date-${index}`).textContent = date.display;
            });

            // 講義シフトのデータを設定
            for (let lecture = 4; lecture <= 7; lecture++) {
                dates.forEach((date, index) => {
                    const cell = document.getElementById(`lecture-${lecture}-${index}`);
                    const users = lecture_shifts[lecture]?.[date.date] || [];
                    cell.textContent = users.join(', ');
                });
            }

            // 時間シフトのデータを設定
            const timeShiftsBody = document.getElementById('time-shifts-body');
            timeShiftsBody.innerHTML = ''; // 既存のコンテンツをクリア

            // ユーザーと時間を集計
            const userTimeRows = {};

            dates.forEach((date) => {
                const shiftsForDate = time_shifts[date.date] || [];

                shiftsForDate.forEach(shift => {
                    const userId = shift.user;
                    if (!userTimeRows[userId]) {
                        userTimeRows[userId] = {
                            user: userId,
                            times: {}
                        };
                    }

                    userTimeRows[userId].times[date.date] = `${shift.start_time} ~ ${shift.end_time}`;
                });
            });

            // 行として追加
            Object.values(userTimeRows).forEach(row => {
                const tr = document.createElement('tr');

                // ユーザー名のセル
                const userCell = document.createElement('td');
                userCell.className = 'border border-gray-300 px-4 py-2';
                userCell.textContent = row.user;
                tr.appendChild(userCell);

                // 各日の時間
                dates.forEach(date => {
                    const timeCell = document.createElement('td');
                    timeCell.className = 'border border-gray-300 px-4 py-2';
                    timeCell.textContent = row.times[date.date] || '';
                    tr.appendChild(timeCell);
                });

                timeShiftsBody.appendChild(tr);
            });
        }
    </script>
</body>
</html>
