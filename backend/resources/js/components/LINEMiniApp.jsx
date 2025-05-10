import React, { useState, useEffect } from 'react';
import { Calendar, Check, Clock, X, History, Home } from 'lucide-react';
import axios from 'axios';

// axiosのデフォルト設定
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

const LINEMiniApp = () => {
  const [selectedDates, setSelectedDates] = useState([]);
  const [currentMonth, setCurrentMonth] = useState(new Date());
  const [showDatePicker, setShowDatePicker] = useState(true);
  const [shifts, setShifts] = useState({});
  const [shiftType, setShiftType] = useState({});
  const [confirmationMode, setConfirmationMode] = useState(false);

  // 曜日の表示
  const weekdays = ['日', '月', '火', '水', '木', '金', '土'];

  // 今月のカレンダーデータを生成
  const getDaysInMonth = (year, month) => {
    const days = [];
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);

    // 前月の日を埋める
    const firstDayOfWeek = firstDay.getDay();
        for (let i = firstDayOfWeek; i > 0; i--) {
        const prevDate = new Date(year, month, 1 - i);
        days.push({ date: prevDate, currentMonth: false });
        }

    // 当月の日を埋める
    for (let i = 1; i <= lastDay.getDate(); i++) {
      const date = new Date(year, month, i);
      days.push({ date, currentMonth: true });
    }

    return days;
  };
  //ここで実際に日付を取得、上の関数を引数ありで呼び出している
  const days = getDaysInMonth(
    currentMonth.getFullYear(),
    currentMonth.getMonth()
  );


  // 日付を選択
  const toggleDateSelection = (date) => {
    const dateStr = date.toLocaleDateString('sv-SE'); // YYYY-MM-DD形式で日本時間の日付を取得
    console.log('日付文字列:', dateStr);
    if (selectedDates.includes(dateStr)) {
      setSelectedDates(selectedDates.filter(d => d !== dateStr));
      // 選択解除された日付のシフト情報も削除
      const newShifts = {...shifts};
      delete newShifts[dateStr];
      setShifts(newShifts);

      const newShiftType = {...shiftType};
      delete newShiftType[dateStr];
      setShiftType(newShiftType);
    } else {
      // 新しい日付を追加して日付順にソート
      const newSelectedDates = [...selectedDates, dateStr].sort((a, b) => {
        // YYYY-MM-DD形式の文字列を比較すると、自然と日付順になる
        return a.localeCompare(b);
      });
      setSelectedDates(newSelectedDates);
    }
  };

  // 月を変更
  const changeMonth = (increment) => {
    const newMonth = new Date(currentMonth);
    newMonth.setMonth(newMonth.getMonth() + increment);
    setCurrentMonth(newMonth);
  };

  // シフトタイプを切り替え（時間帯 or 講数）
  const toggleShiftType = (dateStr) => {
    setShiftType({
      ...shiftType,
      [dateStr]: shiftType[dateStr] === 'lecture' ? 'time' : 'lecture'
    });
  };

  // シフト情報を更新
  const updateShift = (dateStr, value) => {
    setShifts({
      ...shifts,
      [dateStr]: value
    });
  };

  // フォーマットした日付を取得
  const formatDate = (dateStr) => {
    // 'YYYY-MM-DD'を分割してDateを生成
    const [year, month, day] = dateStr.split('-').map(Number);
    const date = new Date(year, month - 1, day);
    return `${date.getMonth() + 1}/${date.getDate()}(${weekdays[date.getDay()]})`;
  };

  // シフト登録を確定
  const confirmShifts = () => {
    setConfirmationMode(true);
  };

  // 登録をキャンセル
  const cancelConfirmation = () => {
    setConfirmationMode(false);
  };

  // 登録を完了
  const submitShifts = async () => {
    try {
      console.log('送信するデータ:', {
        shifts: selectedDates.map(dateStr => ({
          date: dateStr,
          type: shiftType[dateStr] || 'time',
          start_time: shifts[dateStr]?.startTime,
          end_time: shifts[dateStr]?.endTime,
          lectures: shifts[dateStr]?.lectures
        }))
      });

      const response = await axios.post('/api/shifts', {
        shifts: selectedDates.map(dateStr => ({
          date: dateStr,
          type: shiftType[dateStr] || 'time',
          start_time: shifts[dateStr]?.startTime,
          end_time: shifts[dateStr]?.endTime,
          lectures: shifts[dateStr]?.lectures
        }))
      });

      console.log('レスポンス:', response);

      if (response.status === 200) {
        alert('シフトが登録されました！');
        setSelectedDates([]);
        setShifts({});
        setShiftType({});
        setConfirmationMode(false);
      }
    } catch (error) {
      console.error('シフト登録に失敗しました:', error);
      if (error.response) {
        // サーバーからのエラーレスポンスがある場合
        console.error('エラーレスポンス:', error.response.data);
        alert(`シフト登録に失敗しました: ${error.response.data.message || 'エラーが発生しました'}`);
      } else if (error.request) {
        // リクエストは送信されたがレスポンスがない場合
        console.error('リクエストエラー:', error.request);
        alert('サーバーとの通信に失敗しました。ネットワーク接続を確認してください。');
      } else {
        // リクエストの設定中にエラーが発生した場合
        console.error('エラー:', error.message);
        alert('シフト登録に失敗しました。もう一度お試しください。');
      }
    }
  };

  return (
    <div className="flex flex-col h-screen bg-gray-100">
      {/* ヘッダー */}
      <div className="bg-green-500 text-white p-4 flex justify-between items-center">
        <h1 className="text-xl font-bold">シフト登録</h1>
        {!confirmationMode && (
          <button
            onClick={() => setShowDatePicker(!showDatePicker)}
            className="flex items-center text-sm bg-white text-green-500 px-3 py-1 rounded-full"
          >
            <Calendar size={16} className="mr-1" />
            {showDatePicker ? '閉じる' : '日付選択'}
          </button>
        )}
      </div>

      {/* カレンダー */}
      {showDatePicker && !confirmationMode && (
        <div className="p-4 bg-white shadow-md">
          <div className="flex justify-between items-center mb-4">
            <button
              onClick={() => changeMonth(-1)}
              className="text-gray-500"
            >
              ＜ 前月
            </button>
            <h2 className="font-bold">
              {currentMonth.getFullYear()}年{currentMonth.getMonth() + 1}月
            </h2>
            <button
              onClick={() => changeMonth(1)}
              className="text-gray-500"
            >
              次月 ＞
            </button>
          </div>

          <div className="grid grid-cols-7 gap-1 mb-2">
            {weekdays.map(day => (
              <div key={day} className="text-center font-medium text-sm">
                {day}
              </div>
            ))}
          </div>

          <div className="grid grid-cols-7 gap-1">
            {days.map((day, index) => (
              <div
                key={index}
                onClick={() => day.currentMonth && toggleDateSelection(day.date)}
                className={`
                  p-2 text-center rounded-md text-sm
                  ${!day.currentMonth ? 'text-gray-300' :
                    selectedDates.includes(day.date.toLocaleDateString('sv-SE'))
                      ? 'bg-green-500 text-white'
                      : 'hover:bg-gray-100'
                  }
                `}
              >
                {day.date.getDate()}
              </div>
            ))}
          </div>

          {/* 選択した日付を表示（デバッグ用）
          <div className="mt-4 p-2 bg-gray-100 rounded">
            <h3 className="font-bold text-gray-700">選択中の日付:</h3>
            {selectedDates.map(dateStr => (
              <h1 key={dateStr} className="text-red-500">dateStr: {dateStr}</h1>
            ))}
          </div>
          */}
        </div>
      )}

      {/* 選択された日付のシフト設定 */}
      {!confirmationMode ? (
        <div className="flex-1 overflow-auto p-4">
          {/* デバッグ表示（常に表示）
          <div className="mb-4 p-3 bg-yellow-100 border border-yellow-400 rounded">
            <h3 className="font-bold text-gray-700">デバッグ情報:</h3>
            <p>showDatePicker: {showDatePicker ? 'true' : 'false'}</p>
            <p>selectedDates.length: {selectedDates.length}</p>
            {selectedDates.map(dateStr => (
              <h1 key={dateStr} className="text-red-500">選択日: {dateStr}</h1>
            ))}
          </div>
          */}

          {selectedDates.length === 0 ? (
            <div className="text-center text-gray-500 mt-8">
              <Calendar size={48} className="mx-auto mb-4 text-gray-300" />
              <p>カレンダーから日付を選択してください</p>
            </div>
          ) : (
            <>
              {selectedDates.map(dateStr => (
                <div key={dateStr} className="bg-white p-4 rounded-lg shadow mb-4">
                  <div className="flex justify-between items-center mb-3">
                    <h3 className="font-bold">{formatDate(dateStr)}</h3>
                    <button
                      onClick={() => toggleShiftType(dateStr)}
                      className="text-xs bg-gray-100 px-2 py-1 rounded"
                    >
                      {!shiftType[dateStr] || shiftType[dateStr] === 'time' ? '時間指定' : '講数指定'} ▼
                    </button>
                  </div>

                  {/* 時間指定のUI */}
                  {(!shiftType[dateStr] || shiftType[dateStr] === 'time') && (
                    <div className="flex items-center">
                      <Clock size={16} className="text-gray-400 mr-2" />
                      <select
                        className="bg-gray-50 border border-gray-200 rounded px-2 py-1 mr-2"
                        value={shifts[dateStr]?.startTime || ''}
                        onChange={(e) => updateShift(dateStr, {...shifts[dateStr], startTime: e.target.value})}
                      >
                        <option value="">開始時間</option>
                        {Array.from({length: 28}, (_, i) => {
                          const hour = Math.floor(i / 2) + 9;
                          const minute = i % 2 === 0 ? '00' : '30';
                          return `${hour}:${minute}`;
                        }).map(time => (
                          <option key={time} value={time}>{time}</option>
                        ))}
                      </select>
                      <span className="mx-1">〜</span>
                      <select
                        className="bg-gray-50 border border-gray-200 rounded px-2 py-1"
                        value={shifts[dateStr]?.endTime || ''}
                        onChange={(e) => updateShift(dateStr, {...shifts[dateStr], endTime: e.target.value})}
                      >
                        <option value="">終了時間</option>
                        {Array.from({length: 28}, (_, i) => {
                          const hour = Math.floor(i / 2) + 9;
                          const minute = i % 2 === 0 ? '30' : '00';
                          const displayHour = minute === '30' ? hour : hour + 1;
                          return `${displayHour}:${minute}`;
                        }).map(time => (
                          <option key={time} value={time}>{time}</option>
                        ))}
                      </select>
                    </div>
                  )}

                  {/* 講数指定のUI（複数選択可能） */}
                  {shiftType[dateStr] === 'lecture' && (
                    <div className="flex flex-wrap gap-2">
                      {Array.from({length: 7}, (_, i) => i + 1).map(lecture => (
                        <button
                          key={lecture}
                          onClick={() => {
                            const currentLectures = shifts[dateStr]?.lectures || [];
                            let newLectures;

                            if (currentLectures.includes(lecture)) {
                              // 既に選択されている場合は削除
                              newLectures = currentLectures.filter(l => l !== lecture);
                            } else {
                              // 選択されていない場合は追加
                              newLectures = [...currentLectures, lecture].sort((a, b) => a - b);
                            }

                            updateShift(dateStr, {lectures: newLectures});
                          }}
                          className={`px-3 py-1 rounded text-sm ${
                            shifts[dateStr]?.lectures?.includes(lecture)
                              ? 'bg-green-500 text-white'
                              : 'bg-gray-100'
                          }`}
                        >
                          {lecture}講
                        </button>
                      ))}
                    </div>
                  )}
                </div>
              ))}

              {selectedDates.length > 0 && (
                <button
                  onClick={confirmShifts}
                  className="bg-green-500 text-white py-3 px-6 rounded-lg w-full mt-4"
                >
                  確認する
                </button>
              )}
            </>
          )}
        </div>
      ) : (
        /* 確認画面 */
        <div className="flex-1 overflow-auto p-4">
          <h2 className="text-lg font-bold mb-4">シフト確認</h2>

          <div className="bg-white p-4 rounded-lg shadow mb-4">
            {selectedDates.map(dateStr => (
              <div key={dateStr} className="border-b py-3 last:border-b-0">
                <p className="font-bold">{formatDate(dateStr)}</p>
                <p className="text-gray-600 ml-4">
                  {shiftType[dateStr] === 'lecture'
                    ? shifts[dateStr]?.lectures?.length > 0
                      ? shifts[dateStr].lectures.map(l => `${l}講`).join('、')
                      : '未選択'
                    : `${shifts[dateStr]?.startTime || ''} 〜 ${shifts[dateStr]?.endTime || ''}`
                  }
                </p>
              </div>
            ))}
          </div>

          <div className="flex gap-3 mt-4">
            <button
              onClick={cancelConfirmation}
              className="flex-1 bg-gray-200 py-3 rounded-lg"
            >
              修正する
            </button>
            <button
              onClick={submitShifts}
              className="flex-1 bg-green-500 text-white py-3 rounded-lg"
            >
              LINEで提出する
            </button>
          </div>
        </div>
      )}

      {/* フッター */}
      <div className="bg-gray-50 p-4 border-t border-gray-200">
        <div className="text-center text-xs text-gray-400">
          シフト登録システム v1.0
        </div>
      </div>
    </div>
  );
};

export default LINEMiniApp;
