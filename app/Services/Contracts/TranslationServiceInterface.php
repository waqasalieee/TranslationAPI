<?php

namespace App\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

/**
 * Interface for Translation-related operations.
 */
interface TranslationServiceInterface
{
    /**
     * List translations with optional filters.
     *
     * @param array $filters The filters for the translation query.
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function listTranslations(array $filters): Paginator;

    /**
     * Retrieve a translation by its ID.
     *
     * @param int $id The ID of the translation.
     *
     * @return array
     */
    public function getTranslationById(int $id): array;

    /**
     * Create a new translation.
     *
     * @param array $data The data for the new translation.
     *
     * @return array
     */
    public function createTranslation(array $data): array;

    /**
     * Update an existing translation by its ID.
     *
     * @param int   $id   The ID of the translation.
     * @param array $data The updated data for the translation.
     *
     * @return array
     */
    public function updateTranslation(int $id, array $data): array;

    /**
     * Delete a translation by its ID.
     *
     * @param int $id The ID of the translation to delete.
     *
     * @return void
     */
    public function deleteTranslation(int $id): void;

    /**
     * Export all translations as a grouped array.
     *
     * @return array
     */
    public function exportTranslations(string $localeCode, array $tags): array;
}
