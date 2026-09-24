<?php

namespace App\Models;

class Sector extends Content
{
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
