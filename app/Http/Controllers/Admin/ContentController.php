<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentRequest;
use App\Models\Media;
use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use App\Models\Sector;
use App\Models\Setting;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class ContentController extends Controller
{
    private function model($kind)
    {
        return match ($kind) {
            'pages' => Page::class,'sectors' => Sector::class,'projects' => Project::class,'news' => News::class,'slides' => Slide::class,default => abort(404)
        };
    }

    public function index(string $kind)
    {
        $class = $this->model($kind);

        return view('admin.content-index', ['kind' => $kind, 'items' => $class::orderBy('sort_order')->paginate(20)]);
    }

    public function create(string $kind)
    {
        $class = $this->model($kind);

        return view('admin.content-edit', ['kind' => $kind, 'item' => new $class(['status' => 'draft', 'visible' => true, 'sort_order' => 0]), 'media' => Media::latest()->get(), 'sectors' => Sector::all()]);
    }

    public function edit(string $kind, int $id)
    {
        $class = $this->model($kind);

        return view('admin.content-edit', ['kind' => $kind, 'item' => $class::findOrFail($id), 'media' => Media::latest()->get(), 'sectors' => Sector::all()]);
    }

    private function data(ContentRequest $r, $kind)
    {
        $data = $r->validated();
        $allowed = ['slug', 'title', 'excerpt', 'body', 'seo_title', 'seo_description', 'image', 'alt', 'media_id', 'status', 'sort_order', 'visible'];
        $allowed = array_merge($allowed, match ($kind) {
            'slides' => ['link', 'button'],'sectors' => ['services'],'projects' => ['sector_id', 'gallery'],'news' => ['published_at'],default => []
        });

        return array_intersect_key($data, array_flip($allowed));
    }

    public function store(ContentRequest $r, string $kind)
    {
        $class = $this->model($kind);
        $item = $class::create($this->data($r, $kind));

        return redirect()->route('admin.content.edit', [$kind, $item->id])->with('success', 'تم حفظ المحتوى.');
    }

    public function update(ContentRequest $r, string $kind, int $id)
    {
        $class = $this->model($kind);
        $item = $class::findOrFail($id);
        $this->guardLiveLinks($kind, $item, $r->input('status'), $r->input('slug'), $r->boolean('visible'));
        if ($kind === 'pages' && $item->slug !== $r->input('slug')) {
            throw ValidationException::withMessages(['slug' => 'مسارات الصفحات الأساسية ثابتة.']);
        }$item->update($this->data($r, $kind));

        return back()->with('success', 'تم حفظ التغييرات.');
    }

    private function guardLiveLinks($kind, $item, $status, $slug, $visible)
    {
        if ($kind === 'sectors' && ($status !== 'published' || $slug !== $item->slug || ! $visible) && collect(Setting::valueFor('live_slides', []))->contains('link', '/sectors/'.$item->slug)) {
            throw ValidationException::withMessages(['status' => 'هذا القطاع مرتبط بالسلايدر المنشور. حدّث روابط السلايدر وانشره أولاً.']);
        }
    }

    public function destroy(string $kind, int $id)
    {
        $class = $this->model($kind);
        $item = $class::findOrFail($id);
        abort_if($kind === 'pages', 422, 'لا يمكن حذف الصفحات الأساسية.');
        $this->guardLiveLinks($kind, $item, 'draft', $item->slug, false);
        $item->delete();

        return back()->with('success', 'تم حذف المحتوى.');
    }

    public function preview(Request $r, string $kind, int $id)
    {
        $class = $this->model($kind);
        $locale = in_array($r->query('lang'), ['ar', 'en']) ? $r->query('lang') : 'ar';
        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        return response()->view('admin.preview', ['item' => $class::findOrFail($id), 'kind' => $kind, 'preview' => true])->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function publishSlides()
    {
        DB::transaction(function () {
            $slides = Slide::orderBy('sort_order')->lockForUpdate()->get();
            $errors = [];
            if ($slides->count() !== 4) {
                $errors['slides'] = 'يجب أن يحتوي السلايدر على أربع شرائح بالضبط.';
            }foreach ($slides as $s) {
                if (! $s->visible) {
                    $errors['slides'] = 'يجب إظهار الشرائح الأربع قبل نشر السلايدر.';
                }foreach (['ar', 'en'] as $l) {
                    foreach (['title', 'excerpt', 'alt', 'button'] as $f) {
                        if (! $s->tr($f, $l)) {
                            $errors['slides'] = 'أكمل عنوان ووصف وزر ونص بديل باللغتين لكل شريحة.';
                        }
                    }
                }if (! $s->image && ! $s->media_id) {
                    $errors['slides'] = 'الصورة مطلوبة لكل شريحة.';
                }if (! $this->validLink($s->link)) {
                    $errors['slides'] = 'كل شريحة تحتاج رابطاً داخلياً إلى صفحة منشورة.';
                }
            }if ($errors) {
                throw ValidationException::withMessages($errors);
            }Setting::put('live_slides', $slides->map(fn ($s) => $s->getAttributesForSnapshot())->all());
        });

        return back()->with('success', 'تم نشر الشرائح الأربع معاً.');
    }

    private function validLink($link)
    {
        if (! $link) {
            return false;
        }if (preg_match('#^/sectors/([a-z0-9-]+)$#', $link, $m)) {
            return Sector::published()->whereSlug($m[1])->exists();
        }if ($link === '/sectors') {
            return true;
        }if (in_array($link, ['/about', '/contact', '/quote'])) {
            return Page::published()->whereSlug(ltrim($link, '/'))->exists();
        }if ($link === '/projects') {
            return Project::published()->exists();
        }if ($link === '/news') {
            return News::published()->exists();
        }

return false;
    }
}
