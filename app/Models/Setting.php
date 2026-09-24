<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    public static function valueFor($key, $default = null)
    {
        return static::where('key', $key)->first()?->value ?? $default;
    }

    public static function put($key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
