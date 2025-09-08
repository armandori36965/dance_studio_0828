<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finance extends Model
{
    protected $fillable = [
        'transaction_date',
        'type',
        'category',
        'amount',
        'description',
        'status',
        'campus_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // 與校區的關係
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }
}
