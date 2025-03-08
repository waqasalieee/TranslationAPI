<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Contracts\TranslationServiceInterface;
use App\Services\TranslationService;
use App\Services\Contracts\TagServiceInterface;
use App\Services\TagService;
use App\Services\Contracts\LocaleServiceInterface;
use App\Services\LocaleService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TranslationServiceInterface::class, TranslationService::class);
        $this->app->bind(TagServiceInterface::class, TagService::class);
        $this->app->bind(LocaleServiceInterface::class, LocaleService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
