<?php

use App\Models\BusinessSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


if (!function_exists('getFillableList')) {
    /**
     * get fillable properties from model.
     * @param Model $model
     */
    function getFillableList(Model $model)
    {
        return $model->getFillable();
    }
}

if (!function_exists('getColumnListing')) {
    /**
     * get fillable properties from model.
     * @param Model $model
     */
    function getColumnListing(Model $model)
    {
        return Schema::getColumnListing($model->getTable());
    }
}


if (!function_exists('arrayOfObjectFirst')) {
    /**
     * Return a filter object from array of objects by key.
     */
    function arrayOfObjectFirst($filtersArray, $objectKey, $arrayKey = "id")
    {
        return $filtersArray->first(function ($filter) use ($objectKey, $arrayKey) {
            if (gettype($filter) == "object") {
                return $filter->{$arrayKey} == $objectKey;
            } else {
                return $filter[$arrayKey] == $objectKey;
            }
        });
    }
}

if (!function_exists('newUUID')) {
    /**
     * generate uuid.
     */
    function newUUID()
    {
        return Str::uuid()->toString();
    }
}

if (!function_exists('getFillableColumns')) {
    /**
     * get can fillable columns from model.
     * @param Model $model
     * @return array<string>
     */
    function getFillableColumns(Model $model)
    {
        collect(getColumnListing($model))->filter(function ($column) use ($model) {
            return  $model->isFillable($column);
        });
    }
}


if (!function_exists('getColumnListing')) {
    /**
     * get fillable properties from model.
     * @param Model $model
     */
    function getColumnListing(Model $model)
    {
        return Schema::getColumnListing($model->getTable());
    }
}


if (!function_exists('hasColumn')) {
    /**
     * get fillable properties from model.
     * @param Model $model
     */
    function hasColumn(Model $model, $columnName): bool
    {
        return Schema::hasColumn($model->getTable(), $columnName);
    }
}

if (!function_exists('getSlug')) {
    /**
     * get Slug From Text.
     * @param Model $model
     */
    function getSlug(
        $title,
        $separator = '-',
        $language = 'en',
        $dictionary = ['@' => 'at']
    ): string {
        return Str::slug(title: $title, separator: $separator, language: $language, dictionary: $dictionary);
    }
}


if (!function_exists('get_settings')) {
    function get_settings($object, $type)
    {
        $config = null;
        foreach ($object as $setting) {
            if ($setting['type'] == $type) {
                $config = $setting;
            }
        }
        return $config;
    }
}

if (!function_exists('getSettingsByName')) {
    function getSettingsByName($type)
    {
        return BusinessSetting::where('type', $type)->first()?->value;
    }
}

if (!function_exists('myPaginate')) {

    function myPaginate(
        $items,
        $perPage = DEFAULT_DATA_LIMIT,
        $page = null,
        $baseUrl = null,
        $options = []
    ) {
        $items = $items instanceof Collection ?
            $items : Collection::make($items);

        if ($perPage == 'all' || $page == 'all') {
            $perPage =  $items->count();
            $page = 1;
        }

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $lap = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            max($perPage, 1),
            $page,
            $options
        );

        if ($baseUrl) {
            $lap->setPath($baseUrl);
        }

        return $lap;
    }
}

if (!function_exists('myStripTags')) {
    function myStripTags($html)
    {
        return  strip_tags(str_replace('&nbsp;', ' ', $html));
    }
}
