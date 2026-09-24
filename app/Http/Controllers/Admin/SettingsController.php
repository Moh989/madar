<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings', ['contact' => Setting::valueFor('contact', []), 'navigation' => Setting::valueFor('navigation', []), 'brand' => Setting::valueFor('brand', []), 'copy' => Setting::valueFor('copy', [])]);
    }

    public function update(Request $r)
    {
        $data = $r->validate(['copy.ar.*' => 'nullable|string|max:2000', 'copy.en.*' => 'nullable|string|max:2000', 'contact.email' => 'nullable|email|max:190', 'contact.address.ar' => 'nullable|string|max:400', 'contact.address.en' => 'nullable|string|max:400', 'brand.english_legal_name' => 'nullable|string|max:500', 'brand.english_approved' => 'required|boolean', 'navigation.*.visible' => 'required|boolean', 'navigation.*.order' => 'required|integer|min:0|max:99']);
        foreach (['contact', 'brand', 'navigation', 'copy'] as $k) {
            Setting::put($k, $data[$k] ?? []);
        }

        return back()->with('success', 'تم تحديث الإعدادات.');
    }
}
