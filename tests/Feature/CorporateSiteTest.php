<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Media;
use App\Models\Sector;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CorporateSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->is_admin = true;
        $u->save();

        return $u;
    }

    private function inquiry(): array
    {
        return ['type' => 'quote', 'name' => 'Test requester', 'contact' => 'qa@example.test', 'sector_id' => Sector::first()->id, 'details' => 'A valid request describing shipment requirements.'];
    }

    public function test_localized_pages_and_ten_sectors_render(): void
    {
        foreach (['ar', 'en'] as $l) {
            foreach (['', '/about', '/sectors', '/contact', '/quote'] as $path) {
                $this->get('/'.$l.$path)->assertOk();
            }foreach (Sector::all() as $s) {
                $this->get('/'.$l.'/sectors/'.$s->slug)->assertOk();
            }
        }$this->get('/ar')->assertSee('dir="rtl"', false);
        $this->get('/en')->assertSee('dir="ltr"', false);
    }

    public function test_empty_projects_news_and_privacy_are_hidden(): void
    {
        $this->get('/ar')->assertDontSee('href="http://localhost/ar/projects"', false);
        foreach (['/ar/projects', '/ar/news', '/ar/privacy', '/ar/missing', '/fr'] as $path) {
            $this->get($path)->assertNotFound();
        }
    }

    public function test_language_switch_preserves_sector_path(): void
    {
        $this->get('/ar/sectors/air-freight')->assertSee('href="/en/sectors/air-freight"', false);
    }

    public function test_inquiry_is_saved_before_email_and_success_is_truthful(): void
    {
        config(['mail.inquiry_to' => 'admin@example.test']);
        Mail::shouldReceive('raw')->once()->andThrow(new \RuntimeException('SMTP offline'));
        $this->from('/ar/quote')->post('/ar/inquiries', $this->inquiry())->assertRedirect('/ar/quote')->assertSessionHas('success');
        $this->assertDatabaseHas('inquiries', ['contact' => 'qa@example.test', 'notification_status' => 'failed']);
    }

    public function test_notification_not_configured_and_successful_mail(): void
    {
        config(['mail.inquiry_to' => null]);
        $this->post('/ar/inquiries', $this->inquiry())->assertSessionHasNoErrors();
        $this->assertDatabaseHas('inquiries', ['notification_status' => 'not_configured']);
        config(['mail.inquiry_to' => 'admin@example.test']);
        Mail::shouldReceive('raw')->once()->andReturn(null);
        $this->post('/en/inquiries', $this->inquiry())->assertSessionHasNoErrors();
        $this->assertDatabaseHas('inquiries', ['locale' => 'en', 'notification_status' => 'sent']);
    }

    public function test_inquiry_validation_and_honeypot(): void
    {
        $this->post('/ar/inquiries', ['type' => 'contact', 'name' => 'A', 'contact' => 'invalid', 'details' => 'short', 'website' => 'spam'])->assertSessionHasErrors(['contact', 'details', 'website']);
        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_draft_sector_cannot_receive_requests(): void
    {
        $sector = Sector::first();
        $sector->update(['status' => 'draft']);
        $this->post('/ar/inquiries', $this->inquiry())->assertSessionHasErrors('sector_id');
    }

    public function test_inquiry_rate_limit(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $this->post('/ar/inquiries', $this->inquiry())->assertStatus(302);
        }$this->post('/ar/inquiries', $this->inquiry())->assertStatus(429);
    }

    public function test_admin_guest_redirect_and_non_admin_denial(): void
    {
        foreach (['/admin', '/admin/inquiries', '/admin/media', '/admin/settings', '/admin/content/pages'] as $path) {
            $this->get($path)->assertRedirect('/admin/login');
        }$this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_admin_pages_render_and_noindex(): void
    {
        $this->actingAs($this->admin());
        foreach (['/admin', '/admin/inquiries', '/admin/media', '/admin/settings', '/admin/content/pages', '/admin/content/slides', '/admin/content/sectors', '/admin/content/projects', '/admin/content/news', '/admin/content/projects/create', '/admin/content/pages/1/edit', '/admin/content/sectors/1/preview?lang=en'] as $path) {
            $this->get($path)->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        }
    }

    public function test_login_throttling(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/admin/login', ['email' => 'qa@example.test', 'password' => 'bad']);
        }$response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_non_admin_cannot_login_as_admin(): void
    {
        $u = User::factory()->create(['password' => 'Some-Test-Password']);
        $this->post('/admin/login', ['email' => $u->email, 'password' => 'Some-Test-Password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_slider_drafts_do_not_mutate_live_snapshot(): void
    {
        $this->actingAs($this->admin());
        $old = Setting::valueFor('live_slides');
        Slide::first()->update(['title' => ['ar' => 'عنوان مسودة', 'en' => 'Draft title']]);
        $this->get('/ar')->assertDontSee('عنوان مسودة');
        $this->post('/admin/slides/publish')->assertSessionHasNoErrors();
        $this->get('/ar')->assertSee('عنوان مسودة');
        $this->assertNotEquals($old, Setting::valueFor('live_slides'));
    }

    public function test_incomplete_slider_cannot_publish(): void
    {
        $this->actingAs($this->admin());
        $old = Setting::valueFor('live_slides');
        Slide::first()->update(['alt' => ['ar' => '', 'en' => '']]);
        $this->post('/admin/slides/publish')->assertSessionHasErrors('slides');
        $this->assertEquals($old, Setting::valueFor('live_slides'));
        Slide::first()->delete();
        $this->post('/admin/slides/publish')->assertSessionHasErrors('slides');
    }

    public function test_slider_rejects_dead_links(): void
    {
        $this->actingAs($this->admin());
        Slide::first()->update(['link' => '/sectors/not-real']);
        $this->post('/admin/slides/publish')->assertSessionHasErrors('slides');
    }

    public function test_publish_content_and_escaped_output(): void
    {
        $this->actingAs($this->admin());
        $payload = ['slug' => 'approved-project', 'title' => ['ar' => 'مشروع معتمد', 'en' => 'Approved project'], 'body' => ['ar' => 'وصف معتمد للمشروع <script>alert(1)</script>', 'en' => 'Approved project description'], 'status' => 'published', 'visible' => 1, 'sort_order' => 1, 'sector_id' => Sector::first()->id];
        $this->post('/admin/content/projects', $payload)->assertSessionHasNoErrors();
        $this->get('/ar/projects')->assertOk()->assertSee('مشروع معتمد');
        $this->get('/ar/projects/approved-project')->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/sitemap.xml')->assertSee('/ar/projects/approved-project');
    }

    public function test_incomplete_translation_cannot_publish(): void
    {
        $this->actingAs($this->admin())->post('/admin/content/projects', ['slug' => 'incomplete', 'title' => ['ar' => 'غير مكتمل'], 'status' => 'published', 'visible' => 1, 'sort_order' => 0])->assertSessionHasErrors(['title.en', 'body.ar', 'body.en']);
    }

    public function test_media_reencoded_and_served_as_image(): void
    {
        Storage::fake('local');
        $this->actingAs($this->admin())->post('/admin/media', ['file' => UploadedFile::fake()->image('photo.jpg', 600, 400), 'alt' => ['ar' => 'صورة اختبار', 'en' => 'Test image'], 'source' => 'QA', 'license' => 'Owned'])->assertSessionHasNoErrors();
        $media = Media::firstOrFail();
        Storage::disk('local')->assertExists($media->path);
        $this->assertStringEndsWith('.webp', $media->path);
        $this->assertEquals('image/webp', mime_content_type(Storage::disk('local')->path($media->path)));
    }

    public function test_svg_and_executable_upload_rejected(): void
    {
        $this->actingAs($this->admin());
        foreach (['danger.php', 'danger.svg'] as $name) {
            $this->post('/admin/media', ['file' => UploadedFile::fake()->create($name, 1), 'alt' => ['ar' => 'اختبار', 'en' => 'Test'], 'source' => 'QA', 'license' => 'Owned'])->assertSessionHasErrors('file');
        }$this->assertDatabaseCount('media', 0);
    }

    public function test_update_inquiry_status_and_notes(): void
    {
        $this->post('/ar/inquiries', $this->inquiry());
        $this->actingAs($this->admin())->patch('/admin/inquiries/'.Inquiry::first()->id, ['status' => 'following', 'notes' => 'Internal follow-up'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('inquiries', ['status' => 'following', 'notes' => 'Internal follow-up']);
    }

    public function test_live_slider_sector_cannot_be_deleted(): void
    {
        $this->actingAs($this->admin())->delete('/admin/content/sectors/'.Sector::whereSlug('air-freight')->first()->id)->assertSessionHasErrors('status');
    }

    public function test_shared_copy_changes_are_visible(): void
    {
        Setting::put('copy', ['ar' => ['intro_title' => 'عنوان قابل للتحرير']]);
        $this->get('/ar')->assertSee('عنوان قابل للتحرير');
    }

    public function test_sitemap_omits_drafts_and_contains_both_languages(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertSee('/ar/sectors/air-freight')->assertSee('/en/sectors/air-freight')->assertDontSee('/ar/privacy');
    }
}
