<?php

namespace App\Services;

use App\Models\Tag;
use App\Services\Contracts\TagServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class TagService implements TagServiceInterface
{
    /**
     * Retrieve all tags.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTags(): Collection
    {
        return Tag::all();
    }

    /**
     * Retrieve tags by their names.
     *
     * @param array $names
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTagsByName(array $names): Collection
    {
        return Tag::whereIn('name', $names)->get();
    }

    /**
     * Create or retrieve tags by their names.
     *
     * @param array $names
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createOrRetrieveTags(array $names): Collection
    {
        $tags = Tag::whereIn('name', $names)->get();
        $existingNames = $tags->pluck('name')->toArray();

        $newTags = collect($names)
            ->diff($existingNames)
            ->map(function ($name) {
                return Tag::create(['name' => $name]);
            });

        return $tags->merge($newTags);
    }
}
