<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Locale;
use App\Models\Tag;
use App\Models\User;
use App\Models\Translation;

class BigDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param int $numTranslations The number of translations to generate. Default is 100,000.
     * @return void
     */
    public function run(int $numTranslations = 10000, int $numLocales = 20): void
    {
        $user = User::first();
        if(!$user)
        {
            $this->call([
                UserSeeder::class,
            ]);
        }

        $chunkSize = $numTranslations/$numLocales;

        $locales = Locale::inRandomOrder()->take($numLocales)->get();
        if ($locales->count() < $numLocales) {
            $newlocales = Locale::factory($numLocales - $locales->count())->create();
            $locales = $locales->merge($newlocales);
        }
        // Tag::factory(3)->create();     // factory handeling tags

        foreach ($locales as $locale) {
            Translation::factory($chunkSize)
                ->withTags(3) // create with tags
                ->create(['locale_id' => $locale->id]);
        }
    }
}
