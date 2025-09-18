<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;

class SchoolEvent extends Model implements Eventable
{
    use HasFactory;

    // 可填充欄位
    protected $fillable = [
        'title',           // 事件標題
        'description',     // 事件描述
        'start_time',      // 開始時間
        'end_time',        // 結束時間
        'location',        // 地點
        'category',        // 事件類型
        'status',          // 狀態
        'campus_id',       // 所屬校區
        'created_by',      // 創建者
        'extended_props',  // 擴展屬性
        'sort_order',      // 排序欄位
    ];

    // 日期欄位
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'extended_props' => 'array',
    ];

    // 預設值
    protected $attributes = [
        'location' => '',
        'status' => 'active',
    ];

    // 與用戶的關係
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 與校區的關係
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * 獲取路由鍵名
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }


    /**
     * 實現 Eventable 接口
     */
public function toCalendarEvent(): CalendarEvent
    {
        // 為沒有 ID 的事件生成唯一 key
        $key = $this->id ?: 'temp_' . md5($this->title . $this->start_time->toDateTimeString());

        $event = CalendarEvent::make()
            ->title($this->title)
            ->start($this->start_time)
            ->end($this->end_time ?? $this->start_time->addHours(2))
            ->allDay(false)
            ->backgroundColor($this->getDefaultEventColor())
            ->textColor('#ffffff')
            ->resourceId('campus_' . $this->campus_id)
            ->key($key)
            ->extendedProps([
                'model' => static::class,
                'type' => 'school_event',
                'model_id' => $this->id,
                'key' => $key,
                'description' => $this->description,
                'campus' => $this->campus?->name,
                'location' => $this->location,
                'category' => $this->category,
                'status' => $this->status,
            ]);

        // 國定假日使用背景顯示
        if ($this->category === 'national_holiday') {
            $event->display('background')
                  ->allDay(true); // 國定假日設為全天事件
        }

        return $event;
    }

    /**
     * 獲取預設事件顏色（使用校區顏色）
     */
    protected function getDefaultEventColor(): string
    {
        // 國定假日使用特殊的紅色背景
        if ($this->category === 'national_holiday') {
            return '#DC2626'; // 紅色背景，表示國定假日
        }

        return $this->campus?->color ?? '#6B7280';
    }
}
