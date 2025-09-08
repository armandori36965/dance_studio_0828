<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'start_time',
        'end_time',
        'student_count',
        'campus_id',
        'level',
        'is_active',
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
}
