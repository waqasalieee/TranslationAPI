<?php

namespace Tests\Unit;

use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TagServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving all tags.
     *
     * @return void
     */
    public function test_get_all_tags(): void
    {
        // Arrange: Create a few tags
        Tag::factory()->count(3)->create();

        $tagService = new TagService();

        // Act: Call the getAllTags method
        $tags = $tagService->getAllTags();

        // Assert: Check if the returned collection contains 3 tags
        $this->assertInstanceOf(Collection::class, $tags);
        $this->assertCount(3, $tags);
    }

    /**
     * Test retrieving tags by name.
     *
     * @return void
     */
    public function test_get_tags_by_name(): void
    {
        // Arrange: Create tags
        $tag1 = Tag::factory()->create(['name' => 'Tag1']);
        $tag2 = Tag::factory()->create(['name' => 'Tag2']);
        $tag3 = Tag::factory()->create(['name' => 'Tag3']);

        $tagService = new TagService();

        // Act: Retrieve tags by name
        $tags = $tagService->getTagsByName(['Tag1', 'Tag3']);

        // Assert: Check if only the correct tags are retrieved
        $this->assertCount(2, $tags);
        $this->assertTrue($tags->contains($tag1));
        $this->assertTrue($tags->contains($tag3));
        $this->assertFalse($tags->contains($tag2));
    }

    /**
     * Test creating or retrieving tags by their names.
     *
     * @return void
     */
    public function test_create_or_retrieve_tags(): void
    {
        // Arrange: Create an initial tag
        $tag1 = Tag::factory()->create(['name' => 'Tag1']);
        $tagService = new TagService();

        // Act: Call createOrRetrieveTags with existing and new tags
        $tags = $tagService->createOrRetrieveTags(['Tag1', 'Tag2']);

        // Assert: Check if the existing tag is not duplicated, and new tag is created
        $this->assertCount(2, $tags);
        $this->assertTrue($tags->contains($tag1)); // Existing tag should be there
        $this->assertTrue($tags->contains(fn ($tag) => $tag->name === 'Tag2')); // New tag should be created
    }

    /**
     * Test creating new tags.
     *
     * @return void
     */
    public function test_create_new_tags(): void
    {
        // Arrange
        $tagService = new TagService();

        // Act: Create new tags
        $tags = $tagService->createOrRetrieveTags(['Tag1', 'Tag2', 'Tag3']);

        // Assert: Ensure all 3 tags are created
        $this->assertCount(3, $tags);
        $this->assertTrue($tags->contains(fn ($tag) => $tag->name === 'Tag1'));
        $this->assertTrue($tags->contains(fn ($tag) => $tag->name === 'Tag2'));
        $this->assertTrue($tags->contains(fn ($tag) => $tag->name === 'Tag3'));
    }
}
