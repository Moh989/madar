<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function cleanValue(mixed $value): mixed
    {
        if (is_array($value)) {
            unset($value['image_note']);

            return array_map(fn (mixed $entry): mixed => $this->cleanValue($entry), $value);
        }
        if (! is_string($value)) {
            return $value;
        }

        return str_replace([
            'صور تمثيلية للمجالات، وليست توثيقاً لأصول الشركة.',
            'Representative imagery; not documentation of company-owned assets.',
            'تم الاعتماد على الملف التعريفي',
            'بيانات الاتصال من الملف التعريفي المرفق.',
            'الصور تمثيلية مولدة.',
            'مشهد تمثيلي: ',
            'Representative scene: ',
            'صورة تمثيلية مولدة لمجال air',
            'صورة تمثيلية مولدة لمجال sea',
            'صورة تمثيلية مولدة لمجال industry',
            'صورة تمثيلية مولدة لمجال agriculture',
            'Generated representative air scene',
            'Generated representative sea scene',
            'Generated representative industry scene',
            'Generated representative agriculture scene',
        ], [
            '', '', '', '', '', '', '',
            'طائرة شحن وعمليات مناولة في مطار',
            'سفينة حاويات ورافعات الميناء',
            'هيكل منشأة صناعية قيد الإنشاء',
            'حقول زراعية ومنشأة لتجهيز المنتجات',
            'Cargo aircraft and ground handling at an airport',
            'Container ship and port cranes',
            'Industrial building under construction',
            'Agricultural fields and a produce processing building',
        ], $value);
    }

    public function up(): void
    {
        foreach (['pages', 'sectors', 'projects', 'news', 'slides', 'media', 'settings'] as $table) {
            $fields = match ($table) {
                'settings' => ['value'],
                'media' => ['alt'],
                default => ['title', 'excerpt', 'body', 'alt', 'seo_title', 'seo_description'],
            };
            DB::table($table)->orderBy('id')->chunkById(100, function ($rows) use ($table, $fields): void {
                foreach ($rows as $row) {
                    $changes = [];
                    foreach ($fields as $field) {
                        $original = json_decode($row->{$field} ?? 'null', true);
                        $cleaned = $this->cleanValue($original);
                        if ($original !== $cleaned) {
                            $changes[$field] = json_encode($cleaned, JSON_UNESCAPED_UNICODE);
                        }
                    }
                    if ($changes !== []) {
                        DB::table($table)->where('id', $row->id)->update($changes);
                    }
                }
            });
        }
        $navigation = DB::table('settings')->where('key', 'navigation')->first();
        if ($navigation) {
            $value = json_decode($navigation->value, true);
            $value['profile'] ??= ['visible' => true, 'order' => 6];
            DB::table('settings')->where('id', $navigation->id)->update(['value' => json_encode($value)]);
        }
    }

    public function down(): void
    {
        // Editorial removals are deliberately not reintroduced on rollback.
    }
};
