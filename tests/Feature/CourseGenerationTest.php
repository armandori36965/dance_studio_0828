<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Campus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 建立測試校區
        Campus::create([
            'name' => '測試校區',
            'address' => '測試地址',
            'phone' => '02-12345678',
            'color' => '#FF0000',
            'sort_order' => 1,
        ]);
    }

    /**
     * 測試週期課程排定生成功能
     */
    public function test_weekly_course_session_generation()
    {
        $course = Course::create([
            'name' => '測試週期課程',
            'description' => '用於測試',
            'price' => 1000,
            'pricing_type' => 'per_session',
            'start_time' => '2025-10-01 09:00:00', // 星期三
            'end_time' => '2025-10-01 10:00:00',
            'campus_id' => 1,
            'level' => 'beginner',
            'is_active' => true,
            'is_weekly_course' => true,
            'total_sessions' => 5,
            'weekdays' => ['1', '3'], // 星期一、三
            'avoid_school_events' => false,
        ]);

        $sessions = $course->sessions;

        // 驗證生成了正確數量的排定
        $this->assertEquals(5, $sessions->count());

        // 驗證第一個排定從下個匹配日開始（10/1 是星期三，下個星期一是 10/6）
        $firstSession = $sessions->first();
        $this->assertEquals('2025-10-06 09:00:00', $firstSession->start_time->format('Y-m-d H:i:s'));
        $this->assertEquals('scheduled', $firstSession->status);
    }

    /**
     * 測試單次課程排定生成功能
     */
    public function test_single_course_session_generation()
    {
        $course = Course::create([
            'name' => '測試單次課程',
            'description' => '用於測試',
            'price' => 500,
            'pricing_type' => 'per_session',
            'start_time' => '2025-10-01 14:00:00',
            'end_time' => '2025-10-01 15:00:00',
            'campus_id' => 1,
            'level' => 'beginner',
            'is_active' => true,
            'is_weekly_course' => false, // 單次課程
        ]);

        $sessions = $course->sessions;

        // 驗證只生成了1個排定
        $this->assertEquals(1, $sessions->count());

        $session = $sessions->first();
        $this->assertEquals('2025-10-01 14:00:00', $session->start_time->format('Y-m-d H:i:s'));
        $this->assertEquals('2025-10-01 15:00:00', $session->end_time->format('Y-m-d H:i:s'));
    }

    /**
     * 測試價格計算功能
     */
    public function test_pricing_calculation()
    {
        $course = Course::create([
            'name' => '價格測試課程',
            'price' => 800,
            'pricing_type' => 'per_session',
            'start_time' => '2025-10-01 09:00:00',
            'campus_id' => 1,
            'is_weekly_course' => true,
            'total_sessions' => 4,
            'weekdays' => ['1'],
        ]);

        // 測試 per_session 價格計算
        $this->assertEquals(3200, $course->getTotalPrice()); // 800 * 4

        // 切換到 per_student 模式
        $course->update(['pricing_type' => 'per_student']);
        $this->assertEquals(0, $course->getTotalPrice()); // 還沒有學員

        // 模擬添加學員（需要 user_course 表，但這裡簡化測試）
        // 在實際測試中，需要建立用戶並關聯
    }

    /**
     * 測試重複生成排定的防護機制
     */
    public function test_prevent_duplicate_session_generation()
    {
        $course = Course::create([
            'name' => '重複測試課程',
            'start_time' => '2025-10-01 09:00:00',
            'campus_id' => 1,
            'is_weekly_course' => false,
        ]);

        // 第一次生成
        $course->generateCourseSessions();
        $this->assertEquals(1, $course->sessions()->count());

        // 第二次嘗試生成，不應該重複
        $course->generateCourseSessions();
        $this->assertEquals(1, $course->sessions()->count());
    }

    /**
     * 測試邊緣案例：開始日期不匹配 weekdays
     */
    public function test_edge_case_start_date_not_matching_weekdays()
    {
        $course = Course::create([
            'name' => '邊緣案例測試',
            'start_time' => '2025-10-02 09:00:00', // 星期四
            'campus_id' => 1,
            'is_weekly_course' => true,
            'total_sessions' => 2,
            'weekdays' => ['1', '3'], // 星期一、三
        ]);

        $sessions = $course->sessions;

        // 應該從下個匹配日開始
        $firstSession = $sessions->first();
        $this->assertEquals('2025-10-06', $firstSession->start_time->format('Y-m-d')); // 星期一
    }
}
