<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Models\Property\PropertyReport;
use App\Repositories\Contracts\PropertyReportRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class PropertyReportRepository implements PropertyReportRepositoryInterface
{
    public function __construct(
        private readonly PropertyReport $model,
    ) {}

    /**
     * get data with dynamic options.
     *
     * @param DynamicFilterData $dynamicFilterData
     **/
    public function getList(DynamicFilterData $dynamicFilterData): array
    {
        return (new AutoFIlterAndSortService($this->model))->dynamicFilter($dynamicFilterData);
    }

    public function store(array $data): Model
    {
        $createData = collect($data);

        DB::beginTransaction();
        $createdModel = $this->model->create($createData->toArray());
        DB::commit();

        return $createdModel;
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function update(Model $model, array $data): Model
    {
        $updateData = Arr::except($data, []);

        DB::beginTransaction();
        $model->update($updateData);
        DB::commit();

        $model->refresh();
        return $model;
    }

    public function delete(Model $model): bool
    {
        $model->delete();
        return true;
    }

    public function destroy(Model $model): bool
    {
        $model->forceDelete();

        return true;
    }
}
