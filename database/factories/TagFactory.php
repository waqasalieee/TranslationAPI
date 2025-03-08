<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Throwable;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tags = ['web','mobile','desktop','api','tablet','pager'];
        $tag = '';
        try {
            $tag = $this->faker->unique()->randomElement($tags);
        }
        catch (Throwable $e)
        {
            $tag = $this->faker->unique()->word();
        }
        return [
            'name' => $tag,
        ];
    }
}
