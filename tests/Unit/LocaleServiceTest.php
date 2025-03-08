<?php

namespace Tests\Unit;

use App\Models\Locale;
use App\Services\LocaleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for LocaleService.
 */
class LocaleServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The LocaleService instance.
     *
     * @var \App\Services\LocaleService
     */
    private LocaleService $localeService;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->localeService = new LocaleService();
    }

    /**
     * Test getting a locale by ID successfully.
     *
     * @return void
     */
    public function testGetLocaleByIdSuccessfully(): void
    {
        // Arrange
        $locale = Locale::factory()->create();

        // Act
        $retrievedLocale = $this->localeService->getLocaleById($locale->id);

        // Assert
        $this->assertInstanceOf(Locale::class, $retrievedLocale);
        $this->assertEquals($locale->id, $retrievedLocale->id);
    }

    /**
     * Test getting a locale by ID throws an exception if not found.
     *
     * @return void
     */
    public function testGetLocaleByIdThrowsExceptionWhenNotFound(): void
    {
        // Arrange
        $nonExistentId = 999;

        // Assert
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("Locale with ID {$nonExistentId} not found.");

        // Act
        $this->localeService->getLocaleById($nonExistentId);
    }

    /**
     * Test getting a locale by code successfully.
     *
     * @return void
     */
    public function testGetLocaleByCodeSuccessfully(): void
    {
        // Arrange
        $locale = Locale::factory()->create(['code' => 'en']);

        // Act
        $retrievedLocale = $this->localeService->getLocaleByCode('en');

        // Assert
        $this->assertInstanceOf(Locale::class, $retrievedLocale);
        $this->assertEquals('en', $retrievedLocale->code);
    }

    /**
     * Test getting a locale by code throws an exception if not found.
     *
     * @return void
     */
    public function testGetLocaleByCodeThrowsExceptionWhenNotFound(): void
    {
        // Arrange
        $nonExistentCode = 'xx';

        // Assert
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("Locale with code '{$nonExistentCode}' not found.");

        // Act
        $this->localeService->getLocaleByCode($nonExistentCode);
    }

    /**
     * Test creating or retrieving locales by names.
     *
     * @return void
     */
    public function testCreateOrRetrieveLocalesByNames(): void
    {
        // Arrange
        $existingLocale = Locale::factory()->create(['name' => 'English']);
        $localeNames = ['English', 'Spanish', 'French'];

        // Act
        $locales = $this->localeService->createOrRetrieveLocalesByName($localeNames);

        // Assert
        $this->assertCount(3, $locales);
        $this->assertTrue($locales->contains('name', 'English'));
        $this->assertTrue($locales->contains('name', 'Spanish'));
        $this->assertTrue($locales->contains('name', 'French'));
    }

    /**
     * Test creating or retrieving locales by codes.
     *
     * @return void
     */
    public function testCreateOrRetrieveLocalesByCodes(): void
    {
        // Arrange
        $existingLocale = Locale::factory()->create(['code' => 'en']);
        $localeCodes = ['en', 'es', 'fr'];

        // Act
        $locales = $this->localeService->createOrRetrieveLocalesByCodes($localeCodes);

        // Assert
        $this->assertCount(3, $locales);
        $this->assertTrue($locales->contains('code', 'en'));
        $this->assertTrue($locales->contains('code', 'es'));
        $this->assertTrue($locales->contains('code', 'fr'));
    }

    /**
     * Test checking if a locale exists by code.
     *
     * @return void
     */
    public function testLocaleExistsByCode(): void
    {
        // Arrange
        Locale::factory()->create(['code' => 'en']);

        // Act & Assert
        $this->assertTrue($this->localeService->localeExistsByCode('en'));
        $this->assertFalse($this->localeService->localeExistsByCode('xx'));
    }

    /**
     * Test getting a locale by name successfully.
     *
     * @return void
     */
    public function testGetLocaleByNameSuccessfully(): void
    {
        // Arrange
        $locale = Locale::factory()->create(['name' => 'English']);

        // Act
        $retrievedLocale = $this->localeService->getLocaleByName('English');

        // Assert
        $this->assertInstanceOf(Locale::class, $retrievedLocale);
        $this->assertEquals('English', $retrievedLocale->name);
    }

    /**
     * Test getting a locale by name throws an exception if not found.
     *
     * @return void
     */
    public function testGetLocaleByNameThrowsExceptionWhenNotFound(): void
    {
        // Arrange
        $nonExistentName = 'NonExistentLocale';

        // Assert
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("Locale with name '{$nonExistentName}' not found.");

        // Act
        $this->localeService->getLocaleByName($nonExistentName);
    }

    /**
     * Test getting all locales.
     *
     * @return void
     */
    public function testGetAllLocales(): void
    {
        // Arrange
        $locales = Locale::factory()->count(5)->create();

        // Act
        $retrievedLocales = $this->localeService->getAllLocales();

        // Assert
        $this->assertInstanceOf(Collection::class, $retrievedLocales);
        $this->assertCount(5, $retrievedLocales);
        $retrievedLocales->each(function (Locale $locale) use ($locales) {
            $this->assertTrue($locales->contains($locale));
        });
    }

    /**
     * Test getting all locales when no locales exist.
     *
     * @return void
     */
    public function testGetAllLocalesWhenNoLocalesExist(): void
    {
        // Act
        $retrievedLocales = $this->localeService->getAllLocales();

        // Assert
        $this->assertInstanceOf(Collection::class, $retrievedLocales);
        $this->assertCount(0, $retrievedLocales);
    }
}
