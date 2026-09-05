<?php

namespace Database\Seeders;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReadingPlanSeeder extends Seeder
{
    /**
     * 読書計画の動作確認用シードデータを投入する。
     *
     * 実行日にかかわらずリマインダーや期限切れ処理を確認できるよう、
     * 今日の日付を基準にtarget_dateを設定する。
     */
    public function run(): void
    {
        $today = Carbon::today();
        $books = Book::all();

        // 山田太郎：各ステータス・リマインダーの動作確認用
        $yamada = User::where('email', 'yamada@example.com')->first();

        $yamadaPlans = [
            // 3日後：期限前リマインダー対象
            [
                'target_date' => $today->copy()->addDays(3),
                'status' => ReadingPlanStatus::InProgress,
                'completed_at' => null,
            ],

            // 当日：期限当日リマインダー対象
            [
                'target_date' => $today->copy(),
                'status' => ReadingPlanStatus::InProgress,
                'completed_at' => null,
            ],

            // 3日前：期限切れ・期限超過リマインダー対象
            [
                'target_date' => $today->copy()->subDays(3),
                'status' => ReadingPlanStatus::InProgress,
                'completed_at' => null,
            ],

            // 7日後：リマインダー対象外
            [
                'target_date' => $today->copy()->addDays(7),
                'status' => ReadingPlanStatus::InProgress,
                'completed_at' => null,
            ],

            // 完了済み
            [
                'target_date' => $today->copy()->subDays(10),
                'status' => ReadingPlanStatus::Completed,
                'completed_at' => $today->copy()->subDays(5),
            ],
        ];

        foreach ($yamadaPlans as $i => $plan) {
            ReadingPlan::create([
                'user_id' => $yamada->id,
                'book_id' => $books[$i]->id,
                'target_date' => $plan['target_date'],
                'status' => $plan['status'],
                'completed_at' => $plan['completed_at'],
            ]);
        }

        // 鈴木花子：他ユーザーの読書計画にアクセスできないことを確認するためのデータ
        $suzuki = User::where('email', 'suzuki@example.com')->first();

        ReadingPlan::create([
            'user_id' => $suzuki->id,
            'book_id' => $books[5]->id,
            'target_date' => $today->copy()->addDays(5),
            'status' => ReadingPlanStatus::InProgress,
            'completed_at' => null,
        ]);
    }
}
