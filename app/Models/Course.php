<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;

class Course extends Model implements Eventable
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'start_time',
        'end_time',
        'student_count',
        'campus_id',
        'teacher_id',
        'level',
        'is_active',
        'sort_order',
    ];

    // 日期欄位
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    // 與校區的關係
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    // 與老師的關係
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // 與學生的多對多關係
    public function students()
    {
        return $this->belongsToMany(User::class, 'user_course');
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
        $key = $this->id ?: 'temp_' . md5($this->name . $this->start_time->toDateTimeString());

        $event = CalendarEvent::make()
            ->title($this->name)
            ->start($this->start_time)
            ->end($this->end_time ?? $this->start_time->addHours(1))
            ->allDay(false)
            ->backgroundColor($this->getCourseColor())
            ->textColor('#ffffff')
            ->resourceId('campus_' . $this->campus_id)
            ->key($key)
            ->extendedProps([
                'model' => static::class,
                'type' => 'course',
                'model_id' => $this->id,
                'key' => $key,
                'description' => $this->description,
                'campus' => $this->campus?->name,
                'level' => $this->level,
                'price' => $this->price,
                'student_count' => $this->student_count,
            ]);

        return $event;
    }

    /**
     * 獲取課程顏色（使用校區顏色）
     */
    protected function getCourseColor(): string
    {
        return $this->campus?->color ?? '#6B7280';
    }
}
