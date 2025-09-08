<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    /**
     * 可批量賦值的屬性
     */
    protected $fillable = [
        'key',
        'value',
        'description',
        'type'
    ];

    /**
     * 屬性轉換
     */
    protected $casts = [
        'value' => 'json',
    ];

    /**
     * 取得設定值
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * 設定值
     */
    public static function setValue(string $key, $value, string $description = null, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
                'type' => $type
            ]
        );
    }
}
