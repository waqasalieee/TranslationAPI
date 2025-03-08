<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    protected static string $databasePath;

    protected function setUp(): void
    {
        parent::setUp();

        // fwrite(STDOUT, "Checking if testing database exists...\n");
        // $databasePath = base_path('database/testing.sqlite');

        // if (!file_exists($databasePath)) {
        //     fwrite(STDOUT, "Creating testing database...\n");
        //     touch($databasePath);

        //     fwrite(STDOUT, "Running migrations...\n");
        //     Artisan::call('migrate', ['--env' => 'testing']);

        //     fwrite(STDOUT, "Seeding the database...\n");
        //     Artisan::call('db:seed', ['--class' => 'TranslationSeeder', '--env' => 'testing']);
        //     fwrite(STDOUT, "Database setup complete.\n");
        // }
    }

    protected function tearDown(): void
    {
        // $databasePath = base_path('database/testing.sqlite');

        // if (file_exists($databasePath)) {
        //     fwrite(STDOUT, "Deleting testing database...\n");
        //     unlink($databasePath);
        //     fwrite(STDOUT, "Database deleted.\n");
        // }

        parent::tearDown();
    }


    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        //$databasePath = base_path('database/testing.sqlite');
        // static::$databasePath = __DIR__ . '/../database/testing.sqlite';

        // if (!file_exists(static::$databasePath)) {
        //     touch(static::$databasePath);
        //     Artisan::call('migrate', ['--env' => 'testing']);
        //     Artisan::call('db:seed', ['--class' => 'TranslationSeeder', '--env' => 'testing']);
        // }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        // if (file_exists(static::$databasePath)) {
        //     unlink(static::$databasePath); // Delete the database file
        // }
    }
}
