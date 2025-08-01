<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Models\Orientation\Orientation;
use App\Models\Orientation\OrientationTranslation;
use App\Repositories\Contracts\OrientationRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class OrientationRepository implements OrientationRepositoryInterface
{

    private Orientation $model;

    public function __construct(Orientation $model)
    {
        $this->model = $model;
    }

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

            $translateRecord['orientation_id'] = $createdModel->id;
            $translateRecords[] = $translateRecord;

            $locale = $localeData['locale'];
        }
        foreach ($translateRecords as $translateRecord) {
            OrientationTranslation::create($translateRecord);
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
        $effectedRows = $model->update($updateData);

        foreach (($data['locales'] ?? []) as $localeData) {
            $locale = $localeData['locale'];

            $translateRecord =  $localeData;
            $translateRecord['orientation_id'] = $model->id;
            $oldTranslation = OrientationTranslation::where('orientation_id', $model->id)->where('locale', $locale)->first();
            if ($oldTranslation) {
                OrientationTranslation::where('id', $oldTranslation->id)->update($translateRecord);
            } else {
                OrientationTranslation::create($translateRecord);
            }
        }

        DB::commit();
        $model->refresh();
        return  $model;
    }

    public function delete(Model $model): bool
    {
        $deleted = $model->delete();
        return $deleted;
    }

    public function destroy(Model $model): bool
    {
        $deleted = $model->forceDelete();
        return $deleted;
    }
}
