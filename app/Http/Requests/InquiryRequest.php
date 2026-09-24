<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['type' => ['required', Rule::in(['contact', 'quote'])], 'name' => 'required|string|max:120', 'company' => 'nullable|string|max:160', 'contact' => ['required', 'string', 'max:190', function ($a, $v, $fail) {
            if (! filter_var($v, FILTER_VALIDATE_EMAIL) && ! preg_match('/^\+?[\d\s()\-]{7,25}$/', $v)) {
                $fail(__('site.invalid_contact'));
            }
        }], 'sector_id' => ['required_if:type,quote', 'nullable', Rule::exists('sectors', 'id')->where('status', 'published')->where('visible', 1)], 'details' => 'required|string|min:15|max:6000', 'origin' => 'nullable|string|max:160', 'destination' => 'nullable|string|max:160', 'cargo' => 'nullable|string|max:160', 'weight' => 'nullable|string|max:100', 'website' => 'prohibited'];
    }

    public function messages(): array
    {
        return ['required' => __('site.required'), 'min' => __('site.too_short'), 'max' => __('site.too_long')];
    }
}
