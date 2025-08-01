<?php

namespace App\Data;

use App\Enums\PaginationFormateEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class DynamicFilterData extends Data
{

    /**
     *  dynamic filter data model.
     *
     * @param ?string $globalFilter global filter string
     * @param array<ColumnFilterData> $filters dynamic filtering array [id,value,filterFns]
     * @param array<ColumnFilterData> $orFilters dynamic filtering array [id,value,filterFns]
     * @param ?object $advanceFilter Advanced nested filtering object
     * @param array<ColumnSortData> $sorting dynamic sorting array [id,desc]
     * @param string $page pagination page or 'all' for all rows
     * @param string $perPage pagination pre page or 'all' for all rows
     * @param callable $extraOperation extra filtre logic take one param \Illuminate\Database\Eloquent\Builder
     * @param callable $globaleFilterExtraOperation extra filtre logic take one param \Illuminate\Database\Eloquent\Builder
     * @param callable $beforeOperation before logic take one param \Illuminate\Database\Eloquent\Builder
     * @param PaginationFormateEnum $paginationFormate pagination formate
     **/
    public function __construct(
        public ?string $globalFilter = null,
        public array|Collection $filters = [],
        public array|Collection $orFilters = [],
        public ?object $advanceFilter = null,
        public array|Collection $sorting = [],
        public string $page = '1',
        public string $perPage = DEFAULT_DATA_LIMIT,
        public  $extraOperation = null,
        public  $beforeOperation = null,
        public  $globaleFilterExtraOperation = null,
        public PaginationFormateEnum $paginationFormate = PaginationFormateEnum::normal
    ) {}
}
