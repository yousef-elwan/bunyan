<?php

namespace App\Repositories\Contracts;

use App\Data\DynamicFilterData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    /**
     * get data with dynamic options.
     *
     * @param DynamicFilterData $dynamicFilterData
     **/
    public function getList(DynamicFilterData $dynamicFilterData): array;

    /**
     * create model.
     *
     * @param array $data Data value
     * @return string|object
     */
    public function store(array $data): Model;

    /**
     * show model.
     *
     * @param Model $model
     * @return Model
     */
    public function show(Model $model): Model;

    /**
     * update model.
     *
     * @param Model $model
     * @param array $data Data value
     * @return bool
     */
    public function update(Model $model, array $data): Model;


    /**
     * delete model.
     *
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model): bool;

    /**
     * destroy model.
     *
     * @param Model $model
     * @return bool
     */
    public function destroy(Model $model): bool;
}
