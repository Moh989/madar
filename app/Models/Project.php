<?php

namespace App\Models;

class Project extends Content
{
    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
