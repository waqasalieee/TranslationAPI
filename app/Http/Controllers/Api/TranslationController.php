<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\TranslationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TranslationController extends Controller
{
    /**
     * The translation service instance.
     *
     * @var \App\Services\Contracts\TranslationServiceInterface
     */
    protected TranslationServiceInterface $translationService;

    /**
     * Create a new TranslationController instance.
     *
     * @param \App\Services\Contracts\TranslationServiceInterface $translationService
     */
    public function __construct(TranslationServiceInterface $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * List translations with optional filters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'nullable|string|max:255', // Adjust max length as needed
            'value' => 'nullable|string|max:255',// Adjust max length as needed
            'tags' => 'nullable|string',  // Validate as a comma-separated string for now
        ]);

        $filters = $validated; // Use validated data

        // Further processing of 'tags' if needed:
        if (isset($filters['tags'])) {
            $filters['tags'] = explode(',', $filters['tags']);
            $filters['tags'] = array_map('trim', $filters['tags']); // Trim whitespace from each tag
        }

        $translations = $this->translationService->listTranslations($filters);

        return response()->json($translations, 200);
    }

    /**
     * Retrieve a specific translation by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $translation = $this->translationService->getTranslationById($id);

        return response()->json($translation, 200);
    }

    /**
     * Create a new translation.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'locale_id' => 'required|exists:locales,id',
            'key' => [
                'required',
                'string',
                Rule::unique('translations')->where(function ($query) use ($request) {
                    return $query->where('locale_id', $request->locale_id);
                }),
            ],
            'value' => 'required|string',
            'tags' => 'array', // Validate as array
            'tags.*' => 'exists:tags,name', // Validate each tag exists
        ]);

        $translation = $this->translationService->createTranslation($validated);

        return response()->json($translation, 201);
    }

    /**
     * Update an existing translation.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'locale_id' => 'sometimes|exists:locales,id',
            'key' => [
                'sometimes',
                'string',
                Rule::unique('translations')->where(function ($query) use ($request, $id) {
                    return $query->where('locale_id', $request->locale_id)->where('id', '!=', $id); //add id check
                }),
            ],
            'value' => 'sometimes|string',
            'tags' => 'array', // Validate as array
            'tags.*' => 'exists:tags,name', // Validate each tag exists
        ]);

        $translation = $this->translationService->updateTranslation($id, $validated);

        return response()->json($translation, 200);
    }

    /**
     * Delete a translation by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->translationService->deleteTranslation($id);

        return response()->json(['message' => 'Translation deleted successfully'], 200);
    }

    /**
     * Export all translations grouped by locale.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request, $localeCode): JsonResponse
    {
        $tags = [];
        if ($request->has('tags')) {
            $tagsString = $request->input('tags');
            // echo $tagsString;
            if(!empty($tagsString))
            {
                $tags = explode(',', $tagsString);
                $tags = array_map('trim', $tags);
            }
        }
        $translations = $this->translationService->exportTranslations($localeCode, $tags);

        return response()->json($translations, 200);
    }
}
