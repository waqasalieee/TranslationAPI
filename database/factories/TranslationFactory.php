<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure there are locales available in the database
        $locale = Locale::inRandomOrder()->first();

        if (!$locale) {
            // If no locales exist, create 10 new ones
            Locale::factory(2)->create();
            $locale = Locale::inRandomOrder()->first();
        }

        return [
            'locale_id' => $locale->id, // Use an existing locale
            'key' => $this->faker->word . '_' . $this->faker->randomNumber(9), // Combine random word with unique ID
            'value' => $this->faker->sentence,
        ];
    }

    /**
     * Define a state to attach tags to the translation.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withTags(int $count = 3): self
    {
        return $this->afterCreating(function (Translation $translation) use ($count) {
            // Create tags if they don't exist
            $tags = Tag::inRandomOrder()->take($count)->get();

            if ($tags->count() < $count) {
                $newTags = Tag::factory($count - $tags->count())->create();
                $tags = $tags->merge($newTags);
            }

            $translation->tags()->attach($tags->pluck('id')->toArray());
        });
    }
}

