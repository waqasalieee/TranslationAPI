<?php

namespace App\Services\Contracts;

use App\Models\Locale;

/**
 * Interface for Locale-related operations.
 */
interface LocaleServiceInterface
{
    /**
     * Retrieve a locale by its ID.
     *
     * @param int $localeId The ID of the locale.
     *
     * @return \App\Models\Locale
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the locale is not found.
     */
    public function getLocaleById(int $localeId): Locale;
}
