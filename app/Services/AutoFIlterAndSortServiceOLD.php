<?php

namespace App\Services;

use App\Data\ColumnFilterData;
use App\Data\ColumnSortData;
use App\Data\DynamicFilterData;
use App\Enums\FilterFnsEnum;
use App\Enums\PaginationFormateEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AutoFIlterAndSortServiceOLD
{

    public function __construct(
        public Model $model
    ) {}

    /**
     * get data handler
     *
     * get data with dynamic options.
     *
     * @param DynamicFilterData $dynamicFilterData
     **/
    public function dynamicFilter(
        DynamicFilterData $dynamicFilterData,
    ) {

        
        $extraOperation = $dynamicFilterData->extraOperation;
        $globaleFilterExtraOperation = $dynamicFilterData->globaleFilterExtraOperation;
        $beforeOperation = $dynamicFilterData->beforeOperation;


        $pFilterKeys = collect($dynamicFilterData->filters)->groupBy('id');
        $filterKeys = collect([]);
        $sortingKeys = collect($dynamicFilterData->sorting)->groupBy('id');

        $modelColumns = getColumnListing(model: $this->model);

        // initial query object.
        $query =  $this->model->query();

        // get filtering Originls and Foreigns columns.


        $filterdOrginalColumns = array();
        $filterdForeignColumns = array();

        $pFilterKeys->each(function ($value, $key) use ($modelColumns, &$filterdOrginalColumns, &$filterdForeignColumns, &$filterKeys) {
            if (in_array($key, $modelColumns)) {
                array_push($filterdOrginalColumns, $value);
                $filterKeys->push($value[0]);
            } else if (in_array($key . "_cach", $modelColumns)) {
                $key = $key . "_cach";
                $valueValue = $value[0]?->value;
                $valuefilterFns = $value[0]?->filterFns;
                $value = new ColumnFilterData(
                    id: $key,
                    value: $valueValue,
                    filterFns: $valuefilterFns,
                );
                $filterKeys->push($value);
                $filterdOrginalColumns[$key] = $value;
            } else {
                info($value);
                $filterdForeignColumns[$key] = $value;
                // array_push($filterdForeignColumns, $value);
            }
        });

        $filterKeys = $filterKeys->groupBy('id');

        if (isset($beforeOperation)) {
            $beforeOperation(
                $query,
                [
                    'filterKeys' => $filterKeys,
                    'sortingKeys' => $sortingKeys,
                    'modelColumns' => $modelColumns,
                    'filterdOrginalColumns' => $filterdOrginalColumns,
                    'filterdForeignColumns' => $filterdForeignColumns,
                ]
            );
        }

        // filtering
        self::handelFilter($query, $filterKeys);

        // globale filter
        if (isset($dynamicFilterData->globalFilter) && !empty($dynamicFilterData->globalFilter)) {
            $query->where(function ($builder) use (
                $modelColumns,
                $filterKeys,
                $sortingKeys,
                $filterdOrginalColumns,
                $filterdForeignColumns,
                $dynamicFilterData,
                $globaleFilterExtraOperation,
            ) {
                $tableName = $this->model->getTable();
                foreach ($modelColumns as $modelColumn) {
                    $builder->orWhere($tableName . '.' . $modelColumn, 'LIKE',  '%' . $dynamicFilterData->globalFilter  . '%');
                }
                if (isset($globaleFilterExtraOperation)) {
                    $globaleFilterExtraOperation(
                        $builder,
                        [
                            'filterKeys' => $filterKeys,
                            'sortingKeys' => $sortingKeys,
                            'modelColumns' => $modelColumns,
                            'filterdOrginalColumns' => $filterdOrginalColumns,
                            'filterdForeignColumns' => $filterdForeignColumns,
                            'globalFilter' => $dynamicFilterData->globalFilter,
                        ]
                    );
                }
            });
            // $query->whereAny($modelColumns, 'LIKE',  '%' . $dynamicFilterData->globalFilter  . '%');
        }

        if (isset($extraOperation)) {
            $extraOperation(
                $query,
                [
                    'filterKeys' => $filterKeys,
                    'sortingKeys' => $sortingKeys,
                    'modelColumns' => $modelColumns,
                    'filterdOrginalColumns' => $filterdOrginalColumns,
                    'filterdForeignColumns' => $filterdForeignColumns,
                    'globalFilter' => $dynamicFilterData->globalFilter,
                ]
            );
        }

        // sorting
        self::handelSorting($query, $sortingKeys);

        // // Get the currently used access token
        // $accessToken = request()?->token;

        // // Access the token ID
        // $tokenId = $accessToken?->id;

        if (request()->header('debug') == "1") {
            echo $query->toRawSql();
            exit;
        }

        info($query->toRawSql());

        $countQuery = $query;
        $pageinataionData = $this->handelPageAndPerPage($dynamicFilterData->page, $dynamicFilterData->perPage, $countQuery->count());

        $finalResult = $this->handelResultFormate($dynamicFilterData->paginationFormate, $pageinataionData['page'], $pageinataionData['perPage'], $query);
        return  $finalResult;
    }

    public static function handelPageAndPerPage($page, $perPage, $totalCount)
    {
        $result['page'] = $page;
        $result['perPage'] = $perPage;
        if ($perPage == 'all' || $page == 'all') {
            $result['perPage'] =  $totalCount;
            $result['page'] = 1;
        }
        return $result;
    }

    public static function handelFilter(&$query, $filterKeys, $columnPrefix = null)
    {
        $filterKeys->map(function ($filterValueObject, $columnId) use (&$query, $columnPrefix) {

            self::handelFilterOne(
                query: $query,
                filterValueObject: $filterValueObject,
                columnId: $columnId,
                columnPrefix: $columnPrefix,
            );
        });
    }

    public static function handelFilterOne(&$query, $filterValueObject, $columnId, $columnPrefix = null)
    {
        $filterValueObject = $filterValueObject[0];
        $filterData = $filterValueObject;
        $filterValue = $filterValueObject->value;
        $filterFilterFns = $filterValueObject->filterFns;
        $columnPrefix = !is_null($columnPrefix) ? $columnPrefix : $filterValueObject->columnPrefix ?? null;
        $autoAddPrefixFromCurrentModel = $filterValueObject->autoAddPrefixFromCurrentModel ?? false;

        $filterData = new ColumnFilterData(
            id: (is_null($columnPrefix) ? "" : $columnPrefix) . $columnId,
            value: $filterValue,
            columnPrefix: $columnPrefix,
            autoAddPrefixFromCurrentModel: $autoAddPrefixFromCurrentModel,
            filterFns: (
                is_string($filterFilterFns) ?
                FilterFnsEnum::from($filterFilterFns) :
                $filterFilterFns
            )
        );
        $filterData->buildQuery($query);
    }

    public static function handelSorting(&$query, $sortedOrginalColumns)
    {

        foreach ($sortedOrginalColumns  as $orginalColumn) {
            $sortingValue = $orginalColumn[0];
            $sortingValue->buildQuery($query);
        }
    }

    public static function handelResultFormate(
        PaginationFormateEnum $paginationFormate,
        $page,
        $perPage,
        \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder &$query
    ) {
        $finalResult = [
            'data' =>  null,
            'pagination' => null
        ];
        switch ($paginationFormate) {
            case PaginationFormateEnum::normal:
                $finalResult = [
                    'data' =>  $query->paginate(perPage: $perPage, page: $page),
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
                    'data' =>  $query->get(),
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
        $pagination = $result;
        unset($pagination['data']);
        return [
            'data' => $data,
            'pagination' => $pagination
        ];
    }

    public function count(): int
    {
        return $this->model->query()->select([$this->model->getKeyName()])->count();
    }

    public static function getFiltersValuesFromRequest($request)
    {
        $filters = collect([]);
        foreach (collect(json_decode(base64_decode($request->input('filters'))) ?? []) as $key => $value) {
            $arrayValue = (array)$value;
            if (array_key_exists('value', $arrayValue)) {
                $filters->push(new ColumnFilterData(
                    id: $value->id,
                    value: $value->value,
                    filterFns: FilterFnsEnum::from($value->filterFns)
                ));
            }
        }
        return $filters;
    }

    public static function getSortingValuesFromRequest($request)
    {
        $sorting = collect([]);
        foreach (collect(json_decode(base64_decode($request->input('sorting'))) ?? []) as $key => $value) {
            $sorting->push(new ColumnSortData(
                id: $value->id,
                desc: $value->desc,
            ));
        };
        return $sorting;
    }

    public static function dynamicSearchFromRequest(
        // $repo,
        callable  $getFunction,
        $page = null,
        $perPage = null,
        $paginationFormate = null,
        $filters = null,
        $sorting = null,
        $globalFilter = null,
        $globaleFilterExtraOperation = null,
        $extraOperation = null,
        $beforeOperation  = null,
    ) {
        $request = request();
        $page = $page ?? $request->input('page', DEFAULT_PAGE);
        $perPage = $perPage ?? $request->input('perPage', null);
        $paginationFormate = is_null($paginationFormate) ?  PaginationFormateEnum::from($request->input('paginationFormate', PaginationFormateEnum::separated->value)) : $paginationFormate;
        switch ($request->header('pdt', '0')) {
            case '0':
                if (is_null($perPage)) {
                    $paginationFormate = PaginationFormateEnum::none;
                    $page = 'all';
                    $perPage = 'all';
                }
                break;
            case '1':
                $page = $page ?? is_null($page) ? DEFAULT_PAGE : $page;
                $perPage = $perPage ?? is_null($perPage) ? DEFAULT_DATA_LIMIT : $perPage;
                break;
            default:
                break;
        }
        // $result = $repo->getList(
        $result =  $getFunction(
            new DynamicFilterData(
                page: $page,
                perPage: $perPage,
                paginationFormate: $paginationFormate,
                filters: $filters ??  self::getFiltersValuesFromRequest($request),
                sorting: $sorting ?? self::getSortingValuesFromRequest($request),
                globalFilter: $globalFilter ?? $request->input('globalFilter'),
                globaleFilterExtraOperation: $globaleFilterExtraOperation ?? null,
                extraOperation: $extraOperation ?? null,
                beforeOperation: $beforeOperation  ?? null,
            )
        );
        return $result;
    }
}
