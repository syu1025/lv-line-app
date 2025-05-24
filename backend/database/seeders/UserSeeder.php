<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 環境変数から定義されたユーザー
        $predefinedUsers = [
            [
                'name' => env('USER_1_NAME'),
                'password' => env('USER_1_PASSWORD')
            ],
            [
                'name' => env('USER_2_NAME'),
                'password' => env('USER_2_PASSWORD')
            ],
            [
                'name' => env('USER_3_NAME'),
                'password' => env('USER_3_PASSWORD')
            ]
            // 必要に応じて追加のユーザーを定義
        ];

        foreach ($predefinedUsers as $userData) {
            // nameとpasswordが設定されている場合のみ処理
            if (!empty($userData['name']) && !empty($userData['password'])) {
                DB::table('users')->updateOrInsert(
                    ['name' => $userData['name']], // nameを一意の識別子として使用
                    [
                        'name' => $userData['name'],
                        // パスワードは必ずハッシュ化
                        'password' => Hash::make($userData['password']),
                        'remember_token' => Str::random(10),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }

        // 本番環境では初期管理者のみ作成し、パスワードリセットコマンドを実行
        if (app()->environment('production')) {
            $admin = User::create([
                'name' => 'admin',
                'password' => Hash::make(Str::random(32)), // 一時的なランダムパスワード
            ]);

            // パスワードリセットリンクを生成（本番環境のみ）
            $this->command->info('管理者ユーザーが作成されました。パスワードリセットを行ってください。');
        }
    }
}
