<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Translation;
use App\Services\Contracts\TranslationServiceInterface;
use App\Services\Contracts\TagServiceInterface;
use App\Services\Contracts\LocaleServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Locale;


class TranslationService implements TranslationServiceInterface
{
    /**
     * The tag service instance.
     *
     * @var \App\Services\Contracts\TagServiceInterface
     */
    protected TagServiceInterface $tagService;

    /**
     * The locale service instance.
     *
     * @var \App\Services\Contracts\LocaleServiceInterface
     */
    protected LocaleServiceInterface $localeService;

    /**
     * Create a new TranslationService instance.
     *
     * @param \App\Services\Contracts\TagServiceInterface $tagService
     * @param \App\Services\Contracts\LocaleServiceInterface $localeService
     */
    public function __construct(TagServiceInterface $tagService, LocaleServiceInterface $localeService)
    {
        $this->tagService = $tagService;
        $this->localeService = $localeService;
    }

    /**
     * List translations with optional filters.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function listTranslations(array $filters): Paginator
    {
        // Base query with eager loading
        $query = Translation::with([
            'locale:id,code', // Only fetch locale id and code
            'tags:id,name'    // Only fetch tag id and name
        ]);

        // Apply key filter
        if (!empty($filters['key'])) {
            $query->where('key', 'like', '%' . $filters['key'] . '%');
        }

        // Apply value filter
        if (!empty($filters['value'])) {
            $query->where('value', 'like', '%' . $filters['value'] . '%');
        }

        // Apply tags filter
        if (!empty($filters['tags'])) {
            $tags = $filters['tags'];
            // echo "Tags ------ \n";
            // print_r($tags);
            $query->join('translation_tag', 'translations.id', '=', 'translation_tag.translation_id')
                ->join('tags', 'translation_tag.tag_id', '=', 'tags.id')
                ->whereIn('tags.name', $tags);
        }

        // echo $query->toSql();
        // exit;

        // Return paginated results
        return $query->simplePaginate(10);
    }

    /**
     * Retrieve a translation by its ID.
     *
     * @param int $id
     * @return array
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getTranslationById(int $id): array
    {
        $translation = Translation::with(['locale', 'tags'])->find($id);

        if (!$translation) {
            throw new ModelNotFoundException('Translation not found.');
        }

        return $translation->toArray();
    }

    /**
     * Create a new translation.
     *
     * @param array $data
     * @return array
     */
    public function createTranslation(array $data): array
    {
        // Validate locale existence
        $this->localeService->getLocaleById($data['locale_id']);

        $translation = Translation::create($data);

        if (!empty($data['tags'])) {
            $tags = $this->tagService->getTagsByName($data['tags']);
            $translation->tags()->attach($tags);
        }

        return $translation->load('tags')->toArray();
    }

    /**
     * Update an existing translation.
     *
     * @param int $id
     * @param array $data
     * @return array
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateTranslation(int $id, array $data): array
    {
        $translation = Translation::findOrFail($id);

        if (isset($data['locale_id'])) {
            $this->localeService->getLocaleById($data['locale_id']);
        }

        $translation->update($data);

        if (isset($data['tags'])) {
            $tags = $this->tagService->getTagsByName($data['tags']);
            $translation->tags()->sync($tags);
        }

        return $translation->load('tags')->toArray();
    }

    /**
     * Delete a translation by its ID.
     *
     * @param int $id
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteTranslation(int $id): void
    {
        $translation = Translation::findOrFail($id);
        $translation->delete();
    }

    /**
     * Export all translations grouped by locale.
     *
     * @return array
     */
    public function exportTranslations($localeCode, $tags = []): array
    {
        //dd($localeCode);
        $locale = Locale::where('code', $localeCode)->first();
        if(!$locale)
        {
            throw new ModelNotFoundException('Locale not found.');
        }

        // Fetch translations with tags
        $query = Translation::select(
            'translations.id',
            'translations.key',
            'translations.value',
            DB::raw('GROUP_CONCAT(tags.name) as tag_names') // Combine tag names into a single field
        )
        ->join('translation_tag', 'translations.id', '=', 'translation_tag.translation_id')
        ->join('tags', 'translation_tag.tag_id', '=', 'tags.id')
        ->where('translations.locale_id', $locale->id);

        // Apply tag filters
        if (!empty($tags)) {
            $query->whereIn('tags.name', $tags);
        }
        //echo $query->toSql();

        // Group by translation
        $query->groupBy('translations.id', 'translations.key', 'translations.value');

        // echo $query->toSql();

        // Fetch and process results
        $translations = $query->get();
        // echo "Translations ---- \n";
        // print_r($translations);

        $result = $translations->map(function ($translation) {
            return [
                'key' => $translation->key,
                'value' => $translation->value,
                'tags' => explode(',', $translation->tag_names), // Convert tag_names back to an array
            ];
        })->toArray();

        // echo "Result ---- \n";
        // print_r($result);

        return $result;
    }
}
