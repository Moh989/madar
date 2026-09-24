<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        $kind = $this->route('kind');
        abort_unless(in_array($kind, ['pages', 'sectors', 'projects', 'news', 'slides']), 404);
        $published = $this->input('status') === 'published';
        $required = $published ? 'required' : 'nullable';

        return ['slug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:100', Rule::unique($kind)->ignore($this->route('id'))], 'title.ar' => 'required|string|max:180', 'title.en' => "$required|string|max:180", 'excerpt.ar' => 'nullable|string|max:500', 'excerpt.en' => 'nullable|string|max:500', 'body.ar' => 'nullable|string|max:30000', 'body.en' => 'nullable|string|max:30000', 'seo_title.*' => 'nullable|string|max:180', 'seo_description.*' => 'nullable|string|max:320', 'alt.ar' => 'nullable|string|max:250', 'alt.en' => 'nullable|string|max:250', 'image' => ['nullable', Rule::in(['images/air.webp', 'images/sea.webp', 'images/industry.webp', 'images/agriculture.webp'])], 'media_id' => 'nullable|exists:media,id', 'status' => ['required', Rule::in(['draft', 'published'])], 'visible' => 'required|boolean', 'sort_order' => 'required|integer|min:0|max:999', 'sector_id' => 'nullable|exists:sectors,id', 'gallery' => 'nullable|array|max:12', 'gallery.*' => 'integer|exists:media,id', 'services.*' => 'nullable|string|max:3000', 'link' => ['nullable', 'regex:#^/(sectors(/[a-z0-9-]+)?|about|contact|quote|projects|news)$#'], 'button.*' => 'nullable|string|max:100', 'published_at' => 'nullable|date'];
    }

    public function withValidator($v)
    {
        $v->after(function ($v) {
            if ($this->input('status') === 'published' && in_array($this->route('kind'), ['projects', 'news', 'sectors'])) {
                foreach (['ar', 'en'] as $l) {
                    if (! $this->input('body.'.$l)) {
                        $v->errors()->add('body.'.$l, 'أكمل الوصف قبل النشر.');
                    }if (($this->input('image') || $this->input('media_id')) && ! $this->input('alt.'.$l)) {
                        $v->errors()->add('alt.'.$l, 'النص البديل مطلوب عند النشر.');
                    }
                }
            }
        });
    }
}
