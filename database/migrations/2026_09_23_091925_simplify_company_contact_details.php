<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $setting = DB::table('settings')->where('key', 'contact')->first();
        if ($setting) {
            $value = json_decode($setting->value, true);
            unset($value['phone']);
            $value['address'] = ['ar' => 'العراق، بغداد، الداوودي', 'en' => 'Iraq, Baghdad, Al Dawoodi'];
            DB::table('settings')->where('id', $setting->id)->update(['value' => json_encode($value, JSON_UNESCAPED_UNICODE)]);
        }
    }

    public function down(): void
    {
        // Removed contact details must not be republished by a rollback.
    }
};
