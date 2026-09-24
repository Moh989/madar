<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        abort_unless(is_file(config('profile.source')) && is_file(config('profile.manifest')), 404);
        $pages = File::json(config('profile.manifest'))['pages'];
        $currentPage = filter_var($request->query('page', 1), FILTER_VALIDATE_INT);
        abort_unless(is_int($currentPage) && $currentPage >= 1 && $currentPage <= count($pages), 404);
        $page = new Page([
            'title' => ['ar' => 'الملف التعريفي', 'en' => 'Company profile'],
            'seo_description' => ['ar' => 'تصفّح الملف التعريفي لشركة مدار العالم ومجالات أعمالها.', 'en' => 'Explore our company profile and business sectors.'],
            'image' => $pages[0]['image'],
        ]);

        return view('profile', compact('page', 'pages', 'currentPage'));
    }

    public function download(): BinaryFileResponse
    {
        abort_unless(is_file(config('profile.source')), 404);

        return response()->download(config('profile.source'), 'Madar-Al-Alam-Company-Profile.pdf', [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, no-cache',
        ]);
    }
}
