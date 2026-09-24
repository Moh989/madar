<?php

namespace App\Models;

class Slide extends Content
{
    public function getAttributesForSnapshot(): array
    {
        return $this->only(['slug', 'title', 'excerpt', 'alt', 'image', 'media_id', 'link', 'button', 'sort_order']);
    }
}
