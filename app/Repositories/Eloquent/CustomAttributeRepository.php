<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Models\CustomAttribute\CustomAttribute;
use App\Models\CustomAttribute\CustomAttributeTranslation;
use App\Repositories\Contracts\CustomAttributeRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class CustomAttributeRepository implements CustomAttributeRepositoryInterface
{
    public function __construct(
        private readonly CustomAttribute $model,
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

        $translateRecords = [];

        for ($i = 0; $i < count(($data['locales'] ?? [])); $i++) {
            $localeData = $data['locales'][$i];
            $translateRecord =  $localeData;

            if (!isset($translateRecord['name'])) {
                continue;
            }

            $translateRecord['attribute_id'] = $createdModel->id;
            $translateRecords[] = $translateRecord;

            $locale = $localeData['locale'];
        }
        foreach ($translateRecords as $translateRecord) {
            CustomAttributeTranslation::create($translateRecord);
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

            $translateRecord =  Arr::except($localeData, []);
            $translateRecord['attribute_id'] = $model->id;

            $oldTranslation = CustomAttributeTranslation::where('attribute_id', $model->id)->where('locale', $locale)->first();
            if ($oldTranslation) {
                CustomAttributeTranslation::where('id', $oldTranslation->id)->update($translateRecord);
            } else {
                CustomAttributeTranslation::create($translateRecord);
            }
        }

        DB::commit();
        $model->refresh();
        return  $model;
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
