<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Slide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_profile_renders_original_pages_in_both_languages(): void
    {
        foreach (['ar', 'en'] as $locale) {
            $this->get('/'.$locale.'/company-profile?page=5')
                ->assertSee('data-current="5"', false)
                ->assertSee('profile-page-05.webp')
                ->assertSee('/'.$locale.'/company-profile/download')
                ->assertViewHas('pages', fn (array $pages): bool => count($pages) === 17);
        }
    }

    public function test_page_outside_document_is_not_found(): void
    {
        foreach (['0', '18', '-1', 'abc', '2.5'] as $number) {
            $this->get('/ar/company-profile?page='.$number)->assertNotFound();
        }
    }

    public function test_profile_download_delivers_the_current_website_file(): void
    {
        $response = $this->get('/ar/company-profile/download');

        $response->assertDownload('Madar-Al-Alam-Company-Profile.pdf');
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertSame(realpath(config('profile.source')), $response->baseResponse->getFile()->getRealPath());
    }

    public function test_profile_is_hidden_when_original_file_is_missing(): void
    {
        config(['profile.source' => storage_path('app/private/missing-profile.pdf')]);

        $this->get('/ar/company-profile')->assertNotFound();
        $this->get('/ar/company-profile/download')->assertNotFound();
        $this->get('/ar')->assertDontSee('/ar/company-profile');
    }

    public function test_public_pages_link_to_profile_without_removed_disclosures(): void
    {
        foreach (['ar', 'en'] as $locale) {
            $this->get('/'.$locale)->assertSee('/'.$locale.'/company-profile')
                ->assertDontSee('صور تمثيلية')->assertDontSee('Generated representative')
                ->assertDontSee('Representative imagery')->assertDontSee('مولدة');
        }
        $this->get('/ar/about')->assertSee('/ar/company-profile')->assertDontSee('site.image_note');
        $this->get('/sitemap.xml')->assertOk()->assertSee('/ar/company-profile')->assertSee('/en/company-profile');
    }

    public function test_existing_database_and_live_slides_are_cleaned_without_losing_content(): void
    {
        $slide = Slide::firstOrFail();
        $slide->update(['alt' => ['ar' => 'صورة تمثيلية مولدة لمجال air', 'en' => 'Generated representative air scene']]);
        Setting::put('live_slides', [$slide->getAttributesForSnapshot()]);
        Setting::put('copy', ['ar' => ['image_note' => 'صور تمثيلية للمجالات، وليست توثيقاً لأصول الشركة.', 'intro_title' => 'عنوان محفوظ']]);
        $migration = require database_path('migrations/2026_09_23_090550_clean_site_image_descriptions.php');

        $migration->up();

        $this->assertSame('طائرة شحن وعمليات مناولة في مطار', $slide->fresh()->tr('alt', 'ar'));
        $this->assertSame('Cargo aircraft and ground handling at an airport', Setting::valueFor('live_slides')[0]['alt']['en']);
        $this->assertSame(['ar' => ['intro_title' => 'عنوان محفوظ']], Setting::valueFor('copy'));
        $this->assertTrue(Setting::valueFor('navigation')['profile']['visible']);
    }
}
