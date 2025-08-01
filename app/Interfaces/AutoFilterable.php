<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Builder;

interface AutoFilterable
{


    /**
     * Get the final, combined list of all allowed filters (real and virtual).
     * This is the method the service will use for whitelisting.
     *
     * @return array
     */
    public function getAllowedFilters(): array;



    /**
     * Get the list of columns that are allowed for filtering.
     *
     * @return array
     */
    public function getFilterableColumns(): array;

    /**
     * Defines columns that are not real database columns but are allowed as filters
     * to be handled manually in closures like `extraOperation`.
     *
     * @return array
     */
    public function getVirtualColumns(): array;

    /**
     * Get the list of columns that are allowed for sorting.
     *
     * @return array
     */
    public function getSortableColumns(): array;

    /**
     * Get the list of columns on the main model's table that are searchable in the global search.
     *
     * @return array
     */
    public function getSearchableColumns(): array;

    /**
     * Get the list of columns on the translation model's table that are searchable.
     *
     * @return array
     */
    public function getSearchableTranslationColumns(): array;

    /**
     * Get the name of the translation table.
     * e.g., 'property_translations'
     *
     * @return string
     */
    public function getTranslationTable(): ?string;

    /**
     * Get the foreign key used in the translation table.
     * e.g., 'property_id'
     *
     * @return string
     */
    public function getTranslationForeignKey(): ?string;
    /**
     * Get the primary key name for the main model's table.
     * Defaults to 'id'.
     *
     * @return string
     */
    public function getModelKeyName(): string;

    /**
     * Get the column name for the locale in the translation table.
     * Defaults to 'locale'.
     *
     * @return string|null
     */
    public function getLocaleColumnName(): ?string;

    /**
     * Allows for custom logic to be applied when joining translations.
     * This can be used for complex scenarios.
     *
     * @param Builder $query
     * @param string $mainTable
     * @param string $translationTable
     * @return void
     */
    public function applyTranslationJoin(Builder $query, string $mainTable, string $translationTable): void;
}
