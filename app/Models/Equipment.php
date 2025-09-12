<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    protected $fillable = [
        'name',
        'category',
        'serial_number',
        'purchase_date',
        'status',
        'campus_id',
        'sort_order',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    // 與校區的關係
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }
}
