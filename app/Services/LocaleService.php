<?php

namespace App\Services;

use App\Models\Locale;
use App\Services\Contracts\LocaleServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Service for handling locale-related operations.
 */
class LocaleService implements LocaleServiceInterface
{
    /**
     * Get a locale by its ID.
     *
     * @param int $localeId The ID of the locale.
     *
     * @return \App\Models\Locale
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the locale is not found.
     */
    public function getLocaleById(int $localeId): Locale
    {
        $locale = Locale::find($localeId);

        if (!$locale) {
            throw new ModelNotFoundException("Locale with ID {$localeId} not found.");
        }

        return $locale;
    }

    /**
     * Get a locale by its name.
     *
     * @param string $name The name of the locale.
     *
     * @return \App\Models\Locale
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the locale is not found.
     */
    public function getLocaleByName(string $name): Locale
    {
        $locale = Locale::where('name', $name)->first();

        if (!$locale) {
            throw new ModelNotFoundException("Locale with name '{$name}' not found.");
        }

        return $locale;
    }

    /**
     * Get a locale by its code.
     *
     * @param string $code The code of the locale.
     *
     * @return \App\Models\Locale
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the locale is not found.
     */
    public function getLocaleByCode(string $code): Locale
    {
        $locale = Locale::where('code', $code)->first();

        if (!$locale) {
            throw new ModelNotFoundException("Locale with code '{$code}' not found.");
        }

        return $locale;
    }

    /**
     * Get all locales.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllLocales(): Collection
    {
        return Locale::all();
    }

    /**
     * Create or retrieve locales by their names.
     *
     * @param array $locales An array of locale names.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createOrRetrieveLocalesByName(array $locales): Collection
    {
        $existingLocales = Locale::whereIn('name', $locales)->get();
        $existingNames = $existingLocales->pluck('name')->toArray();

        $newLocales = collect($locales)
            ->diff($existingNames)
            ->map(function ($name) {
                return Locale::create(['name' => $name, 'code' => strtolower($name)]);
            });

        return $existingLocales->merge($newLocales);
    }

    /**
     * Create or retrieve locales by their codes.
     *
     * @param array $codes An array of locale codes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createOrRetrieveLocalesByCodes(array $codes): Collection
    {
        $existingLocales = Locale::whereIn('code', $codes)->get();
        $existingCodes = $existingLocales->pluck('code')->toArray();

        $newLocales = collect($codes)
            ->diff($existingCodes)
            ->map(function ($code) {
                return Locale::create(['code' => $code, 'name' => ucfirst($code)]);
            });

        return $existingLocales->merge($newLocales);
    }

    /**
     * Check if a locale exists by its code.
     *
     * @param string $code The locale code.
     *
     * @return bool
     */
    public function localeExistsByCode(string $code): bool
    {
        return Locale::where('code', $code)->exists();
    }
}
