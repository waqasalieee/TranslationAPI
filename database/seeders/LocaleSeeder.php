<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locale;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Locale::factory()->count(10)->create();
    }
}
