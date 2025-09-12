<?php

namespace App\Console\Commands;

use App\Models\SchoolEvent;
use App\Models\Course;
use Illuminate\Console\Command;

class CleanEventTitles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:event-titles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清理事件和課程標題中的校區名稱';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('開始清理事件標題...');

        // 清理校務活動標題
        $events = SchoolEvent::where('title', 'like', '% - %')->get();
        $this->info("找到 {$events->count()} 個包含校區名稱的校務活動");

        foreach ($events as $event) {
            $parts = explode(' - ', $event->title, 2);
            if (count($parts) == 2) {
                $oldTitle = $event->title;
                $event->title = $parts[1];
                $event->save();
                $this->line("校務活動 ID {$event->id}: '{$oldTitle}' -> '{$event->title}'");
            }
        }

        // 清理課程標題
        $courses = Course::where('name', 'like', '% - %')->get();
        $this->info("找到 {$courses->count()} 個包含校區名稱的課程");

        foreach ($courses as $course) {
            $parts = explode(' - ', $course->name, 2);
            if (count($parts) == 2) {
                $oldName = $course->name;
                $course->name = $parts[1];
                $course->save();
                $this->line("課程 ID {$course->id}: '{$oldName}' -> '{$course->name}'");
            }
        }

        $this->info('清理完成！');
    }
}
