<?php

namespace Tests\Feature;

use App\Models\Sector;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CompanyPresentationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_company_phone_is_never_rendered_even_from_legacy_settings(): void
    {
        Setting::put('contact', [...Setting::valueFor('contact'), 'phone' => '+964 785 210 1010']);

        foreach (['ar', 'en'] as $locale) {
            foreach (['', '/about', '/contact', '/quote', '/company-profile', '/sectors/air-freight'] as $path) {
                $this->get('/'.$locale.$path)->assertOk()
                    ->assertDontSee('785 210 1010')->assertDontSee('tel:')
                    ->assertDontSee('"telephone"', false)->assertDontSee('601');
            }
        }
    }

    public function test_contact_migration_shortens_address_and_preserves_other_settings(): void
    {
        Setting::put('contact', ['email' => 'office@example.test', 'phone' => '+964 785 210 1010', 'address' => ['ar' => 'محلة 601، شارع 20، دار 1/26'], 'extra' => 'preserved']);
        $migration = require database_path('migrations/2026_09_23_091925_simplify_company_contact_details.php');

        $migration->up();

        $this->assertSame([
            'email' => 'office@example.test',
            'address' => ['ar' => 'العراق، بغداد، الداوودي', 'en' => 'Iraq, Baghdad, Al Dawoodi'],
            'extra' => 'preserved',
        ], Setting::valueFor('contact'));
    }

    public function test_admin_cannot_reintroduce_company_phone_through_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.settings.update'), [
            'contact' => ['email' => 'office@example.test', 'phone' => '+964 785 210 1010'],
            'brand' => ['english_approved' => false],
        ])->assertSessionHasNoErrors();

        $this->assertArrayNotHasKey('phone', Setting::valueFor('contact'));
        $this->assertSame('office@example.test', Setting::valueFor('contact')['email']);
    }

    public function test_about_page_uses_editable_copy_and_only_published_sectors(): void
    {
        Setting::put('copy', ['ar' => ['about_vision_text' => 'رؤية محدثة من لوحة الإدارة']]);
        Sector::where('slug', 'air-freight')->update(['status' => 'draft']);

        $this->get('/ar/about')->assertOk()->assertSee('رؤية محدثة من لوحة الإدارة')
            ->assertSee('العراق، بغداد، الداوودي')->assertSee('/ar/company-profile')
            ->assertDontSee('/ar/sectors/air-freight')->assertSee('/ar/sectors/sea-freight');
        $this->get('/en/about')->assertOk()->assertSee('Our vision')->assertSee('Iraq, Baghdad, Al Dawoodi');
    }

    public function test_magazine_text_and_images_reference_updated_contact_pages(): void
    {
        $pages = File::json(config('profile.manifest'))['pages'];

        $this->assertStringContainsString('Iraq - Baghdad - Al Dawoodi', $pages[1]['text']);
        foreach ($pages as $page) {
            $this->assertStringNotContainsString('785 210 1010', $page['text']);
            $this->assertStringNotContainsString('601', $page['text']);
            $this->assertFileExists(public_path($page['image']));
            $this->assertFileExists(public_path($page['thumbnail']));
        }
    }
}
