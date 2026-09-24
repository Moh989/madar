<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $guarded = ['id'];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
