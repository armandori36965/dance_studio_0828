<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolEvent extends Model
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
    ];

    // 日期欄位
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
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
}
