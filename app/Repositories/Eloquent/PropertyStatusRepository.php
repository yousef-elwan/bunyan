<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Models\PropertyStatus\PropertyStatus;
use App\Models\PropertyStatus\PropertyStatusTranslation;
use App\Repositories\Contracts\PropertyStatusRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class PropertyStatusRepository implements PropertyStatusRepositoryInterface
{
    public function __construct(
        private readonly PropertyStatus $model,
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
        $createData = collect($data)->except([
            'locales',
        ]);

        DB::beginTransaction();
        $createdModel = $this->model->create($createData->toArray());

        foreach (($data['locales'] ?? []) as $localeData) {
            $locale = $localeData['locale'];

            $translateRecord = Arr::except($localeData, []);
            $translateRecord['property_status_id'] = $createdModel->id;

            PropertyStatusTranslation::create($translateRecord);
        }

        DB::commit();
        return $createdModel;
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function update(Model $model, array $data): Model
    {
        $updateData = Arr::except($data, [
            'locales',
        ]);

        DB::beginTransaction();
        $model->update($updateData);

        foreach (($data['locales'] ?? []) as $localeData) {
            $locale = $localeData['locale'];

            $translateRecord = Arr::except($localeData, []);
            $translateRecord['property_status_id'] = $model->id;

            $oldTranslation = PropertyStatusTranslation::where('property_status_id', $model->id)->where('locale', $locale)->first();
            if ($oldTranslation) {
                PropertyStatusTranslation::where('id', $oldTranslation->id)->update($translateRecord);
            } else {
                PropertyStatusTranslation::create($translateRecord);
            }
        }

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
