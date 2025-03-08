<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker;

/**
 * Class TranslationsTest
 *
 * @package Tests\Feature
 */
class TranslationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Authenticated user.
     *
     * @var \App\Models\User
     */
    protected User $user;

    /**
     * Authentication token for the user.
     *
     * @var string
     */
    protected string $token;

    /**
     * Locale instance for translations.
     *
     * @var \App\Models\Locale
     */
    protected Locale $locale;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('TestToken')->plainTextToken;
        $locale =  Locale::get()->first();
        if(!$locale)
        {
            //echo "Create new Locale \n";
            $this->locale = Locale::factory()->create();
        }
        else
        {
            $this->locale = $locale;
            //echo "Using existing Locale \n";
        }

    }

    /**
     * Get headers for authenticated requests.
     *
     * @return array
     */
    protected function getAuthHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->token}",
        ];
    }

    /**
     * Test access to translation routes without authentication.
     *
     * @return void
     */
    public function testTranslationsRoutesRequireAuthentication(): void
    {
        $response = $this->getJson('/api/translations');
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);

        $response = $this->postJson('/api/translations', [
            'locale_id' => 1,
            'key' => 'greeting',
            'value' => 'Hello',
        ]);
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Test access to translation routes with authentication.
     *
     * @return void
     */
    public function testTranslationsRoutesWithAuthentication(): void
    {
        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson('/api/translations');
        $response->assertStatus(200);

        $response = $this->withHeaders($this->getAuthHeaders())
            ->postJson('/api/translations', [
                'key' => 'greeting',
                'value' => 'Hello',
                'locale_id' => $this->locale->id,
            ]);
        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'key', 'value', 'locale_id']);
    }

    /**
     * Test exporting translations.
     *
     * @return void
     */
    public function testExportTranslationsWithAuthentication(): void
    {
        $locale = Locale::factory()->create();
        Translation::factory()
                ->count(10) // Create translations with locale and tags
                ->withTags(3) // Attach up to 3 tags per translation
                ->create(['locale_id'=>$locale->id]);
        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson('/api/translations/export/'.$locale->code);

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    '*' => []
                ],
            ]);
    }

    /**
     * Test exporting translations with tags.
     *
     * @return void
     */
    public function testExportTranslationsWithTags(): void
    {
        // Create a tag and associate it with a translation
        $tag = Tag::factory()->create(['name' => 'greeting']);
        //dd(Tag::all()->toArray());
        $translation = Translation::factory()->create([
            'locale_id' => $this->locale->id,
            'key' => 'greeting_key',
            'value' => 'Hello',
        ]);
        $translation->tags()->attach($tag);

        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson('/api/translations/export/'.$this->locale->code.'?tags='.$tag->name);

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    '*' => []
                ],
            ])->assertJsonFragment([
                'key' => 'greeting_key',
                'value' => 'Hello',
            ]);
    }

    /**
     * Test updating a translation.
     *
     * @return void
     */
    public function testUpdateTranslation(): void
    {
        $translation = Translation::factory()->create([
            'locale_id' => $this->locale->id,
            'key' => 'greeting',
            'value' => 'Hello',
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
            ->putJson("/api/translations/{$translation->id}", [
                'key' => 'greeting_updated',
                'value' => 'Hello, updated!',
                'locale_id' => $this->locale->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['key' => 'greeting_updated'])
            ->assertJsonFragment(['value' => 'Hello, updated!']);
    }

    /**
     * Test invalid update of a translation.
     *
     * @return void
     */
    public function testUpdateTranslationWithInvalidData(): void
    {
        $translation = Translation::factory()->create([
            'locale_id' => $this->locale->id,
            'key' => 'greeting',
            'value' => 'Hello',
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
            ->putJson("/api/translations/{$translation->id}", [
                'key' => '', // Invalid key
                'value' => 'Updated Value',
                'locale_id' => $this->locale->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['key']);
    }

    /**
     * Test deleting a translation.
     *
     * @return void
     */
    public function testDeleteTranslation(): void
    {
        $translation = Translation::factory()->create([
            'locale_id' => $this->locale->id,
            'key' => 'greeting',
            'value' => 'Hello',
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
            ->deleteJson("/api/translations/{$translation->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Translation deleted successfully']);

        $this->assertDatabaseMissing('translations', [
            'id' => $translation->id,
        ]);
    }

    /**
     * Test showing a specific translation.
     *
     * @return void
     */
    public function testShowTranslation(): void
    {
        $translation = Translation::factory()->create([
            'locale_id' => $this->locale->id,
            'key' => 'greeting',
            'value' => 'Hello',
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson("/api/translations/{$translation->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['key' => 'greeting'])
            ->assertJsonFragment(['value' => 'Hello']);
    }

    /**
     * Test showing a non-existent translation.
     *
     * @return void
     */
    public function testShowNonExistentTranslation(): void
    {
        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson('/api/translations/99999');

        $response->assertStatus(404);
    }

    /**
     * Test searching translations by key.
     *
     * @return void
     */
    public function testSearchTranslationByKey(): void
    {
        Translation::factory()->create([
            'locale_id' => $this->locale->id,
            'key' => 'greeting',
            'value' => 'Hello',
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson('/api/translations?key=greeting');

        $response->assertStatus(200)
            ->assertJsonFragment(['key' => 'greeting']);
    }

    /**
     * Test searching translations by value.
     *
     * @return void
     */
    public function testSearchTranslationByValue(): void
    {
        Translation::factory()->create([
            'locale_id' => $this->locale->id,
            'key' => 'greeting',
            'value' => 'Hello',
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson('/api/translations?value=Hello');

        $response->assertStatus(200)
            ->assertJsonFragment(['value' => 'Hello']);
    }

    /**
     * Test searching translations by tag.
     *
     * @return void
     */
    public function testSearchTranslationByTag(): void
    {
        // Create a tag and associate it with a translation
        $tag = Tag::factory()->create(['name' => 'greeting']);
        //dd(Tag::all()->toArray());
        $translation = Translation::factory()->create([
            'locale_id' => $this->locale->id,
            'key' => 'greeting_key',
            'value' => 'Hello',
        ]);
        $translation->tags()->attach($tag);

        // echo "Translations \n";
        // print_r(Translation::with('tags')->get()->toArray());

        // Perform a GET request to search by tag
        $response = $this->withHeaders($this->getAuthHeaders())
            ->getJson('/api/translations?tags=greeting');
        // echo "Response \n";
            // print_r($response->decodeResponseJson() );
        // Assertions
        $response->assertStatus(200)
            ->assertJsonFragment([
                'key' => 'greeting_key',
                'value' => 'Hello',
            ]);

        // Additional assertion to validate tags structure
        //$response->assertJsonPath('data.0.tags.0.name', 'greeting');
    }

}
