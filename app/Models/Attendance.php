<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    // 與用戶的關聯
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 與課程的關聯
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // 與校區的關聯
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }
}
