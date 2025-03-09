<?php

namespace Tests\Unit;

use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if a tag can be created.
     *
     * @return void
     */
    public function test_tag_can_be_created()
    {
        $tag = Tag::create(['name' => 'Sample Tag']);

        $this->assertDatabaseHas('tags', ['name' => 'Sample Tag']);
    }

    /**
     * Test tag-translation relationship.
     *
     * @return void
     */
    public function test_tag_has_translations()
    {
        $tag = Tag::factory()->create();
        $translation = Translation::factory()->create();

        $tag->translations()->attach($translation->id);

        $this->assertTrue($tag->translations->contains($translation));
    }

    /**
     * Test if multiple translations can be attached to a tag.
     *
     * @return void
     */
    public function test_tag_can_have_multiple_translations()
    {
        $tag = Tag::factory()->create();
        $translations = Translation::factory()->count(3)->create();

        $tag->translations()->attach($translations->pluck('id')->toArray());

        $this->assertCount(3, $tag->translations);
    }

    /**
     * Test if tags can be retrieved by name.
     *
     * @return void
     */
    public function test_get_tags_by_name()
    {
        Tag::factory()->create(['name' => 'Tag1']);
        Tag::factory()->create(['name' => 'Tag2']);

        $tags = Tag::whereIn('name', ['Tag1', 'Tag2'])->get();

        $this->assertCount(2, $tags);
    }

    /**
     * Test deleting a tag and ensuring its relationships are removed.
     *
     * @return void
     */
    public function test_deleting_tag_removes_relationships()
    {
        $tag = Tag::factory()->create();
        $translation = Translation::factory()->create();

        $tag->translations()->attach($translation->id);

        $tag->delete();

        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
        $this->assertDatabaseMissing('translation_tag', ['tag_id' => $tag->id]);
    }
}
