<?php

namespace App\Data;

use App\Enums\FilterFnsEnum;
use Spatie\LaravelData\Data;
use Illuminate\Database\Eloquent\Builder;

class ColumnFilterData extends Data
{
    /**
     * @param string $id column name.
     * @param string|array $value filter value.
     * @param FilterFnsEnum filter function.
     * @param string|null $columnPrefix column prefix.
     * @param bool $autoAddPrefixFromCurrentModel auto Add Prefix From Current Model.
     */
    public function __construct(
        public string |null        $id,
        public string|array|null   $value,
        public FilterFnsEnum  $filterFns,
        public string |null        $columnPrefix = null,
        public bool        $autoAddPrefixFromCurrentModel = false,
    ) {}

    function buildQueryWhereStatment(
        Builder &$queryBuilder,
        ColumnFilterData $columnFilterData,
        string|null $columnPrefix = null,
        // The default value of this parameter is now less important
        // because we will pass `false` from the service.
        bool $autoAddPrefixFromCurrentModel = false,
        string $conditionType  = 'AND',
    ): Builder {
        $columnName = $columnFilterData->id;
        $value = $columnFilterData->value;


        $whereMethod = $conditionType === 'OR' ? 'orWhere' : 'where';


        if ($autoAddPrefixFromCurrentModel && is_null($columnPrefix)) {
            $model = $queryBuilder->getModel();
            $tableName = $model->getTable();
            $columnName = $tableName . '.' .  $columnName;
        }
        if (isset($columnPrefix)) {
            $columnName = $columnPrefix .  $columnName;
        }

        switch ($columnFilterData->filterFns) {
            case FilterFnsEnum::dayEquals:
                $value = \Carbon\Carbon::parse($value)->format('Y-m-d');
                $queryBuilder->{$whereMethod . 'Date'}($columnName, '=', $value);
                break;
            case FilterFnsEnum::equals:
                $queryBuilder->{$whereMethod}($columnName, '=', $value);
                break;
            case FilterFnsEnum::notEquals:
                $queryBuilder->{$whereMethod}($columnName, 'NOT LIKE', $value);
                break;
            case FilterFnsEnum::greaterThan:
                $queryBuilder->{$whereMethod}($columnName, '>', $value);
                break;
            case FilterFnsEnum::greaterThanOrEqualTo:
                $queryBuilder->{$whereMethod}($columnName, '>=', $value);
                break;
            case FilterFnsEnum::lessThan:
                $queryBuilder->{$whereMethod}($columnName, '<', $value);
                break;
            case FilterFnsEnum::lessThanOrEqualTo:
                $queryBuilder->{$whereMethod}($columnName, '<=', $value);
                break;
            case FilterFnsEnum::arrIncludes:
            case FilterFnsEnum::arrIncludesAll:
            case FilterFnsEnum::arrIncludesSome:
            case FilterFnsEnum::in:
                $queryBuilder->{$whereMethod . 'In'}($columnName, (array) $value);
                break;
            case FilterFnsEnum::notIn:
                $queryBuilder->{$whereMethod . 'NotIn'}($columnName, (array) $value);
                break;
            case FilterFnsEnum::between:
                if (is_array($value) && count($value) === 2) {
                    [$from, $to] = $value;
                    $queryBuilder->when(isset($from) && !empty($from), function ($builder) use ($columnName, $from,  $whereMethod) {
                        $builder->{$whereMethod}($columnName, '>', $from);
                    });
                    $queryBuilder->when(isset($to) && !empty($to), function ($builder) use ($columnName, $to,  $whereMethod) {
                        $builder->{$whereMethod}($columnName, '<', $to);
                    });
                }
                break;
            case FilterFnsEnum::betweenInclusive:
            case FilterFnsEnum::inNumberRange:
                if (is_array($value) && count($value) === 2) {
                    [$from, $to] = $value;
                    $queryBuilder->when(isset($from) && !empty($from), function ($builder) use ($columnName, $from,  $whereMethod) {
                        $builder->{$whereMethod}($columnName, '>=', $from);
                    });
                    $queryBuilder->when(isset($to) && !empty($to), function ($builder) use ($columnName, $to,  $whereMethod) {
                        $builder->{$whereMethod}($columnName, '<=', $to);
                    });
                }
                break;
            case FilterFnsEnum::contains:
            case FilterFnsEnum::fuzzy:
            case FilterFnsEnum::includesString:
                $queryBuilder->{$whereMethod}($columnName, 'LIKE', "%$value%");
                break;
            case FilterFnsEnum::notContains:
                $queryBuilder->{$whereMethod}($columnName, 'NOT LIKE', "%$value%");
                break;
            case FilterFnsEnum::startsWith:
                $queryBuilder->{$whereMethod}($columnName, 'LIKE', "$value%");
                break;
            case FilterFnsEnum::notStartsWith:
                $queryBuilder->{$whereMethod}($columnName, 'NOT LIKE', "$value%");
                break;
            case FilterFnsEnum::endsWith:
                $queryBuilder->{$whereMethod}($columnName, 'LIKE', "%$value");
                break;
            case FilterFnsEnum::notEndsWith:
                $queryBuilder->{$whereMethod}($columnName, 'NOT LIKE', "%$value");
                break;
            case FilterFnsEnum::empty:
                $queryBuilder->{$whereMethod}($columnName, '=', '');
                break;
            case FilterFnsEnum::notEmpty:
                $queryBuilder->{$whereMethod}($columnName, '<>', '');
                break;
            case FilterFnsEnum::includesStringSensitive:
                $queryBuilder->{$whereMethod . 'Raw'}("UPPER($columnName) LIKE '%" . strtoupper($value) . "%'");
                break;
        }
        return $queryBuilder;
    }

    function buildQuery(Builder &$queryBuilder): Builder
    {
        $queryBuilder = $this->buildQueryWhereStatment(
            $queryBuilder,
            $this,
            $this->columnPrefix,
            $this->autoAddPrefixFromCurrentModel
        );
        return $queryBuilder;
    }

    function toArray(): array
    {
        return [
            "id" => $this->id,
            "value" => $this->value,
            "filterFns" => $this->filterFns->name,
        ];
    }
}
