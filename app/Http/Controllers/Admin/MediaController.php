<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index()
    {
        return view('admin.media', ['media' => Media::latest()->paginate(24)]);
    }

    public function store(Request $r)
    {
        $data = $r->validate(['file' => 'required|image|mimes:jpg,jpeg,png,webp|max:8192|dimensions:max_width=4000,max_height=4000', 'alt.ar' => 'required|string|max:250', 'alt.en' => 'required|string|max:250', 'source' => 'required|string|max:255', 'license' => 'required|string|max:255']);
        $source = imagecreatefromstring(file_get_contents($r->file('file')->getRealPath()));
        abort_unless($source, 422);
        $width = imagesx($source);
        $height = imagesy($source);
        $ratio = min(1, 2400 / max($width, $height));
        $output = imagecreatetruecolor((int) ($width * $ratio), (int) ($height * $ratio));
        imagealphablending($output, false);
        imagesavealpha($output, true);
        imagecopyresampled($output, $source, 0, 0, 0, 0, imagesx($output), imagesy($output), $width, $height);
        ob_start();
        imagewebp($output, null, 86);
        $bytes = ob_get_clean();
        $path = 'media/'.Str::uuid().'.webp';
        Storage::disk('local')->put($path, $bytes);
        Media::create(['path' => $path, 'alt' => $data['alt'], 'source' => $data['source'], 'license' => $data['license'], 'width' => imagesx($output), 'height' => imagesy($output)]);

        return back()->with('success','تم رفع الصورة وتحويلها إلى WebP آمن.');
    }
}
