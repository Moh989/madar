<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Content extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['title' => 'array', 'excerpt' => 'array', 'body' => 'array', 'seo_title' => 'array', 'seo_description' => 'array', 'alt' => 'array', 'services' => 'array', 'button' => 'array', 'gallery' => 'array', 'visible' => 'boolean'];
    }

    public function tr(string $field, ?string $locale = null): string
    {
        return (string) ($this->{$field}[$locale ?? app()->getLocale()] ?? '');
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')->where('visible', true);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function imageUrl(): ?string
    {
        return $this->media_id ? route('media.show', $this->media_id) : ($this->image ? asset($this->image) : null);
    }
}
