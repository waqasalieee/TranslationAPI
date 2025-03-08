<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if a locale can be created.
     *
     * @return void
     */
    public function test_locale_can_be_created()
    {
        $locale = Locale::create([
            'code' => 'en',
            'name' => 'English',
        ]);

        $this->assertDatabaseHas('locales', [
            'code' => 'en',
            'name' => 'English',
        ]);
    }

    /**
     * Test locale's relationship with translations.
     *
     * @return void
     */
    public function test_locale_has_translations()
    {
        $locale = Locale::factory()->create();
        $translation = Translation::factory()->create(['locale_id' => $locale->id]);

        $this->assertTrue($locale->translations->contains($translation));
    }
}
