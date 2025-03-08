<?php

use Illuminate\Database\Eloquent\Collection;
use App\Models\Translation;
use App\Models\Locale;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Contracts\TagServiceInterface;
use App\Services\Contracts\LocaleServiceInterface;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $translationService;
    protected $mockTagService;
    protected $mockLocaleService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock dependencies
        $this->mockTagService = Mockery::mock(TagServiceInterface::class);
        $this->mockLocaleService = Mockery::mock(LocaleServiceInterface::class);

        // Initialize translation service with mock dependencies
        $this->translationService = new \App\Services\TranslationService(
            $this->mockTagService,
            $this->mockLocaleService
        );
    }

    /**
     * Test listing translations with filters.
     *
     * @return void
     */
    public function testListTranslationsWithFilters(): void
    {
        // Create sample translations using the factory
        $translations = Translation::factory()->count(3)->withTags(3)->create();

        // Use the key of the first translation as a filter
        $filterKey = $translations->first()->key;

        // Execute the service method with the filter
        $result = $this->translationService->listTranslations(['key' => $filterKey]);

        // Assertions
        $this->assertNotEmpty($result->items(), 'The result should not be empty.');
        $this->assertCount(1, $result->items(), 'The result should only contain translations matching the filter.');

        // Validate the returned data
        $firstItem = $result->items()[0];
        $this->assertEquals($filterKey, $firstItem['key'], 'The key of the first item should match the filter.');
        $this->assertEquals($translations->first()->locale_id, $firstItem['locale_id'], 'The locale_id should match the expected locale.');

        $retrievedTags = $firstItem['tags']->pluck('name')->toArray();
        // echo "\n \n Retrieved tags \n";
        //print_r($retrievedTags->toArray());
        // print_r($retrievedTags);
        $expectedTags = $translations->first()->tags->pluck('name')->toArray();
        // echo "Expected tags \n";
        // print_r($expectedTags);
        //dd($expectedTags);
        foreach ($expectedTags as $expectedTag) {
            // echo "Expected tag \n";
            // print_r($expectedTag);
            // echo "\n \n Retrieved tags \n";
            // print_r($retrievedTags);
            //exit;
            $this->assertContains($expectedTag, $retrievedTags, "The tags should include '{$expectedTag}'.");
        }
    }

    /**
     * Test retrieving a translation by its ID.
     */
    public function testGetTranslationById(): void
    {
        // Create a translation
        $locale = Locale::factory()->create();
        $translation = Translation::factory()->create(['key' => 'greet', 'value' => 'hello', 'locale_id' => $locale->id]);

        // Run the test
        $result = $this->translationService->getTranslationById($translation->id);

        $this->assertEquals('greet', $result['key']);
    }

    /**
     * Test creating a new translation.
     */
    public function testCreateTranslation(): void
    {
         // Create a locale and mock the locale service
        $locale = Locale::factory()->create();
        $this->mockLocaleService
            ->shouldReceive('getLocaleById')
            ->with($locale->id)
            ->once()
            ->andReturn($locale);

        // Mock tag service
        $tags = new Collection([
            Tag::factory()->create(['name' => 'tag1']),
        ]);
        $this->mockTagService
            ->shouldReceive('getTagsByName')
            ->with(['tag1'])
            ->once()
            ->andReturn($tags);

        // Create a new translation using the factory
        $data = ['key' => 'greet', 'value' => 'hello', 'locale_id' => $locale->id, 'tags' => ['tag1']];
        $result = $this->translationService->createTranslation($data);

        // Assertions
        $this->assertEquals('greet', $result['key']);
        $this->assertCount(1, $result['tags']);
        $this->assertEquals('tag1', $result['tags'][0]['name']);
    }

    /**
     * Test updating an existing translation.
     */
    public function testUpdateTranslation(): void
    {
        // Create a translation
        $locale = Locale::factory()->create();
        $translation = Translation::factory()->create(['key' => 'greet', 'value' => 'hello', 'locale_id' => $locale->id]);

        // Mock the locale service
        $this->mockLocaleService
            ->shouldReceive('getLocaleById')
            ->with($locale->id)
            ->once()
            ->andReturn($locale);

        // Mock the tag service
        $tags = new Collection([
            Tag::factory()->create(['name' => 'tag1']),
        ]);
        $this->mockTagService
            ->shouldReceive('getTagsByName')
            ->with(['tag1'])
            ->once()
            ->andReturn($tags);

        // Update the translation
        $data = [
            'key'       => 'greet',
            'value'     => 'hello updated',
            'locale_id' => $locale->id,
            'tags'      => ['tag1'],
        ];
        $result = $this->translationService->updateTranslation($translation->id, $data);

        // Assertions
        $this->assertEquals('hello updated', $result['value']);
        $this->assertCount(1, $result['tags']);
        $this->assertEquals('tag1', $result['tags'][0]['name']);
    }

    /**
     * Test deleting a translation.
     */
    public function testDeleteTranslation(): void
    {
        // Create a translation
        $locale = Locale::factory()->create();
        $translation = Translation::factory()->create(['key' => 'greet', 'value' => 'hello', 'locale_id' => $locale->id]);

        // Run the delete method
        $this->translationService->deleteTranslation($translation->id);

        // Assertions
        $this->assertNull(Translation::find($translation->id));
    }

    /**
     * Test exporting translations.
     */
    public function testExportTranslations(): void
    {
        // Create sample data
        // echo "Creating data \n";
        $locale = Locale::factory()->create();
        $t = Translation::factory()->withTags(3)->create(['key' => 'greet', 'value' => 'hello', 'locale_id' => $locale->id]);
        // echo "Translation locale = ".$t->locale->code."\n";
        // $tr = Translation::with('tags')->first();
        // echo "tr data\n";print_r($tr->toArray());
        //die();
        //exit;
        // Run export method
        // echo $locale->code;
        $result = $this->translationService->exportTranslations($locale->code);
        //echo 'result received ---';
        // print_r($result);
        //echo 'result printed';
        //exit;

        // Assertions
        //$this->assertArrayHasKey($locale->code, $result);
        $this->assertEquals('greet', $result[0]['key']);
    }
}
