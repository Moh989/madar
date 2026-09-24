<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/ar');
Route::get('/media/{media}', [SiteController::class, 'media'])->name('media.show');
Route::get('/sitemap.xml', [SiteController::class, 'sitemap']);
Route::get('/robots.txt', fn () => response(app()->isProduction() ? "User-agent: *\nDisallow: /admin\nSitemap: ".config('app.url').'/sitemap.xml' : "User-agent: *\nDisallow: /", 200, ['Content-Type' => 'text/plain']));
Route::get('/admin/login', [AuthController::class, 'show'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.store');
Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('inquiries', [DashboardController::class, 'inquiries'])->name('inquiries');
    Route::patch('inquiries/{inquiry}', [DashboardController::class, 'updateInquiry'])->name('inquiries.update');
    Route::get('settings', [SettingsController::class, 'edit'])->name('settings');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('media', [MediaController::class, 'index'])->name('media');
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::post('slides/publish', [ContentController::class, 'publishSlides'])->name('slides.publish');
    Route::get('content/{kind}', [ContentController::class, 'index'])->name('content.index');
    Route::get('content/{kind}/create', [ContentController::class, 'create'])->name('content.create');
    Route::post('content/{kind}', [ContentController::class, 'store'])->name('content.store');
    Route::get('content/{kind}/{id}/edit', [ContentController::class, 'edit'])->name('content.edit');
    Route::get('content/{kind}/{id}/preview', [ContentController::class, 'preview'])->name('content.preview');
    Route::put('content/{kind}/{id}', [ContentController::class, 'update'])->name('content.update');
    Route::delete('content/{kind}/{id}', [ContentController::class, 'destroy'])->name('content.destroy');
});
Route::prefix('{locale}')->where(['locale' => 'ar|en'])->middleware('locale')->group(function () {
    Route::get('/', [SiteController::class, 'home'])->name('home');
    Route::get('/company-profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/company-profile/download', [ProfileController::class, 'download'])->name('profile.download');
    Route::get('/sectors', [SiteController::class, 'sectors'])->name('sectors');
    Route::get('/sectors/{slug}', [SiteController::class, 'sector'])->name('sector');
    Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
    Route::get('/quote', [SiteController::class, 'contact'])->name('quote');
    Route::post('/inquiries', [InquiryController::class, 'store'])->middleware('throttle:inquiries')->name('inquiries.store');
    Route::get('/{kind}', [SiteController::class, 'listing'])->where('kind', 'projects|news')->name('listing');
    Route::get('/{kind}/{slug}', [SiteController::class, 'detail'])->where('kind', 'projects|news')->name('detail');
    Route::get('/{slug}', [SiteController::class, 'page'])->where('slug', 'about|privacy')->name('page');
});
