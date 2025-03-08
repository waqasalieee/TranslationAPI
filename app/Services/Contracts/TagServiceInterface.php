<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface TagServiceInterface
{
    /**
     * Retrieve all tags.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTags(): Collection;

    /**
     * Retrieve tags by their names.
     *
     * @param array $names
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTagsByName(array $names): Collection;

    /**
     * Create or retrieve tags by their names.
     *
     * @param array $names
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createOrRetrieveTags(array $names): Collection;
}
