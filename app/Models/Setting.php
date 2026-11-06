<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        try {
            if (! Schema::hasTable('settings')) {
                return $default;
            }

            return static::where('key', $key)->value('value') ?? $default;
        } catch (Throwable) {
            return $default;
        }
    }

    public static function set(string $key, ?string $value): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            static::updateOrCreate(['key' => $key], ['value' => $value]);
        } catch (Throwable) {
            // Ignore in unmigrated test contexts
        }
    }
}
