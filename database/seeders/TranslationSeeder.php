<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create translations with tags using the factory's withTags method
        Translation::factory()
            ->count(100) // Create 1000 translations
            ->withTags(3) // Attach up to 3 tags per translation
            ->create();
    }
}
