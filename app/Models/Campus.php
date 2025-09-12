<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Guava\Calendar\Contracts\Resourceable;
use Guava\Calendar\ValueObjects\CalendarResource;

class Campus extends Model implements Resourceable
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'is_active',
        'sort_order',
        'color',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * 校區的校務事件
     */
    public function schoolEvents(): HasMany
    {
        return $this->hasMany(SchoolEvent::class);
    }

    /**
     * 校區的課程
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * 校區的人員
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * 校區的出勤記錄
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * 校區的設備
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    /**
     * 校區的財務記錄
     */
    public function finances(): HasMany
    {
        return $this->hasMany(Finance::class);
    }

    /**
     * 實現 Resourceable 接口
     */
    public function toCalendarResource(): CalendarResource
    {
        return CalendarResource::make('campus_' . $this->id)
            ->title($this->name)
            ->eventBackgroundColor($this->color ?? '#3B82F6')
            ->extendedProps([
                'campus_id' => $this->id,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->email,
            ]);
    }
}
