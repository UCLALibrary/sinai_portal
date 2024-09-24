<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait HasRelatedEntities
{
    /**
     * Cached decoded JSON data.
     *
     * @var array|null
     */
    protected $jsonData = null;

    /**
     * Get the decoded JSON data.
     *
     * @return array
     */
    protected function getJsonData(): array
    {
        if ($this->jsonData === null) {
            $this->jsonData = json_decode($this->json, true) ?? [];
        }
        return $this->jsonData;
    }

    /**
     * Extract the last part of an ARK identifier to get the ID.
     *
     * @param string|null $arkIdentifier
     * @return string|null
     */
    protected function extractIdFromArk(?string $arkIdentifier): ?string
    {
        if ($arkIdentifier) {
            $arkParts = explode('/', $arkIdentifier);
            return end($arkParts);
        }
        return null;
    }

    /**
     * General method to get related entities based on JSON data.
     *
     * @param string $jsonKey
     * @param string $modelClass
     * @param callable|null $filterCallback
     * @param callable|null $mapCallback
     * @return Collection
     */
    protected function getRelatedEntities(string $jsonKey, string $modelClass, callable $filterCallback = null, callable $mapCallback = null): Collection
    {
        $data = $this->getJsonData();
        $items = $data[$jsonKey] ?? [];
        $ids = [];
        $additionalData = [];

        foreach ($items as $item) {
            if ($filterCallback === null || $filterCallback($item)) {
                if (isset($item['id'])) {
                    $id = $this->extractIdFromArk($item['id']);
                    if ($id) {
                        $ids[] = $id;
                        $additionalData[$id] = $item;
                    }
                }
            }
        }

        if (!empty($ids)) {
            $models = $modelClass::whereIn('id', $ids)->get();

            if ($mapCallback) {
                return $models->map(function ($model) use ($additionalData, $mapCallback) {
                    return $mapCallback($model, $additionalData[$model->id]);
                });
            }

            return $models;
        }

        return collect([]);
    }

    /**
     * General method to get IDs from JSON data based on type IDs.
     *
     * @param string $jsonKey
     * @param array $typeIds
     * @return array
     */
    protected function getIdsByType(string $jsonKey, array $typeIds): array
    {
        $data = $this->getJsonData();
        $entries = $data[$jsonKey] ?? [];
        $ids = [];

        foreach ($entries as $entry) {
            if (isset($entry['id'], $entry['type']['id'])) {
                $entryTypeId = $entry['type']['id'];
                if (in_array($entryTypeId, $typeIds)) {
                    $ids[] = $entry['id'];
                }
            }
        }

        return $ids;
    }

    /**
     * Helper method for mapping collections to arrays.
     *
     * @param Collection $collection
     * @param callable $mapCallback
     * @return array
     */
    protected function mapCollection(Collection $collection, callable $mapCallback): array
    {
        return $collection->map($mapCallback)->toArray();
    }
}
