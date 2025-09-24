<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\CourseSession;
use Illuminate\Support\Facades\DB;

class CleanDuplicateCourseSessions extends Command
{
    /**
     * 命令簽名
     */
    protected $signature = 'course:clean-duplicates';

    /**
     * 命令描述
     */
    protected $description = '清理重複的課程排定資料';

    /**
     * 執行命令
     */
    public function handle()
    {
        $this->info('開始清理重複的課程排定資料...');

        // 找出重複的課程排定
        $duplicates = CourseSession::select('course_id', 'session_number', DB::raw('COUNT(*) as count'))
            ->groupBy('course_id', 'session_number')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('沒有發現重複的課程排定資料。');
            return;
        }

        $this->warn('發現 ' . $duplicates->count() . ' 個重複的課程排定：');

        foreach ($duplicates as $duplicate) {
            $course = Course::find($duplicate->course_id);
            $this->line("課程: {$course->name} (ID: {$duplicate->course_id}), 堂數: {$duplicate->session_number}, 重複: {$duplicate->count} 次");

            // 保留最新的，刪除舊的
            $sessions = CourseSession::where('course_id', $duplicate->course_id)
                ->where('session_number', $duplicate->session_number)
                ->orderBy('created_at', 'desc')
                ->get();

            // 刪除除了最新以外的所有記錄
            $sessions->skip(1)->each(function ($session) {
                $session->delete();
                $this->line("  刪除重複記錄 ID: {$session->id}");
            });
        }

        $this->info('重複資料清理完成！');
    }
}
