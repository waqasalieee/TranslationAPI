<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Waqas Ali',
            'email' => 'waqas@domain.com',
        ]);

        $this->call([
            // LocaleSeeder::class,
            // TagSeeder::class,
            TranslationSeeder::class,
        ]);
    }
}
