<?php

namespace App\Console\Commands;

use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use App\Notifications\PlanReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class RunReadingPlanDailyBatch extends Command
{
    /**
     * @var string
     */
    protected $signature = 'reading-plans:send-reminders';

    /**
     * @var string
     */
    protected $description = '読書計画の日次バッチ：期日経過したin_progressを一括Expired化し、3日前/当日/3日後の各タイミングでリマインダー通知を送信する。';

    public function handle(): int
    {
        $today = Carbon::today();

        // 期日経過したin_progressを一括Expired化
        ReadingPlan::query()
            ->where('status', ReadingPlanStatus::InProgress)
            ->whereDate('target_date', '<', $today)
            ->update([
                'status' => ReadingPlanStatus::Expired,
                'updated_at' => now(),
            ]);

        // 期日3日前のin_progress計画にリマインダー（予告）送信
        $this->notify(
            ReadingPlan::query()
                ->with(['user', 'book'])
                ->where('status', ReadingPlanStatus::InProgress)
                ->whereDate('target_date', $today->copy()->addDays(3))
                ->get(),
            PlanReminderNotification::TIMING_THREE_DAYS_BEFORE,
        );

        // 期日当日のin_progressにリマインダー（最終リマインド）送信
        $this->notify(
            ReadingPlan::query()
                ->with(['user', 'book'])
                ->where('status', ReadingPlanStatus::InProgress)
                ->whereDate('target_date', $today)
                ->get(),
            PlanReminderNotification::TIMING_ON_DUE_DATE,
        );

        // 期日3日後のExpired計画にリマインダー（再エンゲージメント）送信
        $this->notify(
            ReadingPlan::query()
                ->with(['user', 'book'])
                ->where('status', ReadingPlanStatus::Expired)
                ->whereDate('target_date', $today->copy()->subDays(3))
                ->get(),
            PlanReminderNotification::TIMING_THREE_DAYS_AFTER,
        );

        return self::SUCCESS;
    }

    /**
     * 対象計画群に通知を送信する
     *
     * @param  Collection<int, ReadingPlan>  $plans
     */
    private function notify(Collection $plans, string $timing): void
    {
        $plans->each(function (ReadingPlan $plan) use ($timing): void {
            $plan->user->notify(new PlanReminderNotification($plan, $timing));
        });
    }
}
