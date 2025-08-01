<?php

namespace App\Services;

use App\Data\ColumnFilterData;
use App\Data\ColumnSortData;
use App\Data\DynamicFilterData;
use App\Enums\FilterFnsEnum;
use App\Enums\PaginationFormateEnum;
use App\Interfaces\AutoFilterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class AutoFIlterAndSortService
{

    public function __construct(
        public Model $model
    ) {}

    /**
     * Get data handler with dynamic options, now smarter with AutoFilterable interface.
     *
     * @param DynamicFilterData $dynamicFilterData
     * @return array
     * @throws \Exception
     */
    public function dynamicFilter(DynamicFilterData $dynamicFilterData): array
    {
        // 1. Check if the model is compatible with our advanced filtering
        if (!($this->model instanceof AutoFilterable)) {
            throw new \Exception('Model ' . get_class($this->model) . ' must implement the AutoFilterable interface to be used with this service.');
        }

        $extraOperation = $dynamicFilterData->extraOperation;
        $globaleFilterExtraOperation = $dynamicFilterData->globaleFilterExtraOperation;
        $beforeOperation = $dynamicFilterData->beforeOperation;

        // 2. Whitelisting Logic using the interface methods
        $allowedFilters = $this->model->getAllowedFilters();
        $allowedSorts = $this->model->getSortableColumns();



        $dynamicFilterData->filters = collect($dynamicFilterData->filters)
            ->filter(fn(ColumnFilterData $filter) => in_array(explode('.', $filter->id)[0], $allowedFilters))
            ->values();


        $dynamicFilterData->sorting = collect($dynamicFilterData->sorting)
            ->filter(fn(ColumnSortData $sort) => in_array(explode('.', $sort->id)[0], $allowedSorts))
            ->values();

        // Initial query object.
        $query = $this->model->query();
        $mainTable = $this->model->getTable();

        // 3. Automatic Translation Join Logic
        $translationTable = $this->model->getTranslationTable();
        if ($translationTable) {
            $this->model->applyTranslationJoin($query, $mainTable, $translationTable);
            $modelKeyName = $this->model->getModelKeyName();
            $query->select("{$mainTable}.*");
            $query->groupBy("{$mainTable}.{$modelKeyName}");
        }

        // This part is kept for backward compatibility and flexibility.
        $pFilterKeys = collect($dynamicFilterData->filters)->groupBy('id');


        $sortingKeys = collect($dynamicFilterData->sorting)->groupBy('id');
        if (isset($beforeOperation)) {
            $beforeOperation($query, ['filterKeys' => $pFilterKeys, 'sortingKeys' => $sortingKeys]);
        }


        // 4. [MODIFIED] Advanced vs. Simple Filter Logic
        // If an advanced filter is provided, it takes precedence.
        if (!empty($dynamicFilterData->advanceFilter)) {
            $query->where(function (Builder $builder) use ($dynamicFilterData, $allowedFilters) {
                self::applyAdvancedFilterGroup($builder, $dynamicFilterData->advanceFilter, $allowedFilters);
            });
        }
        // Otherwise, use the old simple filtering system for backward compatibility.
        else {
            self::handelFilter($query, $pFilterKeys);
        }

        // 5. Global filter logic is now smarter and more secure
        if (isset($dynamicFilterData->globalFilter) && !empty($dynamicFilterData->globalFilter)) {
            $query->where(function (Builder $builder) use ($dynamicFilterData, $globaleFilterExtraOperation, $pFilterKeys, $sortingKeys) {
                $globalFilterValue = '%' . $dynamicFilterData->globalFilter . '%';
                $mainTable = $this->model->getTable();
                $translationTable = $this->model->getTranslationTable();

                foreach ($this->model->getSearchableColumns() as $column) {
                    $builder->orWhere("{$mainTable}.{$column}", 'LIKE', $globalFilterValue);
                }
                if ($translationTable) {
                    foreach ($this->model->getSearchableTranslationColumns() as $column) {
                        $builder->orWhere("{$translationTable}.{$column}", 'LIKE', $globalFilterValue);
                    }
                }
                if (isset($globaleFilterExtraOperation)) {
                    $globaleFilterExtraOperation($builder, ['filterKeys' => $pFilterKeys, 'sortingKeys' => $sortingKeys, 'globalFilter' => $dynamicFilterData->globalFilter]);
                }
            });
        }

        if (isset($extraOperation)) {
            $extraOperation($query, ['filterKeys' => $pFilterKeys, 'sortingKeys' => $sortingKeys, 'globalFilter' => $dynamicFilterData->globalFilter]);
        }



        self::handelSorting($query, $sortingKeys);

        Log::info($query->toRawSql());

        $countQuery = clone $query;
        $countQuery->getQuery()->orders = null;
        $countQuery->getQuery()->columns = null;
        $countQuery->select(DB::raw('1'));
        $totalRecords = DB::connection($this->model->getConnectionName())->table(DB::raw("({$countQuery->toSql()}) as sub"))->mergeBindings($countQuery->getQuery())->count();
        $paginationData = $this->handelPageAndPerPage($dynamicFilterData->page, $dynamicFilterData->perPage, $totalRecords);
        $finalResult = $this->handelResultFormate($dynamicFilterData->paginationFormate, $paginationData['page'], $paginationData['perPage'], $query);
        return $finalResult;
    }

    /**
     * [NEW] Recursively applies a group of advanced filter rules to the query.
     */
    private static function applyAdvancedFilterGroup(Builder $query, object $filterGroup, array $allowedFilters): void
    {
        $condition = strtoupper($filterGroup->condition ?? 'AND') === 'OR' ? 'orWhere' : 'where';

        foreach ($filterGroup->rules ?? [] as $rule) {
            if (isset($rule->condition)) {
                $query->{$condition}(function (Builder $subQuery) use ($rule, $allowedFilters) {
                    self::applyAdvancedFilterGroup($subQuery, $rule, $allowedFilters);
                });
            } elseif (isset($rule->id)) {
                if (!in_array($rule->id, $allowedFilters)) {
                    continue;
                }
                $whereClause = $condition === 'orWhere' ? 'OR' : 'AND';
                self::applySimpleFilterRule($query, $rule, $whereClause);
            }
        }
    }

    /**
     * [NEW] Helper method to apply a single, simple filter rule from an advanced filter group.
     */
    private static function applySimpleFilterRule(Builder $query, object $rule, string $conditionType): void
    {
        /** @var Model|AutoFilterable $model */
        $model = $query->getModel();
        $columnId = $rule->id;

        if (in_array($columnId, $model->getVirtualColumns())) {
            return; // If so, do nothing.
        }

        $translationColumns = $model->getSearchableTranslationColumns();
        $qualifiedColumnId = in_array($columnId, $translationColumns)
            ? $model->getTranslationTable() . '.' . $columnId
            : $model->getTable() . '.' . $columnId;

        $filterData = new ColumnFilterData(
            id: $qualifiedColumnId,
            value: $rule->value,
            filterFns: FilterFnsEnum::from($rule->filterFns),
        );

        $filterData->buildQueryWhereStatment($query, $filterData, null, false, $conditionType);
    }

    public static function handelPageAndPerPage($page, $perPage, $totalCount)
    {
        $result['page'] = $page;
        $result['perPage'] = $perPage;
        if ($perPage == 'all' || $page == 'all') {
            $result['perPage'] = $totalCount > 0 ? $totalCount : 1;
            $result['page'] = 1;
        }
        return $result;
    }

    /**
     * Applies a collection of simple filters (for backward compatibility).
     */
    public static function handelFilter(&$query, $filterKeys, $columnPrefix = null)
    {

        $filterKeys->map(function ($filterValueObject, $columnId) use (&$query, $columnPrefix) {

            foreach ($filterValueObject as $singleFilterValue) {
                self::handelFilterOne($query, $singleFilterValue, $columnId, $columnPrefix);
            }
            /* The code snippet provided is a comment block in PHP. It appears to be documenting a
            method call `self::handelFilterOne()` with parameters ``, ``,
            ``, and ``. However, the method call itself is commented out with
            ` */
            // self::handelFilterOne($query, $filterValueObject, $columnId, $columnPrefix);
        });
    }

    /**
     * Applies a single simple filter.
     */
    public static function handelFilterOne(&$query, $filterValueObject, $columnId, $columnPrefix = null)
    {

        $model = $query->getModel();


        if (in_array($columnId, $model->getVirtualColumns())) {
            return; // If so, do nothing and exit the function.
        }

        // $filterValueObject = $filterValueObject[0];

        $qualifiedColumnId = in_array($columnId, $model->getSearchableTranslationColumns())
            ? $model->getTranslationTable() . '.' . $columnId
            : $model->getTable() . '.' . $columnId;

        $filterData = new ColumnFilterData(
            id: $qualifiedColumnId,
            value: $filterValueObject->value,
            filterFns: is_string($filterValueObject->filterFns)
                ? FilterFnsEnum::from($filterValueObject->filterFns)
                : $filterValueObject->filterFns,
        );

        $filterData->buildQuery($query);
    }

    // public static function handelSorting(&$query, $sortedColumns)
    // {

    //     /** @var Model|AutoFilterable $model */
    //     $model = $query->getModel();
    //     $translationTable = $model->getTranslationTable();

    //     foreach ($sortedColumns as $column) {
    //         $sortingValue = $column[0];

    //         $isTranslationColumn = $translationTable &&
    //             in_array($sortingValue->id, $model->getSearchableTranslationColumns());

    //         $qualifiedColumnId = $isTranslationColumn
    //             ? $translationTable . '.' . $sortingValue->id
    //             : $model->getTable() . '.' . $sortingValue->id;

    //         $sortData = new ColumnSortData(
    //             id: $qualifiedColumnId,
    //             desc: $sortingValue->desc,
    //         );

    //         $sortData->buildQuery($query);
    //     }
    //     // foreach ($sortedColumns as $column) {
    //     //     $sortingValue = $column[0];
    //     //     $sortingValue->buildQuery($query);
    //     // }
    // }

    public static function handelSorting(&$query, $sortedColumns)
    {
        /** @var Model|AutoFilterable $model */
        $model = $query->getModel();
        $translationTable = $model->getTranslationTable();

        foreach ($sortedColumns as $column) {
            $sortingValue = $column[0];

            // التحقق مما إذا كان العمود من جدول الترجمة
            $isTranslationColumn = $translationTable &&
                in_array($sortingValue->id, $model->getSearchableTranslationColumns());

            // بناء المعرف المؤهل للعمود
            $qualifiedColumnId = $isTranslationColumn
                ? $translationTable . '.' . $sortingValue->id
                : $model->getTable() . '.' . $sortingValue->id;

            // إنشاء نسخة معدلة من بيانات الترتيب
            $sortData = new ColumnSortData(
                id: $qualifiedColumnId,
                desc: $sortingValue->desc,
            );

            $sortData->buildQuery($query);
        }
    }

    public static function handelResultFormate(
        PaginationFormateEnum $paginationFormate,
        $page,
        $perPage,
        Builder|\Illuminate\Database\Query\Builder &$query
    ): array {
        $finalResult = [
            'data' => null,
            'pagination' => null
        ];
        switch ($paginationFormate) {
            case PaginationFormateEnum::normal:
                $finalResult = [
                    'data' => $query->paginate(perPage: $perPage, page: $page),
                    'pagination' => null
                ];
                break;
            case PaginationFormateEnum::separated:
                $result = $query->paginate(perPage: $perPage, page: $page);
                $finalResult = self::separatedPaginate($result);
                break;
            case PaginationFormateEnum::none:
            default:
                $finalResult = [
                    'data' => $query->get(),
                    'pagination' => null
                ];
                break;
        }
        return $finalResult;
    }

    public static function separatedPaginate($paginate)
    {
        $result = collect($paginate)->toArray();
        $data = $result['data'];
        unset($result['data']);
        return [
            'data' => $data,
            'pagination' => $result
        ];
    }

    public function count(): int
    {
        return $this->model->query()->select([$this->model->getKeyName()])->count();
    }


    public static function getFiltersValuesFromRequest($request)
    {
        $filters = collect([]);
        $decodedFilters = json_decode(base64_decode($request->input('filters'))) ?? [];
        foreach (collect($decodedFilters) as $value) {
            $arrayValue = (array)$value;
            if (isset($value->id, $value->value, $value->filterFns)) {
                $filters->push(new ColumnFilterData(
                    id: $value->id,
                    value: $value->value,
                    filterFns: FilterFnsEnum::from($value->filterFns),
                ));
            }
        }
        return $filters;
    }

    public static function getSortingValuesFromRequest($request)
    {
        $sorting = collect([]);
        $decodedSorting = json_decode(base64_decode($request->input('sorting'))) ?? [];
        foreach (collect($decodedSorting) as $value) {
            if (isset($value->id, $value->desc)) {
                $sorting->push(new ColumnSortData(
                    id: $value->id,
                    desc: $value->desc,
                ));
            }
        }
        return $sorting;
    }

    /**
     * Decodes and retrieves the advanced filter object from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return object|null
     */
    public static function getAdvanceFilterFromRequest($request): ?object
    {
        $advanceFilterInput = $request->input('advanceFilter');

        if (!$advanceFilterInput) {
            return null;
        }

        return json_decode(base64_decode($advanceFilterInput));
    }


    public static function dynamicSearchFromRequest(
        callable $getFunction,
        $page = null,
        $perPage = null,
        $paginationFormate = null,
        $filters = null,
        $sorting = null,
        $globalFilter = null,
        $advanceFilter = null,
        $globaleFilterExtraOperation = null,
        $extraOperation = null,
        $beforeOperation = null
    ) {
        $request = request();

        // Get values from request if not passed directly
        $finalPage = $page ?? $request->input('page');
        $finalPerPage = $perPage ?? $request->input('perPage');

        // Determine the pagination format
        $finalPaginationFormate = is_null($paginationFormate)
            ? PaginationFormateEnum::from($request->input('paginationFormate', PaginationFormateEnum::separated->value))
            : $paginationFormate;

        // Check if pagination should be disabled
        if (is_null($finalPage) || is_null($finalPerPage) || $finalPerPage === 'all' || $request->header('pdt') === '0') {
            $finalPaginationFormate = PaginationFormateEnum::none;
            $finalPage = 'all';
            $finalPerPage = 'all';
        }

        $result = $getFunction(
            new DynamicFilterData(
                page: $finalPage,
                perPage: $finalPerPage,
                paginationFormate: $finalPaginationFormate,
                filters: $filters ?? self::getFiltersValuesFromRequest($request),
                advanceFilter: $advanceFilter ?? self::getAdvanceFilterFromRequest($request),
                sorting: $sorting ?? self::getSortingValuesFromRequest($request),
                globalFilter: $globalFilter ?? $request->input('globalFilter'),
                globaleFilterExtraOperation: $globaleFilterExtraOperation,
                extraOperation: $extraOperation,
                beforeOperation: $beforeOperation
            )
        );
        return $result;
    }
}
