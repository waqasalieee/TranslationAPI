<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Tests for API performance.
 */
class PerformanceTest extends TestCase
{
    use WithoutMiddleware; // Use this trait to disable middleware for performance tests
    //use RefreshDatabase;

    /**
     * Authentication token for the user.
     *
     * @var string
     */
    protected string $token;

    /**
     * A check for seeder.
     *
     * @var bool
     */
    protected static bool $seeded = false;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->token = $user->createToken('PerformanceToken')->plainTextToken;

        if (!self::$seeded) {
            $locale = Locale::first();
            if (!$locale) {
                echo "\n Running Big Data seeder \n";
                $this->artisan('db:seed', ['--class' => 'BigDataSeeder']);
            }
            else
            {
                echo "\n Using existing Big Data \n";
            }
            self::$seeded = true;
        }
    }

    /**
     * Test listing translations.
     *
     * @return void
     */
    public function testListTranslations(): void
    {
        $begin = microtime(true);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/translations');

        $time_taken = round((microtime(true) - $begin)*1000, 2);
        echo "Listing translations - Time taken = $time_taken ms\n";

        $response->assertStatus(200);
        $this->assertLessThan(200, $time_taken, "Listing translations took $time_taken ms.");
    }

    /**
     * Test listing translations by value.
     *
     * @return void
     */
    public function testListTranslationsByValue(): void
    {
        $begin = microtime(true);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/translations?value=accusamus');

        $time_taken = round((microtime(true) - $begin)*1000, 2);
        echo "Listing translations by value - Time taken = $time_taken ms\n";

        $response->assertStatus(200);
        $this->assertLessThan(200, $time_taken, "Listing translations by value took $time_taken ms.");
    }

    /**
     * Test listing translations by key.
     *
     * @return void
     */
    public function testListTranslationsByKey(): void
    {
        $begin = microtime(true);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/translations?key=corporis');

        $time_taken = round((microtime(true) - $begin)*1000, 2);
        echo "Listing translations by key - Time taken = $time_taken ms\n";

        $response->assertStatus(200);
        $this->assertLessThan(200, $time_taken, "Listing translations by key took $time_taken ms.");
    }

    /**
     * Test listing translations by tags.
     *
     * @return void
     */
    public function testListTranslationsByTags(): void
    {
        $begin = microtime(true);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/translations?tags=aliquam');

        $time_taken = round((microtime(true) - $begin)*1000, 2);
        echo "Listing translations by Tags - Time taken = $time_taken ms\n";

        $response->assertStatus(200);
        $this->assertLessThan(200, $time_taken, "Listing translations by Tags took $time_taken ms.");
    }

    /**
     * Test exporting translations.
     *
     * @return void
     */
    public function testExportTranslations(): void
    {
        //$this->markTestSkipped('Skipping export');
        $begin = microtime(true);

        $locale = Locale::first();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/translations/export/'.$locale->code);

        $time_taken = round((microtime(true) - $begin)*1000, 2);
        echo "Export Time taken = $time_taken \n";

        $response->assertStatus(200);
        $this->assertLessThan(500, $time_taken, "Exporting translations took $time_taken ms.");
    }

    /**
     * Test showing a specific translation.
     *
     * @return void
     */
    public function testShowTranslation(): void
    {
        $begin = microtime(true);
        $translation = Translation::inRandomOrder()->first();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson("/api/translations/{$translation->id}");

        $time_taken = round((microtime(true) - $begin)*1000, 2);
        echo "Show translation - Time taken = $time_taken ms\n";

        $response->assertStatus(200);
        $this->assertLessThan(200, $time_taken, "Show translation route took $time_taken ms.");
    }

    /**
     * Test creating a new translation.
     *
     * @return void
     */
    public function testStoreTranslation(): void
    {
        $begin = microtime(true);

        $locale = Locale::inRandomOrder()->first();
        $tags = Tag::inRandomOrder()->limit(5)->pluck('name')->toArray();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/translations', [
            'locale_id' => $locale->id,
            'key' => 'performance_key'.'_' . fake()->unique()->uuid,
            'value' => 'Performance value',
            'tags' => $tags,
        ]);

        $time_taken = round((microtime(true) - $begin)*1000, 2);
        echo "Store translation - Time taken = $time_taken ms\n";

        $response->assertStatus(201);
        $this->assertLessThan(200, $time_taken, "Store translation route too $time_taken ms.");
    }

    /**
     * Test updating a translation.
     *
     * @return void
     */
    public function testUpdateTranslation(): void
    {
        $begin = microtime(true);

        $translation = Translation::inRandomOrder()->first();
        $tags = Tag::inRandomOrder()->limit(3)->pluck('name')->toArray();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->putJson("/api/translations/{$translation->id}", [
            'key' => $translation->key.'_updated',
            'value' => $translation->value.'_updated',
            'tags' => $tags,
        ]);

        $time_taken = round((microtime(true) - $begin)*1000, 2);
        echo "Update translation - Time taken = $time_taken ms\n";

        $response->assertStatus(200);
        $this->assertLessThan(200, $time_taken, "Update translation route too $time_taken ms.");
    }

    /**
     * Test deleting a translation.
     *
     * @return void
     */
    public function testDeleteTranslation(): void
    {
        $begin = microtime(true);

        $translation = Translation::inRandomOrder()->first();


        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->deleteJson("/api/translations/{$translation->id}");

        $time_taken = round((microtime(true) - $begin)*1000, 2);
        echo "Delete translation - Time taken = $time_taken ms\n";

        $response->assertStatus(200);
        $this->assertLessThan(200, $time_taken, "Delete translation route too $time_taken ms.");
    }
}
