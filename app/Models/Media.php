<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['alt' => 'array'];
    }
}
