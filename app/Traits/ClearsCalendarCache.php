<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait ClearsCalendarCache
{
    /**
     * 清除行事曆快取
     */
    public function clearCalendarCache(): void
    {
        try {
            // 檢查是否使用 Redis cache store
            $store = Cache::getStore();

            if ($store instanceof \Illuminate\Cache\RedisStore) {
                // Redis cache - 使用 keys 方法清除所有匹配的快取
                $cacheKeys = Cache::getRedis()->keys('*calendar_events*');
                foreach ($cacheKeys as $key) {
                    // 移除 cache prefix 前綴
                    $cleanKey = str_replace(config('cache.prefix'), '', $key);
                    Cache::forget($cleanKey);
                }
            } else {
                // 非 Redis cache - 使用完整的快取清除策略
                $this->clearAllCalendarCacheKeys();
            }

            Log::info('Calendar cache cleared for ' . static::class . ': ' . $this->getKey());
        } catch (\Exception $e) {
            Log::error('Failed to clear calendar cache: ' . $e->getMessage());
        }
    }

    /**
     * 清除所有行事曆快取鍵（非 Redis 環境）
     */
    private function clearAllCalendarCacheKeys(): void
    {
        // 清除基本快取鍵
        $baseKeys = [
            'calendar_events_all',
            'calendar_events_active',
            'calendar_events_by_date',
            'calendar_events_by_category',
            'calendar_events_by_campus',
            'calendar_stats'
        ];

        foreach ($baseKeys as $key) {
            Cache::forget($key);
        }

        // 清除日期範圍快取（過去、現在、未來三年）
        $currentYear = now()->year;
        $years = [$currentYear - 1, $currentYear, $currentYear + 1];

        foreach ($years as $year) {
            for ($month = 1; $month <= 12; $month++) {
                // 各種日期格式的快取鍵
                $startOfMonth = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
                $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

                // 生成可能的快取鍵模式
                $dateKeys = [
                    "calendar_events_{$year}_{$month}",
                    "calendar_events_{$startOfMonth->format('Y-m-d')}_{$endOfMonth->format('Y-m-d')}",
                ];

                foreach ($dateKeys as $dateKey) {
                    // 各種學校事件和課程的組合
                    $schoolCourseCombinations = [
                        '_school_1_courses_1',
                        '_school_1_courses_0',
                        '_school_0_courses_1',
                        '_school_0_courses_0',
                        ''  // 空字串為基本鍵
                    ];

                    foreach ($schoolCourseCombinations as $combination) {
                        $baseKey = $dateKey . $combination;

                        // 清除各種搜尋、分類、校區的組合
                        $patterns = [
                            $baseKey,
                            $baseKey . '_search_' . md5(''),
                            $baseKey . '_category_',
                            $baseKey . '_campus_',
                            $baseKey . '_sessions',
                            $baseKey . '_search_' . md5('') . '_category_' . '_campus_' . '_sessions',
                        ];

                        foreach ($patterns as $pattern) {
                            Cache::forget($pattern);
                        }
                    }
                }
            }
        }
    }
}

